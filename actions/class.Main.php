<?php
class wfEngine_actions_Main extends wfEngine_actions_WfModule
{

	/**
	 * 
	 * Main page of wfEngine containning 2 sections : 
	 *  - Processes Execution in progress or just started
	 *  - Processes Definition user may instanciate
	 * 
	 * @return void
	 */	
	public function index()
	{
		
		//init required services
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		
		//get current user:
		$currentUser = $userService->getCurrentUser();
                
		//init variable that save data to be used in the view
		$processViewData 	= array();
		$uiLanguages		= tao_helpers_I18n::getAvailableLangs();
		$this->setData('uiLanguages', $uiLanguages);
		
		$userViewData = UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData', $userViewData);
		
		//list of available process executions:
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$processExecutions = $processInstancesClass->getInstances();
		
		foreach ($processExecutions as $processExecution){
			
			if(!is_null($processExecution) && $processExecution instanceof core_kernel_classes_Resource){
				
				try{
					$processDefinition = $processExecutionService->getExecutionOf($processExecution);
				}catch(wfEngine_models_classes_ProcessExecutionException $e){
					$processDefinition = null;
					$processExecutionService->deleteProcessExecution($processExecution);
					continue;
				}
				$processStatus = $processExecutionService->getStatus($processExecution);
				if(is_null($processStatus) || !$processStatus instanceof core_kernel_classes_Resource){
					continue;
				}
					
				$currentActivities = array();
				// Bypass ACL Check if possible...
				if ($processStatus->uriResource == INSTANCE_PROCESSSTATUS_FINISHED) {
					$processViewData[] = array(
						'type' 			=> $processDefinition->getLabel(),
						'label' 		=> $processExecution->getLabel(),
						'uri' 			=> $processExecution->uriResource,
						'activities'	=> array(array('label' => '', 'uri' => '', 'may_participate' => false, 'finished' => true, 'allowed'=> true)),
						'status'		=> $processStatus
					);
					continue;

				}else{

					$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processExecution);
					
					foreach ($currentActivityExecutions as $uri => $currentActivityExecution){

						$isAllowed = $activityExecutionService->checkAcl($currentActivityExecution, $currentUser, $processExecution);
						
						$activityExecFinishedByUser = false;
						$assignedUser = $activityExecutionService->getActivityExecutionUser($currentActivityExecution);
						if(!is_null($assignedUser) && $assignedUser->uriResource == $currentUser->uriResource){
							$activityExecFinishedByUser = $activityExecutionService->isFinished($currentActivityExecution);
						}
						
						$currentActivity = $activityExecutionService->getExecutionOf($currentActivityExecution);
						
						$currentActivities[] = array(
							'label'				=> $currentActivity->getLabel(),
							'uri' 				=> $uri,
							'may_participate'	=> ($processStatus->uriResource != INSTANCE_PROCESSSTATUS_FINISHED && $isAllowed),
							'finished'			=> ($processStatus->uriResource == INSTANCE_PROCESSSTATUS_FINISHED),
							'allowed'			=> $isAllowed,
							'activityEnded'		=> $activityExecFinishedByUser
						);
					}

					$processViewData[] = array(
						'type' 			=> $processDefinition->getLabel(),
						'label' 		=> $processExecution->getLabel(),
						'uri' 			=> $processExecution->uriResource,
						'activities'	=> $currentActivities,
						'status'		=> $processStatus
					);
					
				}
			}
		}
		
		//list of available process definitions:
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$availableProcessDefinitions = $processDefinitionClass->getInstances();

		//filter process that can be initialized by the current user (2nd check...)
		$authorizedProcessDefinitions = array();
		foreach($availableProcessDefinitions as $processDefinition){
			if($processExecutionService->checkAcl($processDefinition, $currentUser)){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition', $authorizedProcessDefinitions);
		$this->setData('processViewData', $processViewData);
		$this->setView('main.tpl');
	}

}
?>