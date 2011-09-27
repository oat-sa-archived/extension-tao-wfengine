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
	public function index0()
	{
		
		$wfEngineService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_WfEngineService');
		
		$userViewData = UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData',$userViewData);
		
		//list of available process executions:
		$processes = $wfEngineService->getProcessExecutions();
		
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$currentUser = $userService->getCurrentUser();
		


		$processViewData 	= array();
		foreach ($processes as $proc){
	
			$type 	= $proc->process->resource->getLabel();
			$label 	= $proc->resource->getLabel();
			$uri 	= $proc->uri;
			$status = $proc->status;
			$persid	= "-";
						
			$activityIsInitialProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
	
			$currentActivities = array();

			foreach ($proc->currentActivity as $currentActivity)
			{
				$activity = $currentActivity;
				
				
				$isAllowed = $activityExecutionService->checkAcl($activity->resource, $currentUser, $proc->resource);
				$isFinished = false;
				$execution = $activityExecutionService->getExecution($activity->resource, $currentUser, $proc->resource);
				if(!is_null($execution)){
					$aExecution = new wfEngine_models_classes_ActivityExecution($proc, $execution);
					$isFinished = $aExecution->isFinished();
				}

				$currentActivities[] = array(
					'label'				=> $currentActivity->resource->getLabel(),
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
			$allowed = $processExecutionService->checkAcl($processDefinition, $currentUser);
			if($allowed){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition',$authorizedProcessDefinitions);
		$this->setData('processViewData',$processViewData);
		$this->setView('main.tpl');
	
	}
	
	public function index(){
		
		//init required services
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$wfEngineService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_WfEngineService');
		
		//get current user:
		$currentUser = $userService->getCurrentUser();
                
		//init variable that save data to be used in the view
		$processViewData 	= array();
		$uiLanguages		= tao_helpers_I18n::getAvailableLangs();
		$this->setData('uiLanguages', $uiLanguages);
		
		$userViewData = UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData',$userViewData);
		
		//list of available process executions:
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$processExecutions = $processInstancesClass->getInstances();
		
		foreach ($processExecutions as $processExecution){
			
			if(!is_null($processExecution) && $processExecution instanceof core_kernel_classes_Resource){
				
				$status = $processExecutionService->getStatus($processExecution);
				$processDefinition = $processExecutionService->getExecutionOf($processExecution);
				if(is_null($status) || !$status instanceof core_kernel_classes_Resource){
					continue;
				}
					
				$currentActivities = array();
				// Bypass ACL Check if possible...
				if ($status->uriResource == INSTANCE_PROCESSSTATUS_FINISHED) {
					$processViewData[] = array(
						'type' 			=> $processDefinition->getLabel(),
						'label' 		=> $processExecution->getLabel(),
						'uri' 			=> $processExecution->uriResource,
						'activities'	=> array(array('label' => '', 'uri' => '', 'may_participate' => false, 'finished' => true, 'allowed'=> true)),
						'status'		=> $status
					);
					continue;

				}else{

					$isAllowed = false;
					$userActivityExecutions = array();
					$availableCurrentActivities = $processExecutionService->getAvailableCurrentActivityDefinitions($processExecution, $currentUser);
					foreach ($availableCurrentActivities as $uri => $currentActivity){

						$isAllowed = $activityExecutionService->checkAcl($currentActivity, $currentUser, $processExecution);

						$userActivityExecution = null;
						$userActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processExecution, $currentActivity, $currentUser);
						if(count($userActivityExecutions) == 1){
							$userActivityExecution = array_pop($userActivityExecutions);
						}

						$currentActivities[] = array(
							'label'				=> $currentActivity->getLabel(),
							'uri' 				=> $uri,
							'may_participate'	=> ($status->uriResource != INSTANCE_PROCESSSTATUS_FINISHED && $isAllowed),
							'finished'			=> ($status->uriResource == INSTANCE_PROCESSSTATUS_FINISHED),
							'allowed'			=> $isAllowed,
							'activityEnded'		=> (!is_null($userActivityExecution))?$activityExecutionService->isFinished($userActivityExecution):false
						);
					}

					if(!$isAllowed){
//							continue;
					}

					$processViewData[] = array(
						'type' 			=> $processDefinition->getLabel(),
						'label' 		=> $processExecution->getLabel(),
						'uri' 			=> $processExecution->uriResource,
						'activities'	=> $currentActivities,
						'status'		=> $status
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