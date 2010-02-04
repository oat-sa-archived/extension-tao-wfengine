<?php
class ActivityHelper
{
	public static function hasInteractiveServices(ActivityExecution $activity)
	{
		return count($activity->getInteractiveServices());
	}
}
?>