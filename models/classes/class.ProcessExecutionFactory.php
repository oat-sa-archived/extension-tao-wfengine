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
		var_dump($this);
		if (!isset($this->name) || !isset($this->execution) || !isset($this->intervieweeUri)){
			trigger_error('Problem creating Process Execution, missiong parameter',E_USER_ERROR);
		}

		$processExecutionClass = new core_kernel_classes_Class(CLASS_PROCESS_EXECUTIONS, __METHOD__);
		$subjectResource = core_kernel_classes_ResourceFactory::create($processExecutionClass,$this->name,$this->comment);

//		$property = propertyExists(CASE_ID_CODE);
//		if($property)
//		{
//			$caseIdProp = new core_kernel_classes_Property($property,__METHOD__);
//			$subjectResource->setPropertyValue($caseIdProp,$this->name);
//		}

		$statusProp = new core_kernel_classes_Property(STATUS,__METHOD__);
		$subjectResource->setPropertyValue($statusProp,PROPERTY_PINSTANCES_STATUS);

		$processExecutionOfProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_EXECUTIONOF,__METHOD__);
		$subjectResource->setPropertyValue($processExecutionOfProp,$this->execution);

	/*	if (defined('PIAAC_ENABLED'))
		{
					$processActionCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCE_ACTIONCODE,__METHOD__);
		$subjectResource->setPropertyValue($processActionCodeProp,RESOURCE_ACTIONCODE_PROCEED_INTERVIEW);
		
			PiaacDataHolder::build($subjectResource->uriResource);
			
			// Bind the interviewee to the process.
			if ($this->intervieweeUri != null) {

				$processIntervieweeProperty = new core_kernel_classes_Property(VAR_INTERVIEWEE_URI);
				$subjectResource->editPropertyValues($processIntervieweeProperty,$this->intervieweeUri);
				$intervieweeResource = new core_kernel_classes_Resource($this->intervieweeUri);
			}
			else {
				throw new common_Exception('Interviewee not defined in factory could not create Process');
			}
			
			// Bind the owner to the process.
			if ($this->ownerUri != null)
			{
				$processAutoBindProperty = new core_kernel_classes_Property(PROPERTY_PROCESSEXECUTION_AUTO_BIND);
				$subjectResource->editPropertyValues($processAutoBindProperty, $this->ownerUri);
			}

			// Okay we can begin to "infer" some useful variables for the PIAAC case.
			// useful variables...
			$currentMonth = intval(date('n'));
			$currentYear = intval(date('Y'));
			$lastYear = $currentYear - 1;
			$randomValue = rand(1,4);

			// Additional random values (RANDOM_2, RANDOM_3, ...).
			for($i = 2; $i <= 10; $i++) {
      			createLiteralEffectiveVariableFor($this->intervieweeUri, 'RANDOM_'.$i,  rand(0,$i-1));
			}

			// Month of the interview.
			createLiteralEffectiveVariableFor($this->intervieweeUri, 'A_D01a1', $currentMonth);
			createLiteralEffectiveVariableFor($this->intervieweeUri, 'A_D01a2', $lastYear);
			createLiteralEffectiveVariableFor($this->intervieweeUri, 'A_D01a3', $currentYear);
			createLiteralEffectiveVariableFor($this->intervieweeUri, 'RANDOM',  $randomValue);
			
			// Additional Variables
			createLiteralEffectiveVariableFor($this->intervieweeUri, 'COUNTRYCODE', PIAAC_VERSION);
			createLiteralEffectiveVariableFor($this->intervieweeUri, 'PROCESSURI', urlencode($subjectResource->uriResource));
			
			$BQLANGValue = null;
			// Language importation management for the BQ.
			if (($BQLANGValue = getVariableValue($intervieweeResource, 'BQLANG')) != null)
			{
				changeContentLanguage($BQLANGValue);
			}
			else
			{
				changeContentLanguageToDefault();
			}
		}

*/
		$returnValue = new ProcessExecution($subjectResource->uriResource);

		$processVars = $returnValue->getVariables();
		$processVars = Utils::processVarsToArray($processVars);
		$initialActivities = $returnValue->process->getRootActivities();

		foreach ($initialActivities as $key=>$activity)
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

		// If the inital activity is "hidden", let's run it.
		if (!empty($returnValue->currentActivity)){
			if ($returnValue->currentActivity[0]->isHidden)
			{
				$returnValue->performTransition();
			}
		}

		// We log the "NEW CASE" event in the log file.
		// We log the "MOVE_FORWARD" event in the log file.
		if (defined('PIAAC_ENABLED'))
		{
			$event = new PiaacBusinessEvent('BQ_ENGINE', 'INTERVIEW_START',
											'The interview is created',
											getIntervieweeUriByProcessExecutionUri($subjectResource->uriResource),
											null);
												  
			PiaacEventLogger::getInstance()->trigEvent($event);
		}

		return $returnValue;
	}
}
?>