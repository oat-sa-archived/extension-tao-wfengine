<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.Connector.php
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

/* user defined includes */
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008D4-includes begin
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008D4-includes end

/* user defined constants */
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008D4-constants begin
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008D4-constants end

/**
 * Short description of class wfEngine_models_classes_Connector
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class wfEngine_models_classes_Connector
    extends wfEngine_models_classes_WfResource
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute nextActivities
     *
     * @access public
     * @var array
     */
    public $nextActivities = array();

    /**
     * Short description of attribute type
     *
     * @access public
     * @var string
     */
    public $type = '';

    /**
     * Short description of attribute prevActivities
     *
     * @access public
     * @var array
     */
    public $prevActivities = array();

    /**
     * Short description of attribute transitionRule
     *
     * @access public
     * @var object
     */
    public $transitionRule = null;

    /**
     * Short description of attribute consistencyRules
     *
     * @access public
     * @var array
     */
    public $consistencyRules = array();

    /**
     * Short description of attribute inferenceRules
     *
     * @access public
     * @var array
     */
    public $inferenceRules = array();

    // --- OPERATIONS ---

    /**
     * Short description of method feedFlow
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param int
     * @return void
     */
    public function feedFlow($recursivityLevel = "")
    {
        // section -64--88-1-64--7117f567:11a0527df60:-8000:0000000000000914 begin
									  
		// Next activities feeding.
		if ($recursivityLevel != "") {
			$recursivityLevel--;
		}
		$geNextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		$geNextActivities = $this->resource->getPropertyValuesCollection($geNextActivitiesProp);


		$this->nextActivities = array();
		$this->precActivities = Array();
		foreach ($geNextActivities->getIterator() as $val)
		{
			$typeProp = new core_kernel_classes_Property(RDF_TYPE);

			$isAConnector = $val->getUniquePropertyValue($typeProp);
			
			if ($isAConnector->uriResource == CLASS_ACTIVITIES)
			{
				
				if (isset($_SESSION["activities"][$val->uriResource])) 
				{
					
					$this->nextActivities[] =$_SESSION["activities"][$val->uriResource];
				} 
				else
				{
					
					$activity = new wfEngine_models_classes_Activity($val->uriResource);
					$_SESSION["activities"][$val->uriResource] = $activity;

					$this->nextActivities[] = $activity;						
				}
			}
			
		}
		
		// Previous activities feeding.
		$gePrevActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES);
		$gePrevActivities = $this->resource->getPropertyValuesCollection($gePrevActivitiesProp);
    	
													  	
		//if (sizeOf($gePrevActivities)>1) echo $this->label; 
		foreach ($gePrevActivities->getIterator() as $val)
		{
			$typeProp = new core_kernel_classes_Property(RDF_TYPE);

			$isAConnector = $val->getUniquePropertyValues($typeProp);
			
			if ($isAConnector->uriResource == CLASS_ACTIVITIES)
			{
				
			
				if (isset($_SESSION["activities"][$val->uriResource])) 
				{
					$this->prevActivities[] = $_SESSION["activities"][$val->uriResource];
				} 
				else
				{
					
					//we should detect loops, todo
					
					
					$activity = new wfEngine_models_classes_Activity($val->uriResource);
					$_SESSION["activities"][$val->uriResource] = $activity;
						
					$this->prevActivities[] = $activity;	
					
					
				}
			}
			
		}
		
        // section -64--88-1-64--7117f567:11a0527df60:-8000:0000000000000914 end
    }
    
    /**
     * Short description of method getType
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function getType() {
    	
    	$returnValue = null;
    	
    	$resource = new core_kernel_classes_Resource($this->uri,__METHOD__);
    	$connTypeProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE,__METHOD__);
		try {
    		
			$returnValue = $resource->getUniquePropertyValue($connTypeProp);
		}
		catch (common_Exception $e) {
			echo 'Exception when retreiving Connector type ' . $this->uri;
		}
    	return $returnValue;
    }
    
    /**
     * Short description of method getNextActivities
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function getPreviousActivities() {
    	if(empty($this->prevActivities)) {
	    	$precActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES,__METHOD__);
			$resource = new core_kernel_classes_Resource($this->uri,__METHOD__);
			$this->prevActivities = $resource->getPropertyValuesCollection($precActivitiesProp);
    	}
		return $this->prevActivities;
    
    }
    
    /**
     * Short description of method getNextActivities
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function getNextActivities() {
    	if(empty($this->nextActivities)){
	    	$nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES,__METHOD__);
			$resource = new core_kernel_classes_Resource($this->uri,__METHOD__);
			$this->nextActivities = $resource->getPropertyValuesCollection($nextActivitiesProp);
    	}
		return $this->nextActivities;
    
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @return void
     */
    public function __construct($uri)
    {
        // section -64--88-1-64--7117f567:11a0527df60:-8000:0000000000000935 begin

		parent::__construct($uri);
		
		$typeOfConnectorProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE);
		$this->logger->debug('Next Connector  Name: ' . $this->resource->getLabel(),__FILE__,__LINE__);
		$this->logger->debug('Next Connector  Uri: ' . $this->resource->uriResource,__FILE__,__LINE__);
		try{
			$this->type = $this->resource->getUniquePropertyValue($typeOfConnectorProp);
		}
		catch(common_Exception $ce){
			echo 'Exception when retreiving Connector type ' . $this->uri;
		}
		// We get the TransitionRule relevant to the connector.
		$ruleProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE);
		$rule = $this->resource->getPropertyValues($ruleProp);


		if (count($rule)&& $rule[0] != ""){
			
			$this->transitionRule = new TransitionRule($rule[0]);
			
		}
		else {
			$this->transitionRule = null;
		}
		
        // section -64--88-1-64--7117f567:11a0527df60:-8000:0000000000000935 end
    }

} /* end of class Connector */

?>