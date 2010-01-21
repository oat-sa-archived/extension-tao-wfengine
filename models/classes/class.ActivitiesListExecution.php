<?php

error_reporting(E_ALL);

/**
 * @author Lionel Lionel Lecaque lionel.lecaque@tudor.lu
 *
 */
class ActivitiesListExecution extends ActivitiesList {
	
	public $restored = false;
	/**
	 * @param $list
	 * @return boolean
	 */
	public function setRemaining(core_kernel_classes_RdfList $list) {		
		$remainListProp = new core_kernel_classes_Property(PROPERTY_REMAINING_ACTIVITIES,__METHOD__);
		return $this->resource->editPropertyValues($remainListProp,$list->uriResource);
	}
	
	/**
	 * @return core_kernel_classes_RdfList
	 */
	public function getRemaining(){
		$remainListProp = new core_kernel_classes_Property(PROPERTY_REMAINING_ACTIVITIES,__METHOD__);
		try {	

			$collection = $this->resource->getPropertyValuesCollection($remainListProp);
			if($collection->count() != 1) {
				return new core_kernel_classes_RdfList(RDF_NIL);
			}
			else {
				return new core_kernel_classes_RdfList($collection->get(0)->uriResource);						
			}

			
		}				
		catch (common_Exception $e) {
			echo 'Exception when retreiving Activities List data ' . $this->uri;			
			var_dump($e->getMessage(), $e->getTraceAsString());			
		}	
	}
	
	public function setRemainingArray($list) {	
		$remainListProp = new core_kernel_classes_Property(PROPERTY_REMAINING_ACTIVITIES_ARRAY,__METHOD__);
		return $this->resource->editPropertyValues($remainListProp, $list);
	}	
	
	public function getRemainingArray() {	
		$remainListProp = new core_kernel_classes_Property(PROPERTY_REMAINING_ACTIVITIES_ARRAY,__METHOD__);
		$collection = $this->resource->getPropertyValuesCollection($remainListProp);
			if($collection->count() != 1) {
				return false;
			}
			else {
				return $collection->get(0)->literal;
			}
		
	}
	
	public function isUp() {		
		$upProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST_EXECUTION_UP ,__METHOD__);
		return $this->resource->getUniquePropertyValue($upProp)->uriResource == GENERIS_TRUE ;
	}
	
	public function setUp($up) {		
		$upProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST_EXECUTION_UP ,__METHOD__);
		$value = $up ? GENERIS_TRUE : GENERIS_FALSE;
		$this->resource->editPropertyValues($upProp,$value);
	}
	
	public function isFinished() {		
		$dProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST_EXECUTION_ISFINISHED ,__METHOD__);
		return $this->resource->getUniquePropertyValue($dProp)->uriResource == GENERIS_TRUE ;
	}
	
	public function setFinished($d) {		
		$dProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST_EXECUTION_ISFINISHED ,__METHOD__);
		$value = $d ? GENERIS_TRUE : GENERIS_FALSE;
		$this->resource->editPropertyValues($dProp,$value);
	}
}
?>