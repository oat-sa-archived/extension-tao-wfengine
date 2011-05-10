<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine\helpers\class.ProcessUtil.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 22.02.2011, 10:59:47 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package wfEngine
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-includes begin
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-includes end

/* user defined constants */
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-constants begin
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-constants end

/**
 * Short description of class wfEngine_helpers_ProcessUtil
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package wfEngine
 * @subpackage helpers
 */
class wfEngine_helpers_ProcessUtil
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method checkType
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource resource
     * @param  Class clazz
     * @return boolean
     */
    public function checkType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEB begin
		if(!is_null($resource) && !is_null($clazz)){	
			foreach($resource->getType() as $type){
				if($type instanceof core_kernel_classes_Class){
					if( $type->uriResource == $clazz->uriResource){
						$returnValue = true;
						break;
					}
				}
			}
			
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getServiceDefinition
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  string url
     * @return core_kernel_classes_Resource
     */
    public function getServiceDefinition($url)
    {
        $returnValue = null;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF2 begin
		$serviceClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
		$services = $serviceClass->searchInstances(array(PROPERTY_SUPPORTSERVICES_URL => $url), array('like' => true, 'recursive' => true));
		if(count($services)){
			$service = array_pop($services);
			if($service instanceof core_kernel_classes_Resource){
				$returnValue = $service;
			}
		}	
        
		// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF2 end

        return $returnValue;
    }

    /**
     * Short description of method isActivity
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource resource
     * @return boolean
     */
    public function isActivity( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF5 begin
		if(!is_null($resource)){
			$returnValue = self::checkType($resource, new core_kernel_classes_Class(CLASS_ACTIVITIES));
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isActivityFinal
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource activity
     * @return boolean
     */
    public function isActivityFinal( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF8 begin
		$processAuthoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$connectors = $processAuthoringService->getConnectorsByActivity($activity, array('next'));
		if(isset($connectors['next'])){
			$returnValue = empty($connectors['next']);
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isActivityInitial
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource activity
     * @return boolean
     */
    public function isActivityInitial( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFB begin
		$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
		if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
			if($isIntial->uriResource == GENERIS_TRUE){
				$returnValue = true;
			}
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isConnector
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource resource
     * @return boolean
     */
    public function isConnector( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFE begin
		if(!is_null($resource)){
			$returnValue = self::checkType($resource, new core_kernel_classes_Class(CLASS_CONNECTORS));
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFE end

        return (bool) $returnValue;
    }
    
	/**
     * Short description of method getConnectorNextActivities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return array
     */
    public function getConnectorNextActivities( core_kernel_classes_Resource $connector)
    {
        $returnValue = array();

        // section 10-13-1--128--62984630:12fd95c837b:-8000:0000000000002E3E begin
        // section 10-13-1--128--62984630:12fd95c837b:-8000:0000000000002E3E end

        return (array) $returnValue;
    }

} /* end of class wfEngine_helpers_ProcessUtil */

?>