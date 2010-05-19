<?php
class Main extends WfModule
{


	/**
	 * @param $caseId
	 * @return unknown_type
	 */
	public function index($caseId = null, $login = null, $pwd = null)
	{

		$wfEngine 			= $_SESSION["WfEngine"];
		$userViewData 		= UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData',$userViewData);
		$processes 			= $wfEngine->getProcessExecutions();

		if ($caseId != null)
		{
			foreach ($processes as $proc)
			{

				$procVariables = Utils::processVarsToArray($proc->getVariables());
				$intervieweeInst = new core_kernel_classes_Resource($procVariables[VAR_INTERVIEWEE_URI],__METHOD__);
				$property = propertyExists(CASE_ID_CODE);
				if($property)
				{

					$caseIdProp = new core_kernel_classes_Property($property,__METHOD__);
					$results = $intervieweeInst->getPropertyValuesCollection($caseIdProp);
					if (!$results->isEmpty())
					{
						foreach ($results->getIterator() as $result) {
							if($result instanceof core_kernel_classes_Literal && $result->literal == $caseId) {
								$processUri = urlencode($proc->uri);
								$activityUri = urlencode($proc->currentActivity[0]->activity->uri);
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
		foreach ($processes as $proc)
		{
	
			$type 	= $proc->process->label;
			$label 	= $proc->label;
			$uri 	= $proc->uri;
			$status = $proc->status;
			$persid	= "-";
	
	
			$currentActivities = array();
	
			foreach ($proc->currentActivity as $currentActivity)
			{
				$activity = $currentActivity;
	
				//if (UsersHelper::mayAccessProcess($proc->process))
				if (true)
				{
					$currentActivities[] = array('label' 			=> $currentActivity->label,
														 'uri' 				=> $currentActivity->uri,
														 'may_participate'	=> !$proc->isFinished());
	
	
				}
				$this->setData('currentActivities',$currentActivities);
			}
	
			if (true)
			{
				$processViewData[] = array('type' 		=> $type,
											  	   'label' 		=> $label,
												   'uri' 		=> $uri,
													'persid'	=> $persid,
											   	   'activities' => $currentActivities,
												   'status'		=> $status);
	
	
			}
	
	
		}
		$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
	
		$availableProcessDefinition = $processClass->getInstances();
	
	
	
		$this->setData('availableProcessDefinition',$availableProcessDefinition);
		$this->setData('processViewData',$processViewData);
		$this->setView('main.tpl');
	
	}
}
?>