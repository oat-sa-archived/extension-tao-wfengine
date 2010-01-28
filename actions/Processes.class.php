<?php
class Processes extends Module
{
	public function authoring($processDefinitionUri)
	{
		// This action is not available when running
		// the service mode !

			$processDefinitionUri = urldecode($processDefinitionUri);
			UsersHelper::checkAuthentication();

				
			$wfEngine 			= $_SESSION["Wfengine"];
			$userViewData 		= UsersHelper::buildCurrentUserForView();
			$this->setData('userViewData',$userViewData);
			$process 			= new ViewProcess(urldecode($processDefinitionUri));

		
			$processAuthoringData 	= array();
			$processAuthoringData['processUri'] 	= $processDefinitionUri;
			$processAuthoringData['processLabel']	= 'Capi Background Questionnaire';
			$processAuthoringData['variables']		= array();
				
			$uiLanguages		= I18nUtil::getAvailableLanguages();
					
			$this->setData('uiLanguages',$uiLanguages);
			
			// Process variables retrieving.
			$variables = $process->getProcessVars();

			foreach ($variables as $key => $variable)
			{
				$name 			= $variable[0];
				$widgetType		= $variable[1];
				$propertyRange 	= $variable[2];
				$propertyKey	= $key;

				// OMG :D !
				$val["PropertyRange"] = $variable[2];
				$val["PropertyKey"] = $key;

				// Euh what's happening :D ? Ask it to PPL.
				// Utils::getRemoteKB($propertyRange);

				include(GENERIS_BASE_PATH."/core/widgets/".urlencode($widgetType).".php");
				$widget = str_replace("instanceCreation", "posted", $widget);

				$processAuthoringData['variables'][] = array('name'		=> $name,
														   	 'widget'	=> $widget);
			}
			// View selection.
			$this->setData('processAuthoringData',$processAuthoringData);
			$this->setView('process_authoring_old.tpl');

//		}
//		else
//		{
//			// Service mode is enabled so that this action is supported.
//			UsersHelper::informServiceMode();
//		}
	}

	public function add($posted)
	{
		ini_set('max_execution_time', 200);
		var_dump($posted);
		// This action is not available when running the service mode.
		if (!SERVICE_MODE)
		{
			//TODO UGLY but not my fault. NOTE JBO: Forgiven.

			if (isset($posted['login']) && isset($posted['pwd']))
			{
				UsersHelper::authenticate($posted['login'],$posted['pwd']);
			}
			else
			{
				UsersHelper::checkAuthentication();
			}

				

			$processExecutionFactory = new ProcessExecutionFactory();

						
			$processExecutionFactory->name = $posted["properties"][RDFS_LABEL][0];
			$processExecutionFactory->comment = 'Created ' . date(DATE_ISO8601);
			
			$processExecutionFactory->intervieweeUri = 'http://www.tao.lu/middleware/Interview.rdf#test2';


			$processExecutionFactory->execution = urldecode($posted['executionOf']);
			$newProcessExecution = $processExecutionFactory->create();

				
			$newProcessExecution->feed();
				
			// We build the next url for view state. Two possibilities :
			// 1. We go back to the main.
			// 2. We begin the newly created process.
			$viewState = '';
			if (BEGIN_WHEN_PROCESS_CREATED)
			{
				$processUri = urlencode($newProcessExecution->uri);
				$viewState = "processBrowser/index?processUri=${processUri}";
		}
		else
		{
			$viewState = 'main/index';
		}
			
		$this->redirect($viewState);
	}
	else
	{
		// We are running in service mode so that this action is
		// simply not available.
		UsersHelper::informServiceMode();
	}
}

protected static function compareCaseFile($a, $b)
{
	$a = $a['id'];
	$b = $b['id'];

	if ($a > $b)
	{
		return 1;
	}
	else if ($a == $b)
	{
		return 0;
	}
	else
	{
		return -1;
	}
}
}
?>