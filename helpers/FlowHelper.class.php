<?php
class FlowHelper
{
	public static function getFlowPosition(Activity $activity)
	{
		if (!$activity->isFirst() && !$activity->isLast())
			return FLOW_POSITION_MIDDLE;
		
		else if ($activity->isFirst())
			return FLOW_POSITION_START;
		
		else
			return FLOW_POSITION_END;
		
	}
	
	public static function isProcessBackable(ProcessExecution $process)
	{
		return $process->isBackable();
	}
	
	public static function isLastActivity(Activity $activity)
	{
		return $activity->isLast();
	}
}
?>