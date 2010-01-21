<?php
class Annotation
{
	/**
	* retrieve all annotations for the current activity
	*/
	public function getAnnotations($activityUri)
	{
		UsersHelper::checkAuthentication();	
		$browserViewData['annotationsJsArray']= Array();
			
			//retrieve all annotations ... 
			$annotations  = execSQL(Wfengine::singleton()->sessionGeneris," AND subject='".urldecode($activityUri)."'
			and 
				(
					predicate = '".INTERVIEW_NS."#122786664635240'
					or
					predicate = '".INTERVIEW_NS."#122786657224088'
					or
					predicate = '".INTERVIEW_NS."#122786668726350'
			
				)
					", array());
			foreach ($annotations as $key => $val)
				{
					switch ($val["predicate"])
					{
						case INTERVIEW_NS."#122786664635240":{$type=__("Response problem"); break;}
						case INTERVIEW_NS."#122786657224088":{$type=__("Observation");break;}
						case INTERVIEW_NS."#122786668726350":{$type=__("Past Item response problem");break;}
					}
					$browserViewData['annotationsJsArray'][] = Array($type,$val["object"],$val["id"]);
				}
			require_once (GenerisFC::getView('annotations.tpl'));
	}
	/*
	* saves a new annotation with a unixtimestamp,
	* type referxs to the specific predicate of annotation used, $scope is the subject, what is described
	*/
	public function save($scope,$type,$message)
	{
		UsersHelper::checkAuthentication();	
		
		$message .= '@'.mktime();

		//save the new message
		$tripleId = setStatement(Wfengine::singleton()->sessionGeneris,$scope, $type, $message, "l",$GLOBALS['lang'], "", "r");
		
		
		//send back the information on the client
		$scopeRes = new core_kernel_classes_resource($scope);
		
		
		$annotationView["scope"] = $scopeRes->getLabel();
		switch ($type)
			{
				case "#122786664635240":{$annotationView["type"]=__("Response problem"); break;}
				case "#122786657224088":{$annotationView["type"]=__("Observation");break;}
				case "#122786668726350":{$annotationView["type"]=__("Past Item response problem");break;}
				default:$annotationView["type"]=__("Observation");
			}
	
		$annotationView["msg"] = $message;


		//send it to the view, it will be inserted by the ajax callback

		//require_once (GenerisFC::getView('annotationMsg.tpl'));
		
	}
	/**
	* removes a specific annotation
	*/
	public function remove($tripleId)
	{
		UsersHelper::checkAuthentication();	
		removeStatement(Wfengine::singleton()->sessionGeneris,$tripleId);
		
	}

	/**
	* export all annotations
	*/
	public function export()
	{
		UsersHelper::checkAuthentication();	
		
			$browserViewData['annotationsJsArray']=array();
			//retrieve all annotations ... 
			error_reporting(E_ALL);
			$annotations  = execSQL(Wfengine::singleton()->sessionGeneris," 
			and 
				(
					predicate = '".INTERVIEW_NS."#122786664635240'
					or
					predicate = '".INTERVIEW_NS."#122786657224088'
					or
					predicate = '".INTERVIEW_NS."#122786657224088'
			
				)
					", array());
			
			foreach ($annotations as $key => $val)
				{
					switch ($val["predicate"])
					{
						case INTERVIEW_NS."#122786664635240":{$type=__("Response problem"); break;}
						case INTERVIEW_NS."#122786657224088":{$type=__("Observation");break;}
						case INTERVIEW_NS."#122786668726350":{$type=__("Past Item response problem");break;}
					}
					$browserViewData['annotationsJsArray'][] = Array($type,$val["object"],$val["id"],$val["subject"],$val["author"]);
				}
			print_r($browserViewData['annotationsJsArray']);
			
			
		
	}


}
?>