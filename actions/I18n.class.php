<?php
class I18n
{
	public static function translations()
	{
		header('Content-type: application/javascript');
		
		// Please do not refresh this !!!
		header('Cache-control: ');
		header('Expires: ');
		header('Pragma: ');
		header('Etag: "JsTranslation"');
		
		$messages = array(array("Question (Ctrl-Q)", 				__("Question (Ctrl-Q)")),
						  array("Type (Ctrl-T)", 					__("Type (Ctrl-T)")),
						  array("Add (Ctrl-A)", 					__("Add (Ctrl-A)")),
						  array("Enter remark text here", 			__("Enter remark text here")),
						  array("Close (Esc)", 						__("Close (Esc)")),
						  array("(Ctrl-C)", 						__("(Ctrl-C)")),
						  array("Export - Debug only (Ctrl-E)",		__("Export - Debug only (Ctrl-E)")),
						  array("Close (Esc)", 						__("Close (Esc)")),
						  array("Modify" , 							__("Modify")),
						  array("Suppress", 						__("Suppress")),
						  array("Go to", 							__("Go to")));
		
		$translations = array();
		
		foreach($messages as $msg)
			$translations[] = array('original' 	=> $msg[0], 'translated'	=> $msg[1]);
			
		$I18nViewData['translations'] = json_encode($translations);
		
		require_once(GenerisFC::getView('i18n.tpl'));
	}
}
?>