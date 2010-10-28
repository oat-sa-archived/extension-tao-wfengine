<?php

error_reporting(E_ALL);

/**
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
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every service instances.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('tao/models/classes/class.Service.php');

/**
 * The wfEngine_models_classes_ProcessAuthoringService class provides methods to access and edit the process ontology
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_models_classes_ProcessCloningService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---
	
	protected $processUri = '';
	protected $authoringService = null;
	protected $currentActivity = null;		
	protected $clonedActivities = array();
	protected $clonedConnectors = array();
	protected $waitingConnectors = array();
	
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct()
    {
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$this->initCloningVariables();
		parent::__construct();
		
    }
	
	protected function initCloningVariables(){
		$this->currentActivity = null;
		$this->clonedActivities = array();
		$this->clonedConnectors = array();
		$this->waitingConnectors = array();
	}
	
	public function cloneProcess(core_kernel_classes_Resource $process){
		$processClone = $this->cloneWfResource($process, new core_kernel_classes_Class(CLASS_PROCESS), array(PROPERTY_PROCESS_ACTIVITIES, PROPERTY_PROCESS_DIAGRAMDATA));
		
		$this->initCloningVariables();
		
		if(!is_null($processClone)){
			//get all activity processes and clone them:
			$activities = $this->authoringService->getActivitiesByProcess($process);
			foreach($activities as $activityUri => $activity){
				$activityClone = $this->cloneActivity($activity);
				if(!is_null($activityClone)){
					$this->clonedActivities[$activity->uriResource] = $activityClone->uriResource;
					$processClone->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activityClone->uriResource);
				}else{
					throw new Exception("the activity '{$activity->getLabel()}'({$activity->uriResource}) cannot be cloned");
				}
			}
			
			//reloop for connectors this time:
			foreach($activities as $activityUri => $activity){
				$this->currentActivity = $activity;
				$connectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
				foreach($connectors['next'] as $connector){
					$this->cloneConnector($connector);
				}
			}
		}
		
		return $processClone;
	
	}
	
	public function cloneProcessSegment(core_kernel_classes_Resource $process, core_kernel_classes_Resource $startActivity = null, core_kernel_classes_Resource $endActivity = null){
		
		$this->initCloningVariables();
		
		$initialActivity = null;
		$finalActivities = array();
		
		if(is_null($startActivity) && is_null($endActivity)){
			
			
			$activities = $this->authoringService->getActivitiesByProcess($process);
			
			$initialActivity = null;
			//find the first activity:
			foreach($activities as $activityUri => $activity){
				if(wfEngine_helpers_ProcessUtil::isActivityInitial($activity)){
					$initialActivity = $activity;
					break;
				}
			}	
			if(is_null($initialActivity)){
				throw new Exception('no initial activity found to the process');
			}
			
			foreach($activities as $activityUri => $activity){
				$activityClone = $this->cloneActivity($activity);
				if(!is_null($activityClone)){
					$this->clonedActivities[$activity->uriResource] = $activityClone->uriResource;
					$processClone->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activityClone->uriResource);
				}else{
					throw new Exception("the activity '{$activity->getLabel()}'({$activity->getLabel()}) cannot be cloned");
				}
			}
			
			//reloop for connectors this time:
			foreach($activities as $activityUri => $activity){
				$this->currentActivity = $activity;
				$connectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
				if(empty($connectors['next'])){
					//it is a final activity
					$finalActivities[] = $activity;
				}else{
					foreach($connectors['next'] as $connector){
						$this->cloneConnector($connector);
					}
				}
				
			}
		}
		
		if(is_null($initialActivity)){
			throw new Exception('no initial activity found to the defined process segment');
		}
		if(is_null($finalActivities)){
			throw new Exception('no initial activity found to the defined process segment');
		}
		
		return array(
			'initial' => $initialActivity,
			'final' => finalActivities
		);
	}
	
	
	public function cloneActivity(core_kernel_classes_Resource $activity){
		$returnValue = null;
		
		if(wfEngine_models_classes_ProcessAuthoringService::isActivity($activity)){
			$activityClone = $this->cloneWfResource(
				$activity, 
				new core_kernel_classes_Class(CLASS_ACTIVITIES),
				array(
					PROPERTY_ACTIVITIES_INTERACTIVESERVICES,
					PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE,
					PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE,
					PROPERTY_ACTIVITIES_ISERVICES,
					PROPERTY_ACTIVITIES_INFERENCERULE,
					PROPERTY_ACTIVITIES_CONSISTENCYRULE
			));
			
			if(!is_null($activityClone)){
				//clone the interactive service:
				$services = $this->authoringService->getServicesByActivity($activity);
				foreach($services as $service){
					$serviceClone = $this->cloneWfResource($service, new core_kernel_classes_Class(CLASS_CALLOFSERVICES));
					$activityClone->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $serviceClone->uriResource);
				}
				
				//TODO: the related rules, when implementation has been confirmed
				
				$returnValue = $activityClone;
			}
			
		}
		
		return $activityClone;
	}
	
	public function cloneConnector(core_kernel_classes_Resource $connector){
		$returnValue = null;
		
		if(wfEngine_models_classes_ProcessAuthoringService::isConnector($connector)){
			$connectorClone = $this->cloneWfResource(
				$connector, 
				new core_kernel_classes_Class(CLASS_CONNECTORS),
				array(
					PROPERTY_CONNECTORS_TRANSITIONRULE,
					PROPERTY_CONNECTORS_NEXTACTIVITIES,
					PROPERTY_CONNECTORS_ACTIVITYREFERENCE,
					PROPERTY_CONNECTORS_PRECACTIVITIES
			));
			
			//set activity reference:
			$propActivityRef = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
			$oldReferenceActivity = $connector->getUniquePropertyValue($propActivityRef);
			if(isset($this->clonedActivities[$oldReferenceActivity->uriResource])){
				$connectorClone->setPropertyValue($propActivityRef, $this->clonedActivities[$oldReferenceActivity->uriResource]);
			}else{
				throw new Exception("the new activity reference cannot be found among the cloned activities");
			}
			
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if(is_null($connectorType)){
				switch($connectorType->uriResource){
					case INSTANCE_TYPEOFCONNECTORS_SPLIT:{
					
						$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						if(!is_null($transitionRule)){
							//required to recreate the rule:
							$if = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
							$transitionRuleClone = $this->authoringService->createRule($connectorClone, $if->getLabel());
							
							$transitionRuleActivityProperties = array(
								'then' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN),
								'else' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE)
							);
						
							foreach($transitionRuleActivityProperties as $activityType => $connectorActivityProperty){
								$activity = $transitionRule->getOnePropertyValue($connectorActivityProperty);
								if(!is_null($activity)){
									$newPropActivityUri = $this->getNewActivityUriFromOldActivity($activity, $oldReferenceActivity);
									if(!empty($newPropActivityUri)){
										$transitionRuleClone->setPropertyValue($connectorActivityProperty, $newPropActivityUri);
									}
								}
							}
							
						}
						// break;//do not break!
					},
					case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:
					case INSTANCE_TYPEOFCONNECTORS_PARALLEL:
					case INSTANCE_TYPEOFCONNECTORS_JOIN:{
					
						$connectorActivityProperties = array(
							'prev' => new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES),
							'next' => new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES)
						);
						
						foreach($connectorActivityProperties as $activityType => $connectorActivityProperty){
							$activities = $connector->getPropertyValuesCollection($connectorActivityProperty);
							$newPropActivitiesUris = array();
							foreach($activities->getIterator() as $activity){
								if(!is_null($activity)){
									
									$newPropActivitiesUri = $this->getNewActivityUriFromOldActivity($activity, $oldReferenceActivity);
									if(!empty($newPropActivitiesUri)) $newPropActivitiesUris[] = $newPropActivitiesUri;
									
								}
							}
							$connectorClone->editPropertyValues($connectorActivityProperty, $newPropActivitiesUris);
						}
						
						/*
						$previousActivities = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
						$newPrevousActivitiesUris = array();
						foreach($previousActivities->getIterator() as $activity){
							if(!is_null($activity)){
								if(wfEngine_models_classes_ProcessAuthoringService::isActivity($activity)){
									if(isset($this->clonedActivities[$activity->uriResource])){
										$newPrevousActivitiesUris[] = $this->clonedActivities[$activity->uriResource];
									}else{
										//must have been cloned!
										// $this->waitingActivities[] = 
										throw new Exception('the previous activity has not been cloned!');
									}
								}else if(wfEngine_models_classes_ProcessAuthoringService::isConnector($activity)){
									
									if(isset($this->clonedConnectors[$activity->uriResource]){
										//it is a reference to a connector with another activity reference and it has been cloned already
										$newPrevousActivitiesUris[] = $this->clonedConnector[$activity->uriResource];
									}else{
										//not cloned yet:
										//clone it only if the reference id is the current activity
										if($oldReferenceActivity->uriResource == $this->currentActivity->uriResource){
											//recursively clone it
												$nextConnectorClone = $this->cloneConnector($activity);
												if(!is_null($nextConnectorClone)){
													$newPrevousActivitiesUris[] = $nextConnectorClone->uriResource;
												}else{
													throw new Exception("the next connector cannot be cloned");
												}
										}else{
											//put in the waiting list:
											if(!isset($this->waitingConnectors[$activity->uriResource])){
												$this->waitingConnectors[$activity->uriResource] = array('prev', 'next');
											}
											$this->waitingConnectors[$activity->uriResource]['prev'][] = $connector;//always the next activity prop?
										}
									}
								}
								
							}
						}
						$connectorClone->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES), $newPrevousActivitiesUris);
						*/
						
						break;
					}
					
				}
			}
			
			//check if it is in the waiting connector list:
			if(isset($this->waitingConnectors[$connector->uriResource])){
				$activityUris = array();
				foreach($this->waitingConnectors[$connector->uriResource][$activityType] as $activity){
					$activity->setPropertyValue($connectorProperty, $connectorClone->uriResource);
				}
				
				unset($this->waitingConnectors[$connector->uriResource])
			}
			
			$this->clonedConnectors[$connector->uriResource] = $connectorClone->uriResource;
			
		}
		
		return $returnValue;
		
	}
	
	protected function getNewActivityUriFromOldActivity(core_kernel_classes_Resource $activity, core_kernel_classes_Resource $oldReferenceActivity){
		
		$newPropActivitiesUri = '';
		
		if(!is_null($activity) && !is_null($oldReferenceActivity)){
			if(wfEngine_models_classes_ProcessAuthoringService::isActivity($activity)){
				if(isset($this->clonedActivities[$activity->uriResource])){
					$newPropActivitiesUri = $this->clonedActivities[$activity->uriResource];
					//note: works for parallel activity too, where multiple branch is created a parallelized branch
				}else{
					//must have been cloned!
					// $this->waitingActivities[] = 
					throw new Exception('the previous activity has not been cloned!');
				}
			}else if(wfEngine_models_classes_ProcessAuthoringService::isConnector($activity)){
				
				if(isset($this->clonedConnectors[$activity->uriResource]){
					//it is a reference to a connector with another activity reference and it has been cloned already
					$newPropActivitiesUri = $this->clonedConnector[$activity->uriResource];
				}else{
					//not cloned yet:
					//clone it only if the reference id is the current activity
					//OR if the previous activities of a split connector:
					if($oldReferenceActivity->uriResource == $this->currentActivity->uriResource){
						//recursively clone it
							$nextConnectorClone = $this->cloneConnector($activity);
							if(!is_null($nextConnectorClone)){
								$newPropActivitiesUri = $nextConnectorClone->uriResource;
							}else{
								throw new Exception("the next connector cannot be cloned");
							}
					}else{
						//put in the waiting list:
						if(!isset($this->waitingConnectors[$activity->uriResource])){
							$this->waitingConnectors[$activity->uriResource] = array('prev', 'next');
						}
						$this->waitingConnectors[$activity->uriResource][$activityType][] = $connector;//always the next activity prop?
					}
				}
			}
		}
		
		return $newPropActivitiesUri;
	}
	
	protected function cloneWfResource(core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz, $forbiddenProperties = array())
    {
        $returnValue = null;

        
   		$returnValue = $this->createInstance($clazz);
		if(!is_null($returnValue)){
			foreach($clazz->getProperties(true) as $property){
				if(!in_array($property->uriResource, $forbiddenProperties)){
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$returnValue->setPropertyValue($property, $propertyValue);
					}
				}
			}
			$label = $instance->getLabel();
			$cloneLabel = "$label";
			
			$returnValue->setLabel($cloneLabel);
		}
        

        return $returnValue;
    }
	
	
	
} /* end of class wfEngine_models_classes_ProcessAuthoringService */

?>