<?php

error_reporting(-1);

/**
 * WorkFlowEngine - class.Process.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 22.08.2008, 09:36:17
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007DB-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007DB-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007DB-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007DB-constants end

/**
 * Short description of class Process
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class wfEngine_models_classes_Process
extends wfEngine_models_classes_WfResource
{
	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute rootActivities
	 *
	 * @access public
	 * @var array
	 */
	public $rootActivities = array();

	/**
	 * Short description of attribute activities
	 *
	 * @access public
	 * @var array
	 */
	public $activities = array();

	// --- OPERATIONS ---

	/**
	 * Short description of method getRootActivities
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return array
	 */
	public function getRootActivities()
	{
		$returnValue = array();

		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000852 begin
		$processActivitiesProp = new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES);
		$activities = $this->resource->getPropertyValuesCollection($processActivitiesProp);
	// var_dump(PROPERTY_PROCESS_ACTIVITIES, $activities);
		foreach ($activities->getIterator() as $activity)
		{
			$activityIsInitialProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);


			$isInitialCollection= $activity->getOnePropertyValue($activityIsInitialProp);
		
			if ($isInitialCollection!= null && $isInitialCollection->uriResource == GENERIS_TRUE)
			{
				$activityObject = new wfEngine_models_classes_Activity($activity->uriResource);
				$returnValue[] =$activityObject;
			}
		}

		$this->rootActivities = $returnValue;
		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000852 end

		return (array) $returnValue;
	}

	/**
	 * Short description of method getProcessDefinition
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function getProcessDefinition()
	{
		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000085C begin
		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000085C end
	}
	

	/**
	 * Short description of method getProcessVars
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return array
	 */
	public function getProcessVars()
	{
		$returnValue = array();

		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000085E begin
		$processVariableprop = new core_kernel_classes_Property(PROPERTY_PROCESS_VARIABLE);
		$variables = $this->resource->getPropertyValuesCollection($processVariableprop);
			
		$vars=array();
		$vars[RDFS_LABEL] = array("Name",WIDGET_FTE,RDFS_LITERAL);

		foreach ($variables->getIterator() as $variable)
		{
			//widget
			$widgetProp = new core_kernel_classes_Property(PROPERTY_WIDGET);
			$widgets = $variable->getPropertyValues($widgetProp);
						

			//label
			$label = $variable->getLabel();

			//range
			$rangeProp = new core_kernel_classes_Property(RDFS_RANGE);
			$range = $variable->getPropertyValues($rangeProp);

			$vars[$variable->uriResource] = array(trim(strip_tags($label)),	$widgets,$range);
			
		}

		$returnValue=$vars;
		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000085E end

		return (array) $returnValue;
	}

	/**
	 * Short description of method feedFlow
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function feedFlow()
	{
		// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008FF begin
		foreach ($this->getRootActivities() as $rootActivity)
		{$rootActivity->feedFlow();}
			
		unset($_SESSION["activities"]);
		// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008FF end
	}

	/**
	 * Short description of method getAllActivities
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return array
	 */
	public function getAllActivities()
	{
		$returnValue = array();

		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A0B begin
		$activities = array();
		$actsProp = new core_kernel_classes_Property(PROCESS_ACTIVITIES);
		$acts = $this->resource->getPropertyValuesCollection($actsProp);

		foreach ($acts->getIterator() as $activityResource)
		{
			$activities[] = new wfEngine_models_classes_Activity($activityResource->uriResource);
		}

		$returnValue = $activities;
		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A0B end

		return (array) $returnValue;
	}

} /* end of class Process */

?>