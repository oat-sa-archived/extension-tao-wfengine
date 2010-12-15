<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.Activity.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatically generated on 15.12.2010, 13:08:26 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include ActivityExecution
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('class.ActivityExecution.php');

/**
 * include Connector
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('class.Connector.php');

/**
 * include Process
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('class.Process.php');

/**
 * include ProcessExecution
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('class.ProcessExecution.php');

/**
 * include Service
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('class.Service.php');

/**
 * include WfResource
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('class.WfResource.php');

/**
 * include WfRole
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('class.WfRole.php');

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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
class Activity
    extends WfResource
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 

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

    // --- OPERATIONS ---

    /**
     * Short description of method getActors
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getActors()
    {
        $returnValue = array();

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000850 begin


		$activityRoleProp = new core_kernel_classes_Property(ACTIVITY_ROLE);
		$activityroles = $this->resource->getPropertyValuesCollection($activityRoleProp);
		foreach ($activityroles->getIterator() as $role)
		{
			$roleLabel = '';
			$roleUri = '';
			if($role instanceof core_kernel_classes_Resource){
				$roleLabel = $role->getLabel();
				$roleUri = $role->uriResource;
			}
			if($role instanceof core_kernel_classes_Literal){
				$roleLabel = $role->literal;
				
			}
			$returnValue[]=array($roleUri,trim(strip_tags($roleLabel)));
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  int recursivityLevel
     * @return void
     */
    public function feedFlow($recursivityLevel = "")
    {
        // section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008F4 begin
		// We get the next connectors.
		$nextConnectors = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $this->uri);
		$connectors =array();
		foreach ($nextConnectors->getIterator() as $resource)
		{
			$typeProp = new core_kernel_classes_Property(RDF_TYPE);
			$isAConnector = $resource->getPropertyValuesCollection($typeProp);
			
			if ($isAConnector->get(0)->uriResource == CLASS_CONNECTORS)
			{
						
				$connector	= new Connector($resource->uriResource);
				$this->nextConnectors[] = $connector;		

			}
				
		}
		
		// We get the associated consistency rule.
		// Please be carefull that an activity wihtout any transition rule is absolutely valid.
		$consistencyRulesActivitiesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONSISTENCYRULE);
		$consistencyRules = $this->resource->getPropertyValues($consistencyRulesActivitiesProp);


		if (count($consistencyRules) && $consistencyRules[0] != false) {
			$this->consistencyRule = new ConsistencyRule($consistencyRules[0]);
		}
			
		
		// We get the associated onAfterInferenceRule.
			
		$infRulesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INFERENCERULE);
		$inferenceRules = $this->resource->getPropertyValues($infRulesProp);

		if (count($inferenceRules))
		{
			foreach ($inferenceRules as $inf)
			$this->inferenceRule[] = new InferenceRule($inf);
		}

		// We get the associated onBeforeInferenceRule.
		$infRulesOnBeforeProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE);
		$inferenceRules = $this->resource->getPropertyValues($infRulesOnBeforeProp);

		if (count($inferenceRules))
		{
			foreach ($inferenceRules as $inf)
			$this->onBeforeInferenceRule[] = new InferenceRule($inf);
		}

        // section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008F4 end
    }

    /**
     * Short description of method getServices
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  ActivityExecution execution
     * @return array
     */
    public function getServices( ActivityExecution $execution = null)
    {
        $returnValue = array();

        // section 10-13-1--31-2237f23b:11a39ee89a9:-8000:000000000000098F begin
		$activitiesIserviceProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISERVICES);
		$interactiveServices =  $this->resource->getPropertyValues($activitiesIserviceProp);
		
		$interactiveServicesDescription=array();
		if (sizeOf($interactiveServices)>0)
		{
			if ($interactiveServices[0]!="")
			{
				foreach ($interactiveServices as $interactiveService)
				{
						
					$interactiveServicesDescription[] = new Service($interactiveService,$execution);
				}
			}
		}

		$returnValue = $interactiveServicesDescription;

        // section 10-13-1--31-2237f23b:11a39ee89a9:-8000:000000000000098F end

        return (array) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return void
     */
    public function __construct($uri)
    {
        // section 10-13-1-85-16731180:11be4127421:-8000:00000000000009FA begin
		 
		parent::__construct($uri);
		$this->logger->debug('Build Activity  Name: ' . $this->resource->getLabel(),__FILE__,__LINE__);
		$this->logger->debug('Build Activity  Uri: ' . $this->resource->uriResource,__FILE__,__LINE__);
		if ($feed)
		{
			$activityRoleProp = new core_kernel_classes_Property(ACTIVITY_ROLE);
			$acceptedRole = $this->resource->getPropertyValues($activityRoleProp);
			

			if (isset($acceptedRole[0])) {
				$this->acceptedRole = new WfRole($acceptedRole[0]);
			}
			
			 

			 
			// Hidden
			$isHiddenProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
			$isHidden = $this->resource->getPropertyValues($isHiddenProp);


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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function isFirst()
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A03 begin

		//if (defined('PIAAC_ENABLED') && $this->label == PIAAC_FIRST_ACTIVITY)
		//	return true;
		$isInitialProp = new core_kernel_classes_Property(ACTIVITIES_IS_INITIAL);
		$isInitial = $this->resource->getPropertyValues($isInitialProp);
		

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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function isLast()
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A07 begin

		$nextActivitiesCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PREC_ACTIVITIES,$this->uri);
		
		$returnValue = $nextActivitiesCollection=== null;
        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A07 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getControls
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getControls()
    {
        $returnValue = array();

        // section 127-0-1-1--12200f09:12ce9a71a5e:-8000:00000000000010C7 begin
        
        if(!is_null( $this->resource)){
        	$returnValue = $this->resource->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITY_CONTROL));
        }
        
        // section 127-0-1-1--12200f09:12ce9a71a5e:-8000:00000000000010C7 end

        return (array) $returnValue;
    }

} /* end of class Activity */

?>