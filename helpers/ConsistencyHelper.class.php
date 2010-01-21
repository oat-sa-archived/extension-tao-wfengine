<?php
class ConsistencyHelper
{
	public static function BuildConsistencyStructure(ConsistencyException $consistencyException)
	{
		$consistency = array();
		$consistency['notification'] = $consistencyException->notification;
		$consistency['suppressable'] = ($consistencyException->getCode() == ConsistencyException::SUPPRESSABLE);
		$consistency['involvedActivities'] = array();
		$consistency['source'] = $consistencyException->sourceActivity->label;
		
		foreach($consistencyException->involvedActivities as $involved)
		{
			$consistency['involvedActivities'][] = array('uri' => $involved->uri,
														 'label' => $involved->label);	
		}
		
		return $consistency;
	}
}
?>