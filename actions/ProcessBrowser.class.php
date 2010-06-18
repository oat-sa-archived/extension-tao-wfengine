<?php
error_reporting(E_ALL);

class ProcessBrowser extends WfModule
{
	public function index($processUri, $activityUri='')
	{
		try{
		$_SESSION["processUri"] = $processUri;
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
			$_SESSION["processUri"] = null;
			$this->redirect(_url('index', 'Main'));
		}
		
		//initialise the activity execution and assign the tokens to the current user
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
	
		$processExecutionService->initCurrentExecution($process->resource, $activity->resource, $currentUser);
		
		$activityExecutionResource = $activityExecutionService->getExecution($activity->resource, $currentUser, $process->resource);
		$browserViewData['activityExecutionUri']= $activityExecutionResource->uriResource;
		$_SESSION['activityExecutionUri'] = $activityExecutionResource->uriResource;
		
		
		$this->setData('activity',$activity);
		
		$activityPerf 		= new Activity($activity->uri, false); // Performance WA
		$activityExecution 	= new ActivityExecution($process, $activityExecutionResource);//would need for activityexecution to get the current token and thus the value of the variables

		$browserViewData['activityContentLanguages'] = array();

		// If paused, resume it.
		if ($process->status == 'Paused'){
			$process->resume();
		}
		
		// Browser view main data.
		$browserViewData['isInteractiveService']	= false;

		$browserViewData['processLabel'] 			= $process->process->label;
		$browserViewData['processExecutionLabel']	= $process->label;
		$browserViewData['activityLabel'] 			= $activity->label;
		$browserViewData['isBackable']				= (FlowHelper::isProcessBackable($process));
		$browserViewData['uiLanguage']				= $GLOBALS['lang'];
		$browserViewData['contentlanguage']			= $_SESSION['taoqual.serviceContentLang'];
		$browserViewData['processUri']				= $processUri ;

		$browserViewData['uiLanguages']				= I18nUtil::getAvailableLanguages();
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

		if (!($qSortedActivities = common_Cache::getCache("aprocess_activities"))){

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
		}
		
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
					'token' => $tokenService->getCurrent($activityExecutionResource),
					'services' => $servicesResources,
					'variableStack' => wfEngine_models_classes_VariableService::getAll()
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
			$this->redirect(_url('index', 'processBrowser', null, array('processUri' => urlencode($processUri))));
		}
	}

	public function next($processUri, $activityExecutionUri, $ignoreConsistency = 'false')
	{
	
		$processUri 	= urldecode($processUri);
		$processExecution = new ProcessExecution($processUri);
	
		try
		{

			$processExecution->performTransition($activityExecutionUri,($ignoreConsistency == 'true') ? true : false);

			if ($processExecution->isFinished()){
				$this->redirect(_url('index', 'Main'));
			}
			elseif($processExecution->isPaused()){
				$this->pause($processUri);
			}
			else{
				$this->redirect(_url('index', 'processBrowser', null, array('processUri' => urlencode($processUri), 'activityUri'=>'my act uri')));
			}
		}
		catch (ConsistencyException $consistencyException)
		{
			// A consistency error occured when trying to go
			// forward in the process. Let's try to get useful
			// information from the exception.
		
			// We need to tell the "index" action of the "ProcessBrowser" controller
			// that a consistency exception occured. To do so, we will use the concept
			// of flash variable. This kind of variable survives during one and only one
			// HTTP request lifecycle. So that in the "index" action, the session variable
			// depicting the error will be systematically erased after each processing.
			//$_SESSION['taoqual.flashvar.consistency'] = $consistencyException;
			$consistency = ConsistencyHelper::BuildConsistencyStructure($consistencyException);
			$_SESSION['taoqual.flashvar.consistency'] = $consistency;
		
			$this->redirect(_url('index', 'processBrowser', null, array('processUri' => urlencode($processUri))));
		}
	}

	public function pause($processUri)
	{

		$processUri 	= urldecode($processUri);
		$processExecution = new ProcessExecution($processUri);

		$processExecution->pause();
		$_SESSION["processUri"]= null;
		$this->redirect(_url('index', 'Main'));
	}

	public function jumpBack($processUri, $activityUri, $testing="",$ignoreHidden=false)
	{

		$processUri = urldecode($processUri);
		$activityUri = urldecode($activityUri);

		$processExecution = new ProcessExecution($processUri);
		$newActivity = new Activity($activityUri);
		$processExecution->jumpBack(new Activity($activityUri), $testing);

		if ($ignoreHidden == true)
		{
			$newActivity->feedFlow(1);
			if ($newActivity->isHidden)
			{
				$this->next(urlencode($processUri));
				die();
			}
		}


		$this->redirect(_url('index', 'processBrowser', null, array('processUri' => urlencode($processUri))));
	}

	public function breakOff($processUri)
	{
		PiaacDataHolder::build($processUri);

		$processUri = urldecode($processUri);
		$process = new ProcessExecution($processUri);
		$activityUri = $process->currentActivity[0]->uri;

		//returns uri of activity to jump to if the user want to break off
		$endingActivityUri = getBreakOffEndingActivityUri($activityUri);

		$this->jumpBack($processUri, $endingActivityUri, '', true);
	}

	public function jumpLast($processUri)
	{
		PiaacDataHolder::build($processUri);

		$processUri = urldecode($processUri);
		$processExecution = new ProcessExecution($processUri);

		try
		{
			$processExecution->performTransitionToLast();
			$this->index($processUri);
		}
		catch (ConsistencyException $e)
		{
			$consistency = ConsistencyHelper::BuildConsistencyStructure($e);
			$_SESSION['taoqual.flashvar.consistency'] = $consistency;

			$this->redirect(_url('index', 'processBrowser', null, array('processUri' => urlencode($processUri))));
		}
	}
}
?>
