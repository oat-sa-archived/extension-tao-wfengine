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
		$logger = new common_Logger('Sevice API SAVE',Logger::debug_level);
		
		
		if(isset($_SESSION["processUri"])){
			$processUri = $_SESSION["processUri"];
			$process = new core_kernel_classes_Resource(urldecode($processUri));
			foreach($variable as $k=>$v) {
				$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE,$k);
				$logger->debug('Searching code for ' . $k ,__FILE__,__LINE__);
				if(!$collection->isEmpty()){
					if($collection->count() == 1) {
						$property = new core_kernel_classes_Property($collection->get(0)->uriResource);
						$logger->debug('Pocess ' . $processUri . '|'.$k . '|'. $v  ,__FILE__,__LINE__);
						return $process->editPropertyValues($property,$v);
					}
					$logger->debug('Found more than one prop for ' . $k ,__FILE__,__LINE__);
					
				}
				$logger->debug('code prop not found for ' . $k ,__FILE__,__LINE__);
			}

		}
		return false;
	}
	
}