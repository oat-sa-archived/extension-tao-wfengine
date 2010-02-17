<?php



/**
 * @author Lionel Lionel Lecaque lionel.lecaque@tudor.lu
 *
 */
class ProcessExecutionFactory {

	public $name;
	public $comment;
	public $execution;
	public $intervieweeUri;
	public $ownerUri;

	/**
	 * @return unknown_type
	 */
	public function create(){
		if (!isset($this->name) || !isset($this->execution) ){
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
	


		foreach ($initialActivities as $activity)
		{
			//add token
			$pInstanceTokenProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_TOKEN,__METHOD__);
			$subjectResource->setPropertyValue($pInstanceTokenProp,$activity->uri);

			// Add in path
			$pInstanceProcessProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_PROCESSPATH,__METHOD__);
			$subjectResource->setPropertyValue($pInstanceProcessProp,$activity->uri);


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