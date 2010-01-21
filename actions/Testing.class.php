<?php
class Testing
{
	/**
	* retrieve all annotations for the current activity
	*/
	public function getData($processUri, $intervieweeUri)
	{
		
		UsersHelper::checkAuthentication();	
		
		$data=array();	$adaptedItems = array();
		$listOfAdaptedItems = $this->getListOfExtensions();
			//retrieve all properties ... 
			$activities  = execSQL(Wfengine::singleton()->sessionGeneris," AND subject ='".PIAAC_PROCESS_URI."'
			and 
				(
					predicate = '".PROPERTY_PROCESS_ACTIVITIES."'
				)
					", array());

			foreach ($activities as $activity)
			{
				$activityRes  = new core_kernel_classes_Resource($activity[2]);
				$activityResLabel = $activityRes->getLabel();
				if ($activityResLabel != $activityRes->uriResource)
				{
				
				if (isset($listOfAdaptedItems[$activityResLabel]))
					{
						$adaptedItems[$activityResLabel] = $activityRes;
					}
				$data[$activityResLabel] = $activityRes;
				}
			}
			ksort($data);
			//print_r($data);
			require_once (GenerisFC::getView('testing.tpl'));
	}
	
	private function getListOfExtensions()
	{
		$itemGroups =array();
		if (is_file(dirname(__FILE__)."/../../../../piaac/locales/".PIAAC_VERSION."/itemGroups.php"))
		{
		include_once(dirname(__FILE__)."/../../../../piaac/locales/".PIAAC_VERSION."/itemGroups.php");
		}
		return $itemGroups;
	}

}
?>