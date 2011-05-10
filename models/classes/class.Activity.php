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


/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000820-constants end

/**
 * Short description of class wfEngine_models_classes_Activity
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
class wfEngine_models_classes_Activity
    extends wfEngine_models_classes_WfResource
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



	public $isHidden = false;
	
    // --- OPERATIONS ---

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
		$connectorsClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$nextConnectors = $connectorsClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $this->uri), array('like' => true, 'recursive' => false));
		
		foreach ($nextConnectors as $nextConnector){
			$connector	= new wfEngine_models_classes_Connector($nextConnector->uriResource);
			$this->nextConnectors[] = $connector;	
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
    public function getServices( wfEngine_models_classes_ActivityExecution $execution = null)
    {
        $returnValue = array();

        // section 10-13-1--31-2237f23b:11a39ee89a9:-8000:000000000000098F begin
		$activitiesIserviceProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES);
		$interactiveServices =  $this->resource->getPropertyValues($activitiesIserviceProp);
		
		$interactiveServicesDescription=array();
		if (sizeOf($interactiveServices)>0)
		{
			if ($interactiveServices[0]!="")
			{
				foreach ($interactiveServices as $interactiveService)
				{
					$interactiveServicesDescription[] = new wfEngine_models_classes_InteractiveService($interactiveService,$execution);
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
    public function __construct($uri, $feed = true)
    {
        // section 10-13-1-85-16731180:11be4127421:-8000:00000000000009FA begin
		 
		parent::__construct($uri);
		$this->logger->debug('Build Activity  Name: ' . $this->resource->getLabel(),__FILE__,__LINE__);
		$this->logger->debug('Build Activity  Uri: ' . $this->resource->uriResource,__FILE__,__LINE__);
		
		if ($feed)
		{
			 
			// Hidden
			$isHiddenProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
			$isHidden = $this->resource->getPropertyValues($isHiddenProp);
			
			$this->isHidden = false;
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

		$isInitialProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
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
		$activityClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
		$nextActivities = $activityClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $this->uri), array('like' => true, 'recursive' => false));
		$returnValue = (bool) count($nextActivities);
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
        	
        	$returnValue = $this->resource->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONTROLS));
        
       		if($this->isFirst() && isset($returnValue[INSTANCE_CONTROL_BACKWARD])){
        		unset($returnValue[INSTANCE_CONTROL_BACKWARD]);
        	}
        	
        }
        
        // section 127-0-1-1--12200f09:12ce9a71a5e:-8000:00000000000010C7 end

        return (array) $returnValue;
    }

} /* end of class Activity */

?>