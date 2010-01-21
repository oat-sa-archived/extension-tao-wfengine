<?php

error_reporting(E_ALL);

/**
 * @author Lionel Lionel Lecaque lionel.lecaque@tudor.lu
 *
 */
class ActivitiesList extends Activity {
	

	
	/**
	 * @param $resource
	 * @param $feed
	 * @return ActivitiesList
	 */
	public function __construct(core_kernel_classes_Resource $resource, $feed = true){
		parent::__construct($resource->uriResource);
		$this->resource = $resource;
		
	}
	
	/**
	 * @return core_kernel_classes_Resource
	 */
	public function getSelector() {		
		$selectorProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_SELECTOR,__METHOD__);
		try {											
			return  $this->resource->getUniquePropertyValue($selectorProp);		
		}			
		catch (common_Exception $e) {
			echo 'Exception when retreiving Connector data ' . $this->uri;			
			var_dump($e->getMessage(), $e->getTraceAsString());	
		}			
	}
	/**
	 * @param $selector
	 * @return boolean
	 */
	public function setSelector(core_kernel_classes_Resource $selector) {		
		$selectorProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_SELECTOR,__METHOD__);
		return $this->resource->editPropertyValues($selectorProp,$selector->uriResource);
	}
	
	/**
	 * @param $list
	 * @return boolean
	 */
	public function setRdfList(core_kernel_classes_RdfList $list) {		
		$rdfListProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST,__METHOD__);
		return $this->resource->editPropertyValues($rdfListProp,$list->uriResource);
	}
	
	/**
	 * @return core_kernel_classes_RdfList
	 */
	public function getRdfList(){
		$rdfListProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST,__METHOD__);
		try {					
			$rdfList = $this->resource->getUniquePropertyValue($rdfListProp);	
			return new core_kernel_classes_RdfList($rdfList->uriResource);						
		}				
		catch (common_Exception $e) {
			echo 'Exception when retreiving Activities List data ' . $this->uri;			
			var_dump($e->getMessage(), $e->getTraceAsString());			
		}	
	}
	

	/**
	 * @return unknown_type
	 */
	public function getParent(){
		$parentProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST_PARENT,__METHOD__);
					
		$parentCollection = $this->resource->getPropertyValuesCollection($parentProp);	
		if($parentCollection->isEmpty()){
			return null;
		}
		return new ActivitiesList($parentCollection->get(0));				
		
	}
	
	
	public function getCode() {
		$codeProp = new core_kernel_classes_Property(PROPERTY_CODE);
		return $this->resource->getUniquePropertyValue($codeProp);
	}
	
	/**
	 * @param $list
	 * @return unknown_type
	 */
	public function setParent(ActivitiesList $list){
		$parentProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_LIST_PARENT,__METHOD__);	
		$this->resource->editPropertyValues($parentProp,$list->resource->uriResource);
	}
	
	/**
	 * @param $remainning
	 * @return unknown_type
	 */
	public function createExecution(core_kernel_classes_RdfList $remainning) {
		$class = new core_kernel_classes_Class(CLASS_ACTIVITIES_LIST_EXECUTION);
		$label = 'Execution of ' . $this->resource->getLabel();
		$comment = 'Execution of ' . $this->resource->comment;
		$returnValueIns = core_kernel_classes_ResourceFactory::create($class,$label, $comment);
		$remainingActivitiesProp = new core_kernel_classes_Property(PROPERTY_REMAINING_ACTIVITIES);
		$returnValueIns->setPropertyValue($remainingActivitiesProp,$remainning->uriResource);
		$codeProp = new core_kernel_classes_Property(PROPERTY_CODE);
		$returnValueIns->setPropertyValue($codeProp,'exec'.$this->getCode());
		$returnValue = new ActivitiesListExecution($returnValueIns);
		$returnValue->setRdfList($this->getRdfList());
		$returnValue->setSelector($this->getSelector());
		$returnValue->setParent($this);
		$returnValue->setFinished(false);
		$returnValue->setUp(false);
		return $returnValue;
	}
	
}
?>