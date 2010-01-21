<?php
class ConsistencyException extends WfException
{
	const SUPPRESSABLE = true;
	const UNSUPPRESSABLE = false;
	
	public $sourceActivity;
	public $involvedActivities;
	public $notification;
	
	public function __construct($message, 
								Activity $sourceActivity,
								array $involvedActivities,
								$notification, 
								$code)
	{
		parent::__construct($message, $code);
		
		$this->sourceActivity 		= $sourceActivity;
		$this->involvedActivities 	= $involvedActivities;
		$this->notification 		= $notification;
	}
}
?>