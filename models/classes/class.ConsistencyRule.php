<?php
class ConsistencyRule extends PiaacRule
{
	public $involvedActivities = array();
	public $suppressable;
	public $notification;
	
	public function __construct($ressource)
	{
		parent::__construct($ressource);
		
		// Retrieval of activities.
		$involvedActivitiesProperty = new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_INVOLVEDACTIVITIES);
		$involvedActivitiesValues 	= $this->getPropertyValues($involvedActivitiesProperty);
		
		if (count($involvedActivitiesValues))
		{
			foreach ($involvedActivitiesValues as $activityUri)
			{
				$involvedActivity = new Activity($activityUri);	
				$this->involvedActivities[] = $involvedActivity;
			}
		}
		
		// Notification retrieval.
		$notificationProperty = new core_kernel_classes_Property(PROPERTY_NOTIFICATION,__METHOD__);
		$commentPropertyValue = $this->getUniquePropertyValue($notificationProperty);
		$this->notification = $commentPropertyValue->literal;
		
		// Suppressability retrieval.
		$suppressableProperty = new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_SUPPRESSABLE);
		$suppressablePropertyValue = $this->getUniquePropertyValue($suppressableProperty);
		$this->suppressable = ($suppressablePropertyValue->uriResource == GENERIS_TRUE);
	}
}
?>