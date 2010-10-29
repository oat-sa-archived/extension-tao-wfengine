<?php

error_reporting(E_ALL);

/**
 * Utilities on URL/URI
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}



// section 127-0-1-1-4955a5a0:1242e3739c6:-8000:00000000000019D2-constants end

/**
 * Utilities on URL/URI
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage helpers
 */
class wfEngine_helpers_ProcessUtil
{
    	/**
     * Check if the resource is an activity instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource resource
     * @return boolean
     */	
	public static function isActivity(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		if(!is_null($resource)){
			$returnValue = self::checkType($resource, new core_kernel_classes_Class(CLASS_ACTIVITIES));
		}
		
		return $returnValue;
	}
	
	public static function isActivityInitial(core_kernel_classes_Resource $activity){
	
		$initial = false;
		
		$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
		if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
			if($isIntial->uriResource == GENERIS_TRUE){
				$initial = true;
			}
		}
		
		return $initial;
	}
	
	public static function isActivityFinal(core_kernel_classes_Resource $activity){
	
		$final = false;
		
		$processAuthoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$connectors = $processAuthoringService->getConnectorsByActivity($activity, array('next'));
		if(isset($connectors['next'])){
			$final = empty($connectors['next']);
		}
		
		return $final;
	}
	
	/**
     * Check if the resource is a connector instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource resource
     * @return boolean
     */	
	public static function isConnector(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		if(!is_null($resource)){
			$returnValue = self::checkType($resource, new core_kernel_classes_Class(CLASS_CONNECTORS));
		}
		
		return $returnValue;
	}
	
	public static function checkType(core_kernel_classes_Resource $resource, core_kernel_classes_Class $clazz){
		$returnValue = false;
		
		$type = core_kernel_impl_ApiModelOO::singleton()->getObject($resource->uriResource, RDF_TYPE);
		if($type->count()>0){
			if($type->get(0) instanceof core_kernel_classes_Resource){
				if( $type->get(0)->uriResource == $clazz->uriResource){
					$returnValue = true;
				}
			}
		}
		
		return $returnValue;
	}

} /* end of class wfEngine_helpers_ProcessUtil */

?>