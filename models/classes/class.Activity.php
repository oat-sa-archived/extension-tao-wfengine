<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.Activity.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 13.11.2008, 16:17:41
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include ActivityExecution
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ActivityExecution.php');

/**
 * include Connector
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Connector.php');

/**
 * include Process
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Process.php');

/**
 * include ProcessExecution
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ProcessExecution.php');

/**
 * include Tool
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Tool.php');

/**
 * include WfRole
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.WfRole.php');

/**
 * include wfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.wfResource.php');

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-constants end

/**
 * Short description of class Activity
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Activity
    extends wfResource
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute process
     *
     * @access public
     * @var Process
     */
    public $process = null;

    /**
     * Short description of attribute nextConnectors
     *
     * @access public
     * @var array
     */
    public $nextConnectors = array();

    /**
     * Short description of attribute actors
     *
     * @access public
     * @var array
     */
    public $actors = array();

    /**
     * Short description of attribute acceptedRole
     *
     * @access public
     * @var WfRole
     */
    public $acceptedRole = null;

    /**
     * Short description of attribute consistencyRule
     *
     * @access public
     * @var object
     */
    public $consistencyRule = null;
    
    public $inferenceRule = array();
    
    public $onBeforeInferenceRule = array();
    
    public $showCalendar = false;
    
    public $isHidden = false;

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

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000850 begin
		
		$activityroles  = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,array($this->uri),array(ACTIVITY_ROLE),array(""));
		foreach ($activityroles as $key=>$roleUri)
			{
				$labelcomment = getlabelcomment(Wfengine::singleton()->sessionGeneris,$roleUri,array(""));
				$returnValue[]=array($roleUri,trim(strip_tags($labelcomment["label"])));
			}
		$this->actors = $returnValue;
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000850 end

        return (array) $returnValue;
    }

    /**
     * feeds connector attribute and recursively through the complete definiton
     * the process
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param int
     * @return void
     */
    public function feedFlow($recursivityLevel = "")
    {
        // section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008F4 begin
		
		// We get the next connectors.		
		$nextConnectors  = execSQL(Wfengine::singleton()->sessionGeneris,"AND predicate='".PROPERTY_CONNECTORS_PRECACTIVITIES."' AND object ='".$this->uri."'", array());

		$connectors =array();
		foreach ($nextConnectors as $key=>$statement)
		{
			$isAConnector = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
			array($statement["subject"]),												  array("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"),
			array(""));
			
			if ($isAConnector[0] == CLASS_CONNECTORS)
			{
				$connector	= new Connector($statement["subject"]);
				$this->nextConnectors[] = $connector;
				//$connector->feedFlow($recursivityLevel);	
			}
		}
		
		// We get the associated consistency rule.
		// Please be carefull that an activity wihtout any transition rule is absolutely valid.
		$consistencyRules = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
													 array($this->uri),
													 array(PROPERTY_ACTIVITIES_CONSISTENCYRULE),
													 array(""));
													 
        if (count($consistencyRules) && $consistencyRules[0] != false)
			$this->consistencyRule = new ConsistencyRule($consistencyRules[0]);
			
		// We get the associated onAfterInferenceRule.
		$inferenceRules = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
													array($this->uri),
													array(PROPERTY_ACTIVITIES_INFERENCERULE),
													array(""));
													
		if (count($inferenceRules))
		{
			foreach ($inferenceRules as $inf)
				$this->inferenceRule[] = new InferenceRule($inf);
		}
			
		// We get the associated onBeforeInferenceRule.
		$inferenceRules = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,
													array($this->uri),
													array(PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE),
													array(""));
													
		if (count($inferenceRules))
		{
			foreach ($inferenceRules as $inf)
				$this->onBeforeInferenceRule[] = new InferenceRule($inf);
		}
		
        // section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008F4 end
    }

    /**
     * Short description of method getTools
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param ActivityExecution
     * @return array
     */
    public function getTools( ActivityExecution $execution = null)
    {
        $returnValue = array();

        // section 10-13-1--31-2237f23b:11a39ee89a9:-8000:000000000000098F begin
		$interactiveTools = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,array($this->uri),array(PROPERTY_ACTIVITIES_ISERVICES),array(""));
			$interactiveToolsDescription=array();
			if (sizeOf($interactiveTools)>0)
			{
			if ($interactiveTools[0]!="")
			{
			foreach ($interactiveTools as $interactiveTool)
				{
									
					$interactiveToolsDescription[] = new Tool($interactiveTool,$execution);
				}
			}
			}
		
		$returnValue = $interactiveToolsDescription;
        // section 10-13-1--31-2237f23b:11a39ee89a9:-8000:000000000000098F end

        return (array) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @return void
     */
    public function __construct($uri, $feed = true)
    {
        // section 10-13-1-85-16731180:11be4127421:-8000:00000000000009FA begin
    	
    	parent::__construct($uri);
        
    	if ($feed)
    	{
    		$acceptedRole = getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
        										  array($this->uri),
        										  array(ACTIVITY_ROLE),
        										  array(''));
        	if (isset($acceptedRole[0]))
        		$this->acceptedRole = new WfRole($acceptedRole[0]);
        		
        	
	    	// Calendar
	    	$showCalendar = getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
	        											  array($this->uri),
	        											  array(PROPERTY_ACTIVITIES_DISPLAYCALENDAR),
	        											  array(''));
	        											  
	        if (count($showCalendar))
	        {
	        	if ($showCalendar[0] == GENERIS_TRUE)
	        	{
	        		$this->showCalendar = true;
	        	}
	        }
	        
	        // Hidden
	    	$isHidden = getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
	        											  array($this->uri),
	        											  array(PROPERTY_ACTIVITIES_ISHIDDEN),
	        											  array(''));
	        											  
	        if (count($isHidden))
	        {
	        	if ($isHidden[0] == GENERIS_TRUE)
	        	{
	        		$this->isHidden = true;
	        	}
	        }
    	}
        
        // section 10-13-1-85-16731180:11be4127421:-8000:00000000000009FA end
    }

    /**
     * Short description of method isFirst
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isFirst()
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A03 begin
        
        //if (defined('PIAAC_ENABLED') && $this->label == PIAAC_FIRST_ACTIVITY)
        //	return true;
        
   	 	$isInitial = getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
        									  array($this->uri),
        									  array(ACTIVITIES_IS_INITIAL),
        									  array(''));
        if (isset($isInitial[0]))
		{
        $returnValue = ($isInitial[0] == GENERIS_TRUE) ? true : false;
		}
		else
		{
			$returnValue = false;
		}
        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A03 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isLast
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isLast()
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A07 begin
        
        $nextActivities  = execSQL(Wfengine::singleton()->sessionGeneris,
								   "AND predicate='" . PREC_ACTIVITIES . "' AND object ='" . $this->uri . "'", 
								   array());	

								   
        $returnValue = (sizeOf($nextActivities)==0);
        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A07 end

        return (bool) $returnValue;
    }

} /* end of class Activity */

?>