<?php
class GUIHelper
{
	const CTRL 	= 'DOM_VK_CONTROL';
	const ALT 	= 'DOM_VK_ALT';
	const SHIFT = 'DOM_VK_SHIFT';
	
	public static function buildKeyboardShortcuts($lang)
	{
		$jsOutput = '';
		
		$localesDir = dirname(__FILE__) . '/../../locales/' . $lang;
		$iniReader 	= new IniReader($localesDir . '/shortcuts.ini');
		
		$shortcutsMap = $iniReader->getMap();
		
		foreach ($shortcutsMap as $action => $combination)
		{
			if (ereg('^(([^\+]+) |\t*\+ )*([^\+]+)$', $combination))
			{
				// Key collect and cleaning.
				$combinations = explode('+', $combination);
				for ($i = 0; $i < count($combinations); $i++) $combinations[$i] = trim($combinations[$i]);
				
				$functionId 	= $action;
				$masterKey		= '';
				$useControl		= 'false';
				$useAlternate	= 'false';
				$useShift		= 'false';
				
				// Now we build the Javascript object depecting the key combination.
				foreach ($combinations as $key)
				{					
					switch ($key)
					{
						case self::CTRL:
							$useControl = 'true';
						break;
						
						case self::ALT:
							$useAlternate = 'true';
						break;
						
						case self::SHIFT:
							$useShift = 'true';
						break;
						
						default:
							$masterKey = $key;
					}
				}
				
				$jsOutput .= "{functionId:'${functionId}', masterKey:'${masterKey}', useControl:${useControl}, useAlternate:${useAlternate}, useShift:${useShift}},";
			}
		}
		
		// Last comma cleaning.
		if (count($shortcutsMap)) $jsOutput = substr($jsOutput, 0, strlen($jsOutput) - 1);
		
		$jsOutput = '[' . $jsOutput . ']';
		
		return $jsOutput;
	}
	
	public static function buildActivitiesList(array $activities)
	{
		$jsOutput = '';

		foreach ($activities as $activity)
		{
			$uri 		= urlencode($activity['uri']);
			$label 		= addslashes($activity['label']);
			$processUri	= urlencode($activity['processUri']);
			
			$jsOutput .= "{uri:'${uri}', label:'${label}', processUri:'${processUri}'},";
		}
		
		// Last comma cleaning.
		if (count($activities)) $jsOutput = substr($jsOutput, 0, strlen($jsOutput) - 1);
		
		$jsOutput = '[' . $jsOutput . ']';
		
		return $jsOutput;
	}
	
	public static function sanitizeGenerisString($string)
	{
		return str_replace(array('&nbsp;'), ' ', $string);
	}
	public static function jsonIfY($array)
	{
		
		$json = json_encode($array);
		
		return $json;
	}
	public static function buildStatusImageURI($strStatus)
	{
		$baseURI = $GLOBALS['dir_theme'] . 'img/status_';
		$statusName = '';
		
		switch ($strStatus)
		{
			case 'Paused':
				$statusName = 'paused';
			break;
			
			case 'Resumed':
				$statusName = 'resumed';
			break;
			
			case 'Finished':
				$statusName = 'finished';
			break;
			
			case 'Started':
				$statusName = 'started';
			break;
			
			case 'Stopped':
				$statusName = 'stopped';
			break;
		}
		
		return $baseURI.$statusName.'.png';
	}
	
	public static function buildContentLanguage($lgString)
	{
		return $lgString;
	}
}
?>