<?php
class wfEngine_actions_ProcessInstanciation extends wfEngine_actions_WfModule{
	
	
	public function authoring($processDefinitionUri){
		
		// This action is not available when running
		// the service mode !

		$processDefinitionUri = urldecode($processDefinitionUri);

		$userViewData 		= UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData',$userViewData);

		$processAuthoringData 	= array();
		$processAuthoringData['processUri'] 	= $processDefinitionUri;
		$processAuthoringData['processLabel']	= "Process' variables initialization";
		$processAuthoringData['variables']		= array();

		// Process variables retrieving.
		$processDefinitionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessDefinitionService');
		$variables = $processDefinitionService->getProcessVars(new core_kernel_classes_Resource($processDefinitionUri));

		foreach ($variables as $key => $variable){

			$name 			= $variable['name'];
			$propertyKey	= $key;//urlencode?

			$processAuthoringData['variables'][] = array(
				'name'	=> $name,															
				'key'	=> $propertyKey
			);
		}

		$this->setData('processAuthoringData', $processAuthoringData);
		$this->setView('process_initialization.tpl');
	}
	
	public function initProcessExecution($posted){
		
		ini_set('max_execution_time', 200);
			
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		
		$processDefinitionUri = urldecode($posted['executionOf']);
		$processDefinition = new core_kernel_classes_Resource($processDefinitionUri);

		$processExecName = $posted["variables"][RDFS_LABEL];
		$processExecComment = 'Created in Processes server on ' . date(DATE_ISO8601);
		$processVariables = $posted["variables"];

		$newProcessExecution = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $processVariables);
		
		//create nonce to initial activity executions:
		foreach($processExecutionService->getCurrentActivityExecutions($newProcessExecution) as $initialActivityExecution){
			$activityExecutionService->createNonce($initialActivityExecution);
		}

		$param = array('processUri' => urlencode($newProcessExecution->uriResource));
		$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $param));

	}
}
?>