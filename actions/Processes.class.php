<?php
class Processes
{
	public function authoring($processDefinitionUri)
	{
		// This action is not available when running
		// the service mode !
		if (!SERVICE_MODE)
		{
			UsersHelper::checkAuthentication();	
							
			$wfEngine 			= $_SESSION["Wfengine"];
			$userViewData 		= UsersHelper::buildCurrentUserForView();
			$process 			= new ViewProcess(urldecode($processDefinitionUri));
		
			if (DEBUG_MODE_ENABLE)
			{
		
				$processes 			= $wfEngine->getProcessExecutions();
				$filter = array();
				$filter[] = '.svn';
		
				foreach ($processes as $proc){
					$procVariables = Utils::processVarsToArray($proc->getVariables());
		
					$intervieweeInst = new core_kernel_classes_Resource($procVariables[VAR_INTERVIEWEE_URI],__METHOD__);
					$property = propertyExists(CASE_ID_CODE);
		
					if(($property)) 
					{
						
						$caseIdProp = new core_kernel_classes_Property($property,__METHOD__);
						$result = $intervieweeInst->getPropertyValuesCollection($caseIdProp);
		
						if ($result->count() == 1)
						{
							$filter[] = $result->get(0)->literal . '.zip';
						}
						else {
							foreach ($result->getIterator() as $container) {
								if ($container instanceof core_kernel_classes_Literal ){
									$filter[] = $container->literal . '.zip';
								}
							}
						}
					}
				}
			
			
				$caseIdViewData = array();
				$caseIdFiles = FileSystemUtil::getDirectoryContentNames(SHARE_DIRECTORY.'/Import',$filter);
				
				
				foreach ($caseIdFiles as $caseId){
					if(strpos($caseId,'.zip')){
						$plop = str_split($caseId,strpos($caseId,'.zip'));		
						$caseIdViewData[]['id'] = $plop[0];
					}	
				}
				
				usort($caseIdViewData, 'Processes::compareCaseFile');
			}
			
			$processAuthoringData 	= array();
			$processAuthoringData['processUri'] 	= $processDefinitionUri;
			$processAuthoringData['processLabel']	= 'PIAAC Background Questionnaire';
			$processAuthoringData['variables']		= array();
			
			$uiLanguages		= I18nUtil::getAvailableLanguages();
			
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
				
				include("../../../generis/core/widgets/".urlencode($widgetType).".php"); 
				$widget = str_replace("instanceCreation", "posted", $widget); 
				
				$processAuthoringData['variables'][] = array('name'		=> $name,
														   	 'widget'	=> $widget);
			}
			// View selection.
			if (DEBUG_MODE_ENABLE)
			{			
				require_once(GenerisFC::getView('process_authoring.tpl'));
			}
			else 
			{
				require_once(GenerisFC::getView('process_authoring_old.tpl'));
			}
		}
		else
		{
			// Service mode is enabled so that this action is supported.
			UsersHelper::informServiceMode();	
		}
	}
	
	public function add($posted)
	{
		ini_set('max_execution_time', 200);
		
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

			
			if (DEBUG_MODE_ENABLE) 
			{
				$processExecutionFactory = new ProcessExecutionFactory();
				if($posted['new'] === __("Import Interview") && isset($posted['caseId'])) 
				{	
					$processExecutionFactory->name = urldecode($posted['caseId']);
					$processExecutionFactory->comment = urldecode($posted['caseId']) ;
			
					if (defined('PIAAC_ENABLED'))
					{
						$processExecutionFactory->intervieweeUri = PiaacDataExchange::importAll($posted['caseId']);
						
						if ($owner = PiaacCommonUtils::getInterviewerById($_SESSION['taoqual.userId']))
						{
							$processExecutionFactory->ownerUri = $owner->uriResource;
						}
					}
				}
				else 
				{
					$processExecutionFactory->name = 'Interview ' . date(DATE_ISO8601);
					$processExecutionFactory->comment = 'comment';
					
					if (defined('PIAAC_ENABLED'))
					{
						$processExecutionFactory->intervieweeUri = PiaacHyperClassUtils::import();
						
						if ($owner = PiaacCommonUtils::getInterviewerById($_SESSION['taoqual.userId']))
						{
							$processExecutionFactory->ownerUri = $owner->uriResource;
						}
					}
				}
				
				$processExecutionFactory->execution = urldecode($posted['executionOf']);
				$newProcessExecution = $processExecutionFactory->create();
			}
			else 
			{
				$newProcessExecution = Utils::createProcessExecution($posted);
			}
			
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
			
			GenerisFC::redirection($viewState);
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