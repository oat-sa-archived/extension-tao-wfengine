<?php
class Main extends WfModule
{


	/**
	 * 
	 * @param string $caseId
	 * @param string $login
	 * @param string $pwd
	 * @return void
	 */
	public function index($caseId = null, $login = null, $pwd = null)
	{

		$wfEngine 			= $_SESSION["WfEngine"];
		
		$userViewData 		= UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData',$userViewData);
		
		//list of available process executions:
		$processes = $wfEngine->getProcessExecutions();
		
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$currentUser = $userService->getCurrentUser();
		
		//use of caseId?
		if ($caseId != null){
			foreach ($processes as $proc){

				$procVariables = Utils::processVarsToArray($proc->getVariables());
				$intervieweeInst = new core_kernel_classes_Resource($procVariables[VAR_INTERVIEWEE_URI],__METHOD__);
				$property = propertyExists(CASE_ID_CODE);
				
				if($property){
					$caseIdProp = new core_kernel_classes_Property($property,__METHOD__);
					$results = $intervieweeInst->getPropertyValuesCollection($caseIdProp);
					if (!$results->isEmpty()){
						foreach ($results->getIterator() as $result) {
							if($result instanceof core_kernel_classes_Literal && $result->literal == $caseId) {
								$processUri = urlencode($proc->uri);
								
								$activityUri = urlencode($proc->currentActivity[0]->activity->uri);//not even used??!
								
								$viewState = _url('index', 'ProcessBrowser', null, array('processUri' => $processUri));
								$this->redirect($viewState);
							}
						}
					}
				}
			}
		}

		$processViewData 	= array();
		$uiLanguages		= I18nUtil::getAvailableLanguages();
		$this->setData('uiLanguages',$uiLanguages);
		foreach ($processes as $proc){
	
			$type 	= $proc->process->label;
			$label 	= $proc->label;
			$uri 	= $proc->uri;
			$status = $proc->status;
			$persid	= "-";
						
			$activityIsInitialProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
	
			$currentActivities = array();

			foreach ($proc->currentActivity as $currentActivity)
			{
				$activity = $currentActivity;
				
//				if(!is_null($activity->resource->getOnePropertyValue($activityIsInitialProp))){
//					$activityExecutionService->initExecution($activity->resource, $currentUser);
//				}
				
				$isAllowed = $activityExecutionService->checkAcl($activity->resource, $currentUser, $proc->resource);
				$isFinished = false;
				$execution = $activityExecutionService->getExecution($activity->resource, $currentUser, $proc->resource);
				if(!is_null($execution)){
					$aExecution = new ActivityExecution($proc, $execution);
					$isFinished = $aExecution->isFinished();
				}

				$currentActivities[] = array(
					'label'				=> $currentActivity->label,
					'uri' 				=> $currentActivity->uri,
					'may_participate'	=> (!$proc->isFinished() && $isAllowed),
					'finished'			=> $proc->isFinished(),
					'allowed'			=> $isAllowed,
					'activityEnded'		=> $isFinished
				);
			}
			
			$processViewData[] = array(
				'type' 			=> $type,
		  	   	'label' 		=> $label,
			   	'uri' 			=> $uri,
				'persid'		=> $persid,
		   	  	'activities'	=> $currentActivities,
			   	'status'		=> $status
			);
	
		}
		$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
		
		//list of available process definitions:
		$availableProcessDefinitions = $processClass->getInstances();
		
		//filter process that can be initialized by the current user:
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$authorizedProcessDefinitions = array();
		foreach($availableProcessDefinitions as $processDefinition){
			if($processExecutionService->checkAcl($processDefinition, $currentUser)){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition',$authorizedProcessDefinitions);
		$this->setData('processViewData',$processViewData);
		$this->setView('main.tpl');
	
	}
	
	public function test(){
		
		if(!DEBUG_MODE){
			echo "debug mode deactiviated!";
			return;
		}
		
		if(isset($_SESSION['activityExecutionUri'])){
			$activityExe = new core_kernel_classes_Resource($_SESSION['activityExecutionUri']);
			$activity = $activityExe->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY));
			echo "Activity :" . $activity->getLabel() ."<br>";
		}
		print "<pre>";
		
		print_r(wfEngine_models_classes_VariableService::getAll());
		
		print "</pre>";
	}
}
?>