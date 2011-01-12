<?php
class ActivityHelper
{
	public static function hasInteractiveServices(wfEngine_models_classes_ActivityExecution $activity)
	{
		return count($activity->getInteractiveServices());
	}
}
?>