<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}
/**
 * The wfEngine_models_classes_ProcessFlow class
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_models_classes_ProcessFlow{
    
	protected $jump = 0;
	public $checkedActivities = array();
	public $checkedConnectors = array();
	
	public function resetCheckedResources(){
		$this->checkedActivities = array();
		$this->checkedConnectors = array();
	}
	
	public function getCheckedActivities(){
		//return and ordered array of the sequence of activities that have been checked during the process flow analysis:
		return $this->checkedActivities;
	}
	
	public function findParallelFromActivityBackward(core_kernel_classes_Resource $activity){
	
		$returnValue = null;
		
		//put the activity being searched in an array to prevent searching from it again in case of back connection
		$this->checkedActivities[] = $activity->uriResource;
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$cardinalityClass = new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY);
		
		$activityCardinalities = $cardinalityClass->searchInstances(array(PROPERTY_ACTIVITYCARDINALITY_ACTIVITY => $activity->uriResource), array('like' => false));//note: count()>1 only 
		$nextActivities = array_merge(array($activity->uriResource), array_keys($activityCardinalities));
		$previousConnectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_NEXTACTIVITIES => $nextActivities), array('like' => false));//note: count()>1 only 
		foreach($previousConnectors as $connector){
		
			if(in_array($connector->uriResource, array_keys($this->checkedConnectors))){
				continue;
			}else{
				$this->checkedConnectors[$connector->uriResource] = $connector;
			}
		
			//get the type of the connector:
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if($connectorType instanceof core_kernel_classes_Resource){
				
				switch($connectorType->uriResource){
					case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
						//parallel connector found:
						if($this->jump == 0){
							return $returnValue = $connector;
						}else{
							$this->jump --;
						}
						break;
					}
					case INSTANCE_TYPEOFCONNECTORS_JOIN:{
						//increment the class attribute $this->jump
						$this->jump ++;
					}
				}
			}
			
			//if the wanted parallel connector has not be found (i.e. no value returned so far):
			//get the previousActivityCollection and recursively execute the same function ON ONLY ONE of the previous branches (there would be several branches only in the case of a join, otherwise it should be one anyway:
			$previousActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
			//Note: the use of the property activity reference allow to jump to the "main" (in case of a join connector and successive conditionnal connectors) directly
			
			//if the previousActivity happens to have already been checked, jump it
			if(in_array($previousActivity->uriResource, $this->checkedActivities)){
				continue;
			}else{
				$parallelConnector = $this->findParallelFromActivityBackward($previousActivity);
				if($parallelConnector instanceof core_kernel_classes_Resource){
					//found it:
					if($this->jump != 0){
						throw new Exception('parallel connector returned while the "jump value" is not null ('.$this->jump.')');
					}
					return $returnValue = $parallelConnector;
				}
			}
			
		}
		
		return $returnValue;//null
	}
	
	public function findJoinFromActivityForward(core_kernel_classes_Resource $activity){
	
		$returnValue = null;
		
		//put the activity being searched in an array to prevent searching from it again in case of back connection
		$this->checkedActivities[] = $activity->uriResource;
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$nextConnectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $activity->uriResource), array('like' => false));//note: count()>1 only 
		if(count($nextConnectors)){//there could be only one next connector for an activity
		
			$connector = array_pop($nextConnectors);
			if(in_array($connector->uriResource, array_keys($this->checkedConnectors))){
				continue;
			}else{
				$this->checkedConnectors[$connector->uriResource] = $connector;
			}
		
			//get the type of the connector:
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if($connectorType instanceof core_kernel_classes_Resource){
				
				switch($connectorType->uriResource){
					case INSTANCE_TYPEOFCONNECTORS_JOIN:{
						//parallel connector found:
						if($this->jump == 0){
							return $returnValue = $connector;
						}else{
							$this->jump --;
						}
						break;
					}
					case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
						//increment the class attribute $this->jump
						$this->jump ++;
						break;
					}
				}
			}
			
			$classMultiplicity = new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY);
			$propMultiplicityActivity = new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_ACTIVITY);
			//if the wanted join connector has not be found (i.e. no value returned so far):
			//get the nextActivitiesCollection and recursively execute the same function ON ONLY ONE of the next parallel branch, but both banches in case of a conditionnal connector
			$nextActivitiesCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
			foreach($nextActivitiesCollection->getIterator() as $nextActivity){
				
				if($nextActivity->hasType($classMultiplicity)){
					$activity = $nextActivity->getPropertyValue();
				}
			
				//if the nextActivity happens to have already been checked, jump it
				if(in_array($nextActivity->uriResource, $this->checkedActivities)){
					continue;
				}else{
					$joinConnector = $this->findJoinFromActivityForward($nextActivity);
					if($joinConnector instanceof core_kernel_classes_Resource){
						//found it:
						if($this->jump != 0){
							throw new Exception('parallel connector returned while the "jump value" is not null ('.$this->jump.')');
						}
						return $returnValue = $joinConnector;
					}
				}
			}
			
		}
		
		return $returnValue;//null
	}
	
}
?>