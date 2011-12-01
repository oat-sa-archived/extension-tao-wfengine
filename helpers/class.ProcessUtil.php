<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/class.ProcessUtil.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.08.2011, 14:59:16 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource resource
     * @param  Class clazz
     * @return boolean
     */
    public static function checkType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $clazz)
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
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  string url
     * @return core_kernel_classes_Resource
     */
    public static function getServiceDefinition($url)
    {
        $returnValue = null;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF2 begin
		$serviceClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
		$services = $serviceClass->searchInstances(array(PROPERTY_SUPPORTSERVICES_URL => $url), array('like' => true, 'recursive' => 1000));
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
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isActivity( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF5 begin
		if(!is_null($resource)){
			$returnValue = $resource->hasType( new core_kernel_classes_Class(CLASS_ACTIVITIES));
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isActivityFinal
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public static function isActivityFinal( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF8 begin
		$processAuthoringService = wfEngine_models_classes_ProcessAuthoringService::singleton();
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
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public static function isActivityInitial( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFB begin
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isConnector
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isConnector( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFE begin
		if(!is_null($resource)){
			$returnValue = $resource->hasType( new core_kernel_classes_Class(CLASS_CONNECTORS));
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFE end

        return (bool) $returnValue;
    }

    /**
     * Organize Process Variable into an array
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  array variables
     * @return array
     */
    public static function processVarsToArray($variables)
    {
        $returnValue = array();

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E9E begin
        foreach ($variables as $var) {
            $returnValue[$var->uri] = $var->value;
        }
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E9E end

        return (array) $returnValue;
    }

} /* end of class wfEngine_helpers_ProcessUtil */

?>