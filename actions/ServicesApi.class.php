<?php

class ServiceApi extends Module
{


	/**
	 * @param $variable
	 * @return true
	 */
	public function save($variable){
		if(isset($_SESSION["processUri"])){
			$processUri = $_SESSION["processUri"];
			$process = new core_kernel_classes_Resource($processUri);
			foreach($variable as $k=>$v) {
				$collection = core_kernel_classes_ApiModelOO::singleton()->getSubject(PROPERTY_CODE,$k);
				if(!$collection->isEmpty()){
					if($collection->count() == 1) {
						$property = core_kernel_classes_Property($collection->get(0)->uriResource);
						return $process->setPropertyValue($property,$v);
					}
					
				}
			}

		}
		return false;
	}
	
}