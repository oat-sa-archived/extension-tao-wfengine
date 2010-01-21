<?php
class Preferences
{
	public function switchUiLanguage($lang, $from)
	{
		//batch
		$lgFrom = $_SESSION['taoqual.lang'];
		$lgTo = $lang;
		
		$_SESSION['taoqual.lang'] = str_replace("EN_EN", "EN", $lang); //php-framework
		$_SESSION['taoqual.lang'] =  $lang;
		
		if (isset($from))
		{
			$processUri = urldecode($from);
			$processExecution = new ProcessExecution($processUri, true);
			$processUri = urlencode($processUri);
			
			// We log the "LANGUAGE_CHANGE_GUI" in the log file.
			if (defined('PIAAC_ENABLED'))
			{
				$event = new PiaacBusinessEvent('BQ_ENGINE', 'LANGUAGE_CHANGE_GUI',
												'The current GUI language was changed', 
												getIntervieweeUriByProcessExecutionUri($from),
												$processExecution->currentActivity[0]->label,
												$lgFrom . '-' . $lgTo);
													  
				PiaacEventLogger::getInstance()->trigEvent($event);
			}
			
			header('Location:../../index.php/processBrowser/index?processUri=' . $processUri);
		}
		else
		{
			header('Location:../../index.php/main/index');
		}
	}
	public function nextUiLanguage($from)
	{
		$nextLanguage = I18nUtil::getNextLanguage($_SESSION['taoqual.lang']);
		$this->switchUiLanguage($nextLanguage,$from);


	}
	public function switchServiceContentLanguage($lang, $from)
	{
		$lgFrom = $_SESSION['taoqual.serviceContentLang'];
		$lgTo = $lang;
		
		$_SESSION['taoqual.serviceContentLang'] = $lang; //kernel session
		
		if (isset($from))
		{
			$processUri = urldecode($from);
			$processExecution = new ProcessExecution($processUri, true);
			$processUri = urlencode($processUri);
			
			// We log the "LANGUAGE_CHANGE_CONTENT" in the log file.
			if (defined('PIAAC_ENABLED'))
			{
				$event = new PiaacBusinessEvent('BQ_ENGINE', 'LANGUAGE_CHANGE_CONTENT',
												'The current content language was changed', 
												getIntervieweeUriByProcessExecutionUri($from),
												$processExecution->currentActivity[0]->label,
												$lgFrom . '-' . $lgTo);
													  
				PiaacEventLogger::getInstance()->trigEvent($event);
			}
			
			header('Location:../../index.php/processBrowser/index?processUri=' . $processUri);
		}
		else
		{
			header('Location:../../index.php/main/index');
		}
	}
	public function nextServiceContentLanguage($from)
	{
		$nextLanguage = I18nUtil::getServiceContentNextLanguage($_SESSION['taoqual.serviceContentLang']);
		$this->switchServiceContentLanguage($nextLanguage,$from);
	}
}
?>