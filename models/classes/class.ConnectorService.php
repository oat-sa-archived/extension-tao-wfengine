<?php

error_reporting(E_ALL);

/**
 * Connector Services
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
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-includes begin
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-includes end

/* user defined constants */
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-constants begin
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-constants end

/**
 * Connector Services
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ConnectorService
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
     * Check if the resource is a connector
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return boolean
     */
    public function isConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBD begin
        if(!is_null($connector)){
            $returnValue = $connector->hasType( new core_kernel_classes_Class(CLASS_CONNECTORS));
        }
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBD end

        return (bool) $returnValue;
    }

    /**
     * retrieve connector nexts activities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return array
     */
    public function getNextActivities( core_kernel_classes_Resource $connector)
    {
        $returnValue = array();

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EC5 begin
        $nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES, __METHOD__);
        $nextActivities = $connector->getPropertyValues($nextActivitiesProp);
		$count = count($nextActivities);
		for($i=0;$i<$count;$i++){
			if(common_Utils::isUri($nextActivities[$i])){
				$returnValue[] = new core_kernel_classes_Resource($nextActivities[$i]);
			}
		}
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EC5 end

        return (array) $returnValue;
    }

    /**
     * retrieve connector previous activities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return array
     */
    public function getPreviousActivities( core_kernel_classes_Resource $connector)
    {
        $returnValue = array();

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECB begin
        $prevActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES,__METHOD__);
        $prevActivities = $connector->getPropertyValues($prevActivitiesProp);
		$count = count($prevActivities);
		for($i=0;$i<$count;$i++){
			if(common_Utils::isUri($prevActivities[$i])){
				$returnValue[] = new core_kernel_classes_Resource($prevActivities[$i]);
			}
		}
		
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECB end

        return (array) $returnValue;
    }

    /**
     * retrive type of Connector Conditionnal, Sequestionnal Parallele...
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    public function getType( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECF begin
       	$connTypeProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE);
       	try{
       	    $returnValue = $connector->getUniquePropertyValue($connTypeProp);
       	}
       	catch (common_Exception $e) {
			throw new wfEngine_models_classes_ProcessDefinitonException('Exception when retreiving connector type ' . $connector->uriResource);
       	}
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECF end

        return $returnValue;
    }

    /**
     * Short description of method getTransitionRule
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    public function getTransitionRule( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED3 begin
        $ruleProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE);
        $returnValue = $connector->getOnePropertyValue($ruleProp);
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED3 end

        return $returnValue;
    }

    /**
     * Short description of method deleteConnector
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return boolean
     */
    public function deleteConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:000000000000307C begin
		
		$cardinalityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityCardinalityService');
		
		if(!$this->isConnector($connector)){
			// throw new Exception("the resource in the parameter is not a connector: {$connector->getLabel()} ({$connector->uriResource})");
			return $returnValue;
		}
		
		//get the type of connector:
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
		if(!is_null($connectorType) && $connectorType instanceof core_kernel_classes_Resource){
			if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_CONDITIONAL){
				//delete the related rule:
				$relatedRule = $this->getTransitionRule($connector);
				if(!is_null($relatedRule)){
					$processAuthoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
					$processAuthoringService->deleteRule($relatedRule);
				}
			}
		}
		
		//delete cardinality resources if exists in previous activities:
		foreach($this->getPreviousActivities($connector) as $prevActivity){
			if($cardinalityService->isCardinality($prevActivity)){
				$prevActivity->delete();//delete the cardinality resource
			}
		}
		
		//manage the connection to the following activities
		$activityRef = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
		foreach($this->getNextActivities($connector) as $nextActivity){
			
			$activity = null;
			
			if($cardinalityService->isCardinality($nextActivity)){
				try{
				$activity = $cardinalityService->getActivity($nextActivity);
				}catch(Exception $e){
					//the actiivty could be null if the reference have been removed...
				}
				
				$nextActivity->delete();//delete the cardinality resource
			}else{
				$activity = $nextActivity;
			}
			
			if(!is_null($activity) && $this->isConnector($activity)){
				$nextActivityRef = $activity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
				if($nextActivityRef == $activityRef){
					$this->deleteConnector($activity);//delete following connectors only if they have the same activity reference
				}
			}
		}
		
		//delete connector itself:
		$returnValue = $connector->delete(true);
		
        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:000000000000307C end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ConnectorService */

?>