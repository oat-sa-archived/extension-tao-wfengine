<?php

if (!defined('PROPERTY_CODE')) {
	define('PROPERTY_CODE', 'http://www.tao.lu/middleware/taoqual.rdf#code');
} 


class ServiceApi extends Module
{


	/**
	 * @param $variable
	 * @return true
	 */
	public static function save($variable){
		
		$returnValue = false;
		
		$logger = new common_Logger('Sevice API SAVE',Logger::debug_level);
		
		
		if(Session::hasAttribute("processUri")){
			$process = new core_kernel_classes_Resource(Session::getAttribute("processUri"));
			
			
			foreach($variable as $k=>$v) {
				$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE,$k);
				$logger->debug('Searching code for ' . $k ,__FILE__,__LINE__);
				if(!$collection->isEmpty()){
					if($collection->count() == 1) {
						$property = new core_kernel_classes_Property($collection->get(0)->uriResource);
						// $logger->debug('Pocess ' . $processUri . '|'.$k . '|'. $v  ,__FILE__,__LINE__);
						$process->editPropertyValues($property,$v);
					}
					$logger->debug('Found more than one prop for ' . $k ,__FILE__,__LINE__);
					
				}
				$logger->debug('code prop not found for ' . $k ,__FILE__,__LINE__);
			}

		}
		return $returnValue;
	}
	
	/**
	 * Service persitance remover 
	 * @param mixed $params the list of param keys you want to remove
	 * @return boolean
	 */
	public static function remove($params){
		
		if(Session::hasAttribute("processUri")){
			$process = new core_kernel_classes_Resource(Session::getAttribute("processUri"));
			if(is_string($params)){
				$params = array($params);
			}
			if(is_array($params)){
				foreach($params as $param) {
					$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $param);
					if(!$collection->isEmpty()){
						if($collection->count() == 1) {
							$property = new core_kernel_classes_Property($collection->get(0)->uriResource);
							// $apiModel->removeStatement($subjectCollection->get(0)->uriResource, $property->uriResource, $object->uriResource, '');
							return $process->removePropertyValues($property);
						}
					}
				}
			}
		}
	}
	
}