<?php
class InferenceException extends WfException
{

	public function __construct($message, 
								$code = null)
	{
		parent::__construct($message, $code);
	}
}
?>