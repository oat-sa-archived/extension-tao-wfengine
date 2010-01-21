<?php
class Watch
{
	/**
	* retrieve all annotations for the current activity
	*/
	public function getData($processUri, $intervieweeUri)
	{
		UsersHelper::checkAuthentication();	
		
		$browserViewData['annotationsJsArray']= Array();
		$data=array();	
			//retrieve all properties ... 
			$properties  = execSQL(Wfengine::singleton()->sessionGeneris," AND object='".CLASS_INTERVIEWEE."'
			and 
				(
					predicate = '".RDF_DOMAIN."'
				)
					", array());

			foreach ($properties as $property)
			{
				$values  = execSQL(Wfengine::singleton()->sessionGeneris," AND subject='".urldecode($intervieweeUri)."'
				and 
					(
						predicate = '".$property[0]."'
					)
						", array());

				
				$property  = new core_kernel_classes_Resource($property[0]);
				$propertyLabel = str_replace("Generated Property","",$property->getLabel());
				
				// In PIAAC We have sometimes to replace some variables label with another one.
				if (defined('PIAAC_ENABLED'))
					$propertyLabel = PiaacCommonUtils::watchVariablesLabelPlaceHolder($propertyLabel);
				
				$data[$propertyLabel]["values"] =array();
				foreach ($values as $value)
				{
					if (common_Utils::isUri($value[2]))
					{
						$valResource = new core_kernel_classes_Resource($value[2]);
						$data[$propertyLabel]["values"][] = $valResource->getLabel();
						
						$inputCode  = execSQL(Wfengine::singleton()->sessionGeneris," AND subject='".$value[2]."'
						and 
							(
								predicate = '".PROPERTY_CODE."'
							)
								", array());
						$data[$propertyLabel]["inputCode"][] = $inputCode[0][2];
					}
					else
					{
						$data[$propertyLabel]["values"][] = $value[2];
					}
				}


			}
			ksort($data);
			require_once (GenerisFC::getView('watch.tpl'));
	}
	


}
?>