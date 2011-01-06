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
			
		
			// Add in path
			$pInstanceProcessProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_PROCESSPATH,__METHOD__);
			$subjectResource->setPropertyValue($pInstanceProcessProp,$activity->uri);
			
			$token = $tokenService->create($activity->resource);
			$tokens[] = $token;
					
			$activity->feedFlow(0);
		}
		
		//foreach first tokens, assign the user input prop values:
		$codes[] = array();
		$processVariableCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		foreach($this->variables as $uri => $value) {
			// have to skip name because doesnt work like other variables
			if($uri != RDFS_LABEL) {
				
				$property = new core_kernel_classes_Property($uri);
				
				//assign property values to them:
				foreach($tokens as $token){
					$token->setPropertyValue($property,$value);
				}
				
				//prepare the array of codes to be inserted as the "variables" property of the current token
				$code = $property->getUniquePropertyValue($processVariableCodeProp);
				$codes[] = (string) $code;
				
			}
		}
		
		//set serialized codes array into variable property:
		$tokenVariableProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
		foreach($tokens as $token){
			$token->setPropertyValue($tokenVariableProp, serialize($codes)); 
		}
		
		
		$tokenService->setCurrents($returnValue->resource, $tokens);
		
		// Feed newly created process.
		$returnValue->feed();
		
		return $returnValue;
	}
}
?>