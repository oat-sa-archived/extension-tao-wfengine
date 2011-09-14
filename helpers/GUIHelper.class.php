<?php
//deprecated: new class generated
class GUIHelper
{
	
	
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
	
	public static function buildStatusImageURI($strStatus)
	{
		$baseURI = 'img/status_';
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
	
}
?>