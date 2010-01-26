<?php
class Main extends Module
{


	/**
	 * @param $caseId
	 * @return unknown_type
	 */
	public function index($caseId = null, $login = null, $pwd = null)
	{
		if (!SERVICE_MODE)
		{
			if ($login != null && $pwd != null)
			{
				UsersHelper::authenticate($login, $pwd);
			}

//			UsersHelper::checkAuthentication();
			UsersHelper::authenticate('tao','tao');


			$wfEngine 			= $_SESSION["Wfengine"];
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
									$viewState = "processBrowser/index?processUri=${processUri}";
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
			$property = propertyExists(CASE_ID_CODE);
			$procVariables = Utils::processVarsToArray($proc->getVariables());
			$intervieweeInst = new core_kernel_classes_Resource($procVariables[VAR_INTERVIEWEE_URI],__METHOD__);



			if($property)
			{
				$caseIdProp = new core_kernel_classes_Property($property,__METHOD__);

				$results = $intervieweeInst->getPropertyValuesCollection($caseIdProp);

				foreach ($results->sequence as $result){
					if (isset($result->literal)){
						$persid	= $result->literal;

					}
				}
			}

			/** In case of ---  if we want to embed svg into the html page
				* $proc->editMode=false;
				* $processStatus = Utils::renderSvg($uri,$proc->drawSvg());;
				*/
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

		$this->setData('processViewData',$processViewData);
		$this->setView('main.tpl');
	}
	else
	{
		// This view cannot be seen by simple users when TAOQual is running
		// in 'service mode'.
		UsersHelper::informServiceMode();
	}
}
}
?>