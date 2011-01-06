<?php
error_reporting(E_ALL);

class ProcessBrowser extends WfModule
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
		
		$process 			= new ProcessExecution($processUri);
		$currentActivity = null;
		if(!empty($activityUri)){
			//check that it is an uri of a valid activity definition (which is contained in currentActivity):
			foreach($process->currentActivity as $processCurrentActivity){
				if($processCurrentActivity->uri == $activityUri){
					$currentActivity = new Activity($activityUri);
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
				$this->redirect(_url('pause', 'processBrowser'));
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
			Session::removeAtrribute("processUri");
			$this->redirect(_url('index', 'Main'));
		}
	
		//initialise the activity execution and assign the tokens to the current user
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
	
		$processExecutionService->initCurrentExecution($process->resource, $activity->resource, $currentUser);
		
		$activityExecutionResource = $activityExecutionService->getExecution($activity->resource, $currentUser, $process->resource);
		$browserViewData['activityExecutionUri']= $activityExecutionResource->uriResource;
		Session::setAttribute('activityExecutionUri', $activityExecutionResource->uriResource);
		
		
		$this->setData('activity',$activity);
		
		$activityPerf 		= new Activity($activity->uri, false); // Performance WA
		$activityExecution 	= new ActivityExecution($process, $activityExecutionResource);//would need for activityexecution to get the current token and thus the value of the variables

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
		$browserViewData['uiLanguage']				= $GLOBALS['lang'];
		$browserViewData['contentlanguage']			= $_SESSION['taoqual.serviceContentLang'];
		$browserViewData['processUri']				= $processUri ;

		$browserViewData['uiLanguages']				 = I18nUtil::getAvailableLanguages();
		$browserViewData['activityContentLanguages'] = I18nUtil::getAvailableServiceContentLanguages();

		$browserViewData['showCalendar']			= $activityPerf->showCalendar;
		
		// process variables data.
		$variablesViewData = array();
		$variables = $process->getVariables();

		foreach ($variables as $var)
		{
			// $variablesViewData[$var->code] = array('uri' 	=> $var->uri,
												   // 'value' 	=> urlencode($var->value));
			$variablesViewData[$var->uri] = urlencode($var->value);	
		}
		
		$this->setData('variablesViewData',$variablesViewData);
		// consistency data.

		
		$consistencyViewData = array();
		if (isset($_SESSION['taoqual.flashvar.consistency'])){
			$consistencyException 		= $_SESSION['taoqual.flashvar.consistency'];
			$involvedActivities 		= $consistencyException['involvedActivities'];
			$consistencyViewData['isConsistent']		= false;
			$consistencyViewData['suppressable']		= $consistencyException['suppressable'];
			$consistencyViewData['notification']		= str_replace(array("\r", "\n"), '', $consistencyException['notification']);
			$consistencyViewData['processExecutionUri'] = urlencode($processUri);
			$consistencyViewData['activityUri']			= urlencode($activity->uri);
			$consistencyViewData['source']				= $consistencyException['source'];

			$consistencyViewData['involvedActivities']	= array();

			foreach ($involvedActivities as $involvedActivity)
			{
				$consistencyViewData['involvedActivities'][] = array('uri' => $involvedActivity['uri'],
																	 'label' => $involvedActivity['label'],
																	 'processUri' => $processUri);
			}
			
			// Clean flash variables.
			$_SESSION['taoqual.flashvar.consistency'] = null;
		}else{
			// Everything is allright with data consistency for this process.
			$consistencyViewData['isConsistent'] = true;

			$_SESSION['taoqual.flashvar.consistency'] = null;
		}
		
		$this->setData('consistencyViewData',$consistencyViewData);

		//The following takes about 0.2 seconds -->cache

		//retrieve activities

		/*if (!($qSortedActivities = common_Cache::getCache("aprocess_activities"))){

			$processDefinition = new core_kernel_classes_resource($process->process->uri);
			$activities = $processDefinition->getPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES));

			//sort the activities
			$qSortedActivities =array();
			foreach ($activities as $key=>$val)
			{
				$activity_res = new core_kernel_classes_resource($val);
				$label = $activity_res->label;
				$qSortedActivities[$label] = $val;

			}
			ksort($qSortedActivities);
			common_Cache::setCache($qSortedActivities,"aprocess_activities");
		}*/
		
		$browserViewData['annotationsResourcesJsArray'] = array();
		foreach ($qSortedActivities as $key=>$val){
			$browserViewData['annotationsResourcesJsArray'][]= array($val,$key);
		}

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
		$processExecution = new ProcessExecution($processUri);
		$activity = $processExecution->currentActivity[0];
		$processExecution->performBackwardTransition($activity);
		$processUri 	 = urlencode($processUri);

		if (!ENABLE_HTTP_REDIRECT_PROCESS_BROWSER)
		{
			$this->index($processUri);
		}
		else
		{
			$this->redirect(_url('index', get_class($this), null, array('processUri' => urlencode($processUri))));
		}
	}

	public function next($processUri, $activityExecutionUri, $ignoreConsistency = 'false')
	{
	
		$processUri 		= urldecode($processUri);
		$processExecution 	= new ProcessExecution($processUri);

		$processExecution->performTransition($activityExecutionUri,($ignoreConsistency == 'true') ? true : false);
		
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
			$this->redirect(_url('index', get_class($this), null, array('processUri' => urlencode($processUri), 'activityUri'=>urlencode($nextActivityDefinitionUri)) ));
		}

	}

	public function pause($processUri)
	{

		$processUri 	= urldecode($processUri);
		$processExecution = new ProcessExecution($processUri);

		$processExecution->pause();
		Session::removeAttribute("processUri");
		$this->redirect(_url('index', 'Main'));
	}


}
?>
