<?php
class ActivityHelper
{
	public static function hasInteractiveTools(ActivityExecution $activity)
	{
		return count($activity->getInteractiveTools());
	}
}
?>