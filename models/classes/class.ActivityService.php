<?php

error_reporting(E_ALL);

/**
 * Service that retrieve information about Activty definition during runtime
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/**
 * include tao_models_classes_ServiceCacheInterface
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/classes/interface.ServiceCacheInterface.php');

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
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ActivityService
    extends tao_models_classes_GenerisService
        implements tao_models_classes_ServiceCacheInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @param  array value
     * @return boolean
     */
    public function setCache($methodName, $args, $value)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB begin
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return mixed
     */
    public function getCache($methodName, $args = array())
    {
        $returnValue = null;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 begin
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 end

        return $returnValue;
    }

    /**
     * Short description of method clearCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return boolean
     */
    public function clearCache($methodName = '', $args = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 begin
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 end

        return (bool) $returnValue;
    }

    /**
     * indicate if the activity need back and forth controls
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getControls( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E84 begin
        if(!is_null($activity)){
            $possibleValues = array( INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD ); 
            $propValues = $activity->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONTROLS));
            foreach ($propValues as $value) {
                if(in_array($value, $possibleValues)){
                    $returnValue[$value] = true;
                }
            }
            if($this->isInitial($activity) && isset($returnValue[INSTANCE_CONTROL_BACKWARD])){
                $returnValue[INSTANCE_CONTROL_BACKWARD] = false ;
            }
             
        }
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E84 end

        return (array) $returnValue;
    }

    /**
     * retrieve the Interactive service associate to the Activity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getInteractiveServices( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E92 begin
        
		$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
		foreach($services->getIterator() as $service){
			if($service instanceof core_kernel_classes_Resource){
				$returnValue[$service->uriResource] = $service;
			}
		}

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E92 end

        return (array) $returnValue;
    }

    /**
     * Check if the activity is initial
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function isFinal( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA7 begin
        $nextConnectors = $this->getNextConnectors($activity);
        if(count($nextConnectors) == 0){
            $returnValue = true;
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EA7 end

        return (bool) $returnValue;
    }

    /**
     * get activity's next connector
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getNextConnectors( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EAB begin
		
		//to be cached!!
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$cardinalityClass = new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY);
		$activityCardinalities = $cardinalityClass->searchInstances(array(PROPERTY_ACTIVITYCARDINALITY_ACTIVITY => $activity->uriResource), array('like' => false));//note: count()>1 only 
		$previousActivities = array_merge(array($activity->uriResource), array_keys($activityCardinalities));
        $nextConnectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $previousActivities), array('like' => true, 'recursive' => 0));
        foreach ($nextConnectors as $nextConnector){
			$returnValue[$nextConnector->uriResource] = $nextConnector;
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EAB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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

    /**
     * Short description of method getUniqueNextConnector
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function getUniqueNextConnector( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F84 begin
		
		$connectors = $this->getNextConnectors($activity);
		$countConnectors = count($connectors);
		
		if($countConnectors > 1){
			//there might be a join connector among them or an issue
			$connectorsTmp = array();
			foreach ($connectors as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				//drop the connector join for now 
				//(a join connector is considered only when it is only one found, i.e. the "else" case below)
				if($connectorType->uriResource != INSTANCE_TYPEOFCONNECTORS_JOIN){
					$connectorsTmp[] = $connector;
				}else{
					//warning: join connector:
					$connectorsTmp = array($connector);
					break;
				}
			}
			
			if(count($connectorsTmp) == 1){
				//ok, the unique next connector has been found
				$returnValue = $connectorsTmp[0];
			}
		}else if($countConnectors == 1){
			$returnValue = array_shift($connectors);
		}else{
			//it is the final activity
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F84 end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityService */

?>