<?php



/**
 * @author Lionel Lionel Lecaque lionel.lecaque@tudor.lu
 *
 */
class ProcessExecutionFactory {

	public $name;
	public $comment;
	public $execution;
	public $variables = array();
	public $ownerUri;

	/**
	 * @return unknown_type
	 */
	public function create(){
		if (!isset($this->name) || !isset($this->execution)  ){
			trigger_error('Problem creating Process Execution, missiong parameter',E_USER_ERROR);
		}

		$processExecutionClass = new core_kernel_classes_Class(CLASS_PROCESS_EXECUTIONS, __METHOD__);
		$subjectResource = core_kernel_classes_ResourceFactory::create($processExecutionClass,$this->name,$this->comment);
	
		
		$statusProp = new core_kernel_classes_Property(STATUS,__METHOD__);
		$subjectResource->setPropertyValue($statusProp,PROPERTY_PINSTANCES_STATUS);

		$processExecutionOfProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_EXECUTIONOF,__METHOD__);
		$subjectResource->setPropertyValue($processExecutionOfProp,$this->execution);

		$returnValue = new ProcessExecution($subjectResource->uriResource,false);
		
		$processVars = $returnValue->getVariables();

		$processVars = Utils::processVarsToArray($processVars);

		$initialActivities = $returnValue->process->getRootActivities();

		
		
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		
		$tokens = array();
		foreach ($initialActivities as $activity)
		{
			
			//add token
			$pInstanceTokenProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_TOKEN,__METHOD__);
			$subjectResource->setPropertyValue($pInstanceTokenProp,$activity->uri);
		
			// Add in path
			$pInstanceProcessProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_PROCESSPATH,__METHOD__);
			$subjectResource->setPropertyValue($pInstanceProcessProp,$activity->uri);
			
			$token = $tokenService->create($activity->resource);
			$tokens[] = $token;
					
			// OnBefore initial activity.
			// If the initial Activity has inference rules on before... let's run them !
			
			$activity->feedFlow(0);
			if (count($activity->onBeforeInferenceRule))
			{
				foreach ($activity->onBeforeInferenceRule as $onbir)
				{
					$onbir->execute($processVars);
				}
			}
		}
		
		//foreach first tokens, assign the user input prop values:
		foreach($this->variables as $uri => $value) {
			// have to skip name because note work like other variable
			if($uri != RDFS_LABEL) {
				
				$property = new core_kernel_classes_Property($uri);
				// $returnValue->resource->setPropertyValue($property,$value);//old
				
				//assign property values to them:
				foreach($tokens as $token){
					$token->setPropertyValue($property,$value);
				}
			}
		}
		
		error_reporting(E_ALL);
		
		$tokenService->setCurrents($returnValue->resource, $tokens);
		
		// Feed newly created process.
		$returnValue->feed();
		
		
/*
		// If the inital activity is "hidden", let's run it.
		if (!empty($returnValue->currentActivity)){
			if ($returnValue->currentActivity[0]->isHidden)
			{
				$returnValue->performTransition();
			}
		}

*/

		return $returnValue;
	}
}
?>