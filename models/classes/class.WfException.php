<?php
class wfEngine_models_classes_WfException extends Exception
{
	public function __construct($message, $code = null)
	{
		parent::__construct($message, $code);
	}
}
?>