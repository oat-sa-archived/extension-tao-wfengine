<?php

/**
 * ProcessBrowser Controller provide actions that navigate along a process
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class wfEngine_actions_ProcessBrowser extends wfEngine_actions_WfModule
{
	public function index($processUri, $activityUri='')
	{

		try{
			
		Session::setAttribute("processUri", $processUri);
		$processUri 		= urldecode($processUri); // parameters clean-up.
		$this->setData('processUri',$processUri);
		
		$activityUri = urldecode($activityUri);
		
		$userViewData 		= UsersHelper::buildCurrentUserForView(); // user data for browser view.
		$this->setData('userViewData',$userViewData);
		$browserViewData 	= array(); // general data for browser view.
		
		$process 			= new wfEngine_models_classes_ProcessExecution($processUri);
		$currentActivity = null;
		if(!empty($activityUri)){
			//check that it is an uri of a valid activity definition (which is contained in currentActivity):
			foreach($process->currentActivity as $processCurrentActivity){
				if($processCurrentActivity->uri == $activityUri){
					$currentActivity = new wfEngine_models_classes_Activity($activityUri);
					break;
				}
			}
		}
		
		if(is_null($currentActivity)){
			//if the activity is still null check if there is a value in $process->currentActivity:
			if(empty($process->currentActivity)) {
				die('No current activity found in the process: ' . $processUri);
			}
			if(count($process->currentActivity) > 1) {
				// echo 'paused in process browser';exit;
				$this->redirect(_url('pause', 'ProcessBrowser'));
			}else{
				//use the first one:
				$currentActivity = $process->currentActivity[0];
			}
		}
		
		$activity 			= $currentActivity;
		
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$currentUser = $userService->getCurrentUser();
		if(is_null($currentUser)){
			throw new Exception("No current user found!");
		}
		
	
		//security check if the user is allowed to access this activity
		$activityExecutionService 	= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		if(!$activityExecutionService->checkAcl($activity->resource, $currentUser, $process->resource)){
			Session::removeAttribute("processUri");
			$this->redirect(tao_helpers_Uri::url('index', 'Main'));
		}
		
		//initialise the activity execution and assign the tokens to the current user
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
	
		$processExecutionService->initCurrentExecution($process->resource, $activity->resource, $currentUser);
		
		$activityExecutionResource = $activityExecutionService->getExecution($activity->resource, $currentUser, $process->resource);
		$browserViewData['activityExecutionUri']= $activityExecutionResource->uriResource;
		Session::setAttribute('activityExecutionUri', $activityExecutionResource->uriResource);
		
		
		$this->setData('activity',$activity);
		
		$activityPerf 		= new wfEngine_models_classes_Activity($activity->uri, false); // Performance WA
		$activityExecution 	= new wfEngine_models_classes_ActivityExecution($process, $activityExecutionResource);//would need for activityexecution to get the current token and thus the value of the variables

		$browserViewData['activityContentLanguages'] = array();

		// If paused, resume it.
		if ($process->status == 'Paused'){
			$process->resume();
		}
		
		$controls = $activity->getControls();
		$browserViewData['controls'] = array(
			'backward' 	=> (in_array(INSTANCE_CONTROL_BACKWARD, $controls)),
			'forward'	=> (in_array(INSTANCE_CONTROL_FORWARD, $controls))
		);
		
		
		// Browser view main data.
		$browserViewData['isInteractiveService']	= false;

		$browserViewData['processLabel'] 			= $process->process->label;
		$browserViewData['processExecutionLabel']	= $process->label;
		$browserViewData['activityLabel'] 			= $activity->label;
		$browserViewData['processUri']				= $processUri ;


		
		// process variables data.
		$variablesViewData = array();
		$variables = $process->getVariables();

		foreach ($variables as $var)
		{
			$variablesViewData[$var->uri] = urlencode($var->value);	
		}
		
		$this->setData('variablesViewData',$variablesViewData);
		

		$browserViewData['active_Resource']="'".$activity->uri."'" ;
		$browserViewData['isInteractiveService'] 	= true;
		
		
		$servicesViewData 	= array();
		
		$services = $activityExecution->getInteractiveServices();
		
		$this->setData('services',$services);

		$this->setData('browserViewData', $browserViewData);
		
		$this->setData('debugWidget', DEBUG_MODE);
		if(DEBUG_MODE){
			
			
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			
			$servicesResources = array();
			foreach($services as $service){
				$servicesResources[] = array(
					'resource' => $service->resource,
					'input'		=> $service->input,
					'output'	=> $service->output
				);
			}
			
			$this->setData('debugData', array(
					'Activity' => $activity->resource,
					'ActivityExecution' => $activityExecutionResource,
					'Token' => $tokenService->getCurrent($activityExecutionResource),
					'All tokens' => $tokenService->getCurrents($process->resource),
					'Current activities' => $tokenService->getCurrentActivities($process->resource),
					'Services' => $servicesResources,
					'VariableStack' => wfEngine_models_classes_VariableService::getAll()
			));
		}
		}
		catch(common_Exception $ce){
			print $ce;exit;
		}
		$this->setView('process_browser.tpl');
	}

	public function back($processUri)
	{
		$processUri 	= urldecode($processUri);
		$processExecution = new wfEngine_models_classes_ProcessExecution($processUri);
		$activity = $processExecution->currentActivity[0];
		$processExecution->performBackwardTransition($activity);
		$processUri 	 = urlencode($processUri);

		if (!ENABLE_HTTP_REDIRECT_PROCESS_BROWSER)
		{
			$this->index($processUri);
		}
		else
		{
			$this->redirect(_url('index', 'ProcessBrowser', null, array('processUri' => urlencode($processUri))));
		}
	}

	public function next($processUri, $activityExecutionUri, $ignoreConsistency = 'false')
	{
	
		$processUri 		= urldecode($processUri);
		$processExecution 	= new wfEngine_models_classes_ProcessExecution($processUri);

		$processExecution->performTransition($activityExecutionUri);
		
		if ($processExecution->isFinished()){
			$this->redirect(_url('index', 'Main'));
		}
		elseif($processExecution->isPaused()){
			$this->pause($processUri);
		}
		else{
			//perform transition returns a unique next activity, execute it straight away:
			$nextActivityDefinitionUri = '';
			if(count($processExecution->currentActivity) == 1){
				$nextActivityDefinitionUri = $processExecution->currentActivity[0]->resource->uriResource;
			}
			$this->redirect(_url('index', 'ProcessBrowser', null, array('processUri' => urlencode($processUri), 'activityUri'=>urlencode($nextActivityDefinitionUri)) ));
		}

	}

	public function pause($processUri)
	{

		$processUri 	= urldecode($processUri);
		$processExecution = new wfEngine_models_classes_ProcessExecution($processUri);

		$processExecution->pause();
		Session::removeAttribute("processUri");
		$this->redirect(_url('index', 'Main'));
	}


}
?>
