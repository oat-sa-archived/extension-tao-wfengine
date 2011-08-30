<?php

error_reporting(E_ALL);

/**
 * Service that retrieve information about Activty definition during runtime
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance.
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-includes begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-includes end

/* user defined constants */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-constants begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E82-constants end

/**
 * Service that retrieve information about Activty definition during runtime
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ActivityService
extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * indicate if the activity need back and forth controls
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getControls( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E84 begin
        if(!is_null($activity)){
             
            $returnValue = $activity->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONTROLS));

            if($this->isInitial($activity) && isset($returnValue[INSTANCE_CONTROL_BACKWARD])){
                unset($returnValue[INSTANCE_CONTROL_BACKWARD]);
            }
             
        }
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E84 end

        return (array) $returnValue;
    }

    /**
     * retrieve the Interactive service associate to the Activity
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getInteractiveServices( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E92 begin
        $activitiesIserviceProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES);
        $interactiveServices = $activity->getPropertyValues($activitiesIserviceProp);
        if (sizeOf($interactiveServices)>0) {
            if ($interactiveServices[0]!="") {
                foreach ($interactiveServices as $interactiveService) {
                    $returnValue[$interactiveService] =  new core_kernel_classes_Resource($interactiveService);
                }
            }
        }

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E92 end

        return (array) $returnValue;
    }

    /**
     * Check if the activity is initial
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isInitial( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA3 begin
        $isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
        if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
            if($isIntial->uriResource == GENERIS_TRUE){
                $returnValue = true;
            }
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA3 end

        return (bool) $returnValue;
    }

    /**
     * check if activity is final
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isFinal( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA7 begin
        $connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
        $nextActivities = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $activity->uriResource), array('like' => true, 'recursive' => 0));
        if(count($nextActivities) == 0){
            $returnValue = true;
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA7 end

        return (bool) $returnValue;
    }

    /**
     * get activity's next connector
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getNextConnectors( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EAB begin
        $connectorsClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
        $nextConnectors = $connectorsClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $activity->uriResource), array('like' => true, 'recursive' => 0));
        $connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
        foreach ($nextConnectors as $nextConnector){
            if($connectorService->isConnector($nextConnector)){
                $returnValue[$nextConnector->uriResource] = $nextConnector;
            }
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EAB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isActivity
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EB8 begin
        if(!is_null($activity)){
            $returnValue = $activity->hasType( new core_kernel_classes_Class(CLASS_ACTIVITIES));
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EB8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isHidden
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isHidden( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EBE begin
        $propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
        $hidden = $activity->getOnePropertyValue($propHidden);
        if(!is_null($hidden) && $hidden instanceof core_kernel_classes_Resource){
            if($hidden->uriResource == GENERIS_TRUE){
                $returnValue = true;
            }
        }
        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EBE end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityService */

?>