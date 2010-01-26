<?php

error_reporting(E_ALL);

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

/**
 * include Activity
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Activity.php');

/**
 * include ProcessExecution
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ProcessExecution.php');

/**
 * include wfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.wfResource.php');

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
class Process
extends wfResource
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
	 * Short description of method getActors
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return array
	 */
	public function getActors()
	{
		$returnValue = array();

		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000081E begin

		$activities  = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
		array($this->uri),
		array(PROPERTY_PROCESS_ACTIVITIES),
		array(""));
		$roles = array();

		foreach ($activities as $key=>$activity)
		{
			$activityObject 	= new Activity($activity);
			$roles				= array_merge($roles, $activityObject->getActors());
		}


		//hack to get unique array ...
		//as described http://lu.php.net/array_unique
		foreach ($roles as $key => $value)
		$roles[$key] = "'" . serialize($value) . "'";

		$roles = array_unique($roles);

		foreach ($roles as $key=>$value)
		$roles[$key] = unserialize(trim($value, "'"));

		$returnValue = $roles;
		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000081E end

		return (array) $returnValue;
	}

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
		$activities  = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
		array($this->uri),
		array(PROPERTY_PROCESS_ACTIVITIES),
		array(""));
			
		foreach ($activities as $key=>$activity)
		{
			$isInitial = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
			array($activity),
			array(PROPERTY_ACTIVITIES_ISINITIAL),
			array(""));
				
			if (count($isInitial) && ($isInitial[0])=="http://www.tao.lu/Ontologies/generis.rdf#True")
			{
				$activityObject = new Activity($activity);
				$activityObject->getActors();
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
			$label = $variable->getLabels();

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

		$acts = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
		array($this->uri),
		array(PROCESS_ACTIVITIES),
		array(""));

		foreach ($acts as $activityUri)
		{
			$activities[] = new Activity($activityUri);
		}

		$returnValue = $activities;
		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A0B end

		return (array) $returnValue;
	}

} /* end of class Process */

?>