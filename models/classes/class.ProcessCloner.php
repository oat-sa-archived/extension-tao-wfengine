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
class wfEngine_models_classes_ProcessCloner
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---
	
	protected $authoringService = null;
	protected $currentActivity = null;
	protected $clonedProcess = null;
	protected $cloneLabel = '';	
	protected $clonedActivities = array();
	protected $clonedConnectors = array();
	protected $waitingConnectors = array();
	public $debugClonedActivities = array();
	public $debugClonedConnectors = array();
	
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct($cloneLabel='')
    {
		$this->cloneLabel = $cloneLabel;
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$this->initCloningVariables();
		parent::__construct();
		
    }
	
	public function setCloneLabel($cloneLabel=''){
		$this->cloneLabel = $cloneLabel;
	}
	
	public function getCloneLabel(){
		return $this->cloneLabel;
	}
	
	protected function initCloningVariables(){
		$this->currentActivity = null;
		$this->clonedProcess = null;
		$this->clonedActivities = array();
		$this->clonedConnectors = array();
		$this->waitingConnectors = array();
	}
	
	private function setDebugClonedActivities(core_kernel_classes_Resource $activity){
		if(!is_null($activity))
		$this->debugClonedActivities[$activity->uriResource] = $activity->getLabel();
	}
	
	public function addClonedActivity(core_kernel_classes_Resource $oldActivity = null, core_kernel_classes_Resource $newActivityIn, $newActivityOut = null){
		
		if(is_null($newActivityOut)) $newActivityOut = $newActivityIn;
		
		if(!is_null($oldActivity)){
			//set the in:
			$this->clonedActivities[$oldActivity->uriResource]['in'] = $newActivityIn->uriResource;
			//debug:
			$this->setDebugClonedActivities($oldActivity);
				
			//set the out:
			if($newActivityOut instanceof core_kernel_classes_Resource){
			
				$this->clonedActivities[$oldActivity->uriResource]['out'] = $newActivityOut->uriResource;
				//debug:
				$this->setDebugClonedActivities($newActivityOut);
				
			}else if(is_array($newActivityOut)){
				//debug
				$this->clonedActivities[$oldActivity->uriResource]['out'] = array();
				foreach($newActivityOut as $aNewActivityOut){
					if($aNewActivityOut instanceof core_kernel_classes_Resource) {
						$this->clonedActivities[$oldActivity->uriResource]['out'][] = $aNewActivityOut->uriResource;
						$this->setDebugClonedActivities($aNewActivityOut);
					}
				}
				
			}
		}else{
			$this->clonedActivities[] = $newActivityIn->uriResource;
		}
		
		$this->setDebugClonedActivities($newActivityIn);
	}
	
	//return an activity resource or an array of activity resources
	public function getClonedActivity(core_kernel_classes_Resource $oldActivity, $InOut ='in'){
		$returnValue = null;
		
		$InOut = strtolower($InOut);
		if(in_array($InOut, array('in', 'out')) && isset($this->clonedActivities[$oldActivity->uriResource])){
			if(isset($this->clonedActivities[$oldActivity->uriResource][$InOut])){
				$activities = $this->clonedActivities[$oldActivity->uriResource][$InOut];
				if(is_array($activities)){
					$returnValue = array();
					foreach($activities as $activityUri){
						$returnValue[] = new core_kernel_classes_Resource($activityUri);
					}
				}else if(is_string($activities)){
					$returnValue = new core_kernel_classes_Resource($activities);
				}
				else{
					throw new Exception("unkown type in getClonedActivity array ({$activities})");
				}
				
			}
		}
		
		return $returnValue;
		
	}
	
	public function getClonedActivities(){
		$returnValue = array();
		
		foreach($this->clonedActivities as $newActivityIO){
			if(is_array($newActivityIO)){
				foreach(array('in', 'out') as $interface){
					$activity = new core_kernel_classes_Resource($newActivityIO[$interface]);
					if(wfEngine_helpers_ProcessUtil::isActivity($activity)){
						$returnValue[$activity->uriResource] = $activity;
					}
				}
			}else if(is_string($newActivityIO)){
				$activity = new core_kernel_classes_Resource($newActivityIO);
				if(wfEngine_helpers_ProcessUtil::isActivity($activity)){
					$returnValue[$activity->uriResource] = $activity;
				}
				
			}
			
		}
		
		return $returnValue;
	}
	
	public function addClonedConnector(core_kernel_classes_Resource $oldConnector, core_kernel_classes_Resource $newConnector){
		$this->clonedConnectors[$oldConnector->uriResource] = $newConnector->uriResource;
	}
	
	public function getClonedConnector(core_kernel_classes_Resource $oldConnector){
		$returnValue = null;
		
		if(isset($this->clonedConnectors[$oldConnector->uriResource])){
			$returnValue = new core_kernel_classes_Resource($this->clonedConnectors[$oldConnector->uriResource]);
		}
		
		return $returnValue;
	}
	
	public function getClonedConnectors(){
		$returnValue = array();
		
		foreach($this->clonedConnectors as $connectorUri){
			$connector = new core_kernel_classes_Resource($connectorUri);
			if(wfEngine_helpers_ProcessUtil::isConnector($connector)){
				$returnValue[$connector->uriResource] = $connector;
			}
		}
		
		return $returnValue;
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
					$this->addClonedActivity($activity, $activityClone);
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
			
			if(!empty($this->waitingConnectors)){
				//update the remaing connectors:
				foreach($this->clonedConnectors as $oldConnectorUri => $newConnectorUri){
					$this->updateWaitingConnector(new core_kernel_classes_Resource($oldConnectorUri), new core_kernel_classes_Resource($newConnectorUri));
				}
			}
				
			$this->clonedProcess = $processClone;
		}
		
		return $processClone;
	
	}
	
	//TODO: return the new activity as an array
	public function cloneProcessSegment(core_kernel_classes_Resource $process, $addTransitionalActivity=false, core_kernel_classes_Resource $startActivity = null, core_kernel_classes_Resource $endActivity = null){
		
		// $this->initCloningVariables();
		
		$initialActivity = null;
		
		$newInitialActivity = null;
		$newFinalActivities = array();
		
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
				if($activity->uriResource == $initialActivity->uriResource){
					$newInitialActivity = $activityClone;
				}
				
				if(!is_null($activityClone)){
					$this->addClonedActivity($activity, $activityClone);
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
					
					$clonedActivitiesOut = $this->getClonedActivity($activity, 'out');
					if(is_array($clonedActivitiesOut)){
						foreach($clonedActivitiesOut as $clonedActivityOut){
							if($clonedActivityOut instanceof core_kernel_classes_Resource){
								$newFinalActivities[] = $clonedActivityOut;
							}
						}
					}else if($clonedActivitiesOut instanceof core_kernel_classes_Resource){
						$newFinalActivities[] = $clonedActivitiesOut;
					}
				}else{
					foreach($connectors['next'] as $connector){
						
						$this->cloneConnector($connector);
					}
				}
			}
			
			if(!empty($this->waitingConnectors)){
				//update the remaing connectors:
				foreach($this->clonedConnectors as $oldConnectorUri => $newConnectorUri){
					$this->updateWaitingConnector(new core_kernel_classes_Resource($oldConnectorUri), new core_kernel_classes_Resource($newConnectorUri));
				}
			}
		}
		
		if(is_null($initialActivity)){
			throw new Exception('no initial activity found to the defined process segment');
		}
		if(is_null($newFinalActivities)){
			//TODO: check that every connector has a following activity
			throw new Exception('no terminal activity found to the defined process segment');
		}
		
		if($addTransitionalActivity){
			//echo "adding transitionnal actiivties";
			//init the required properties:
			$propInitial = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
			$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
			$activityClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
			
			//build the $firstActivity:
			$firstActivity = $activityClass->createInstance("process_start ({$process->getLabel()})", "created by ProcessCloner.Class");
			$firstActivity->editPropertyValues($propInitial, GENERIS_TRUE);//do set it here, the property will be modified automatically by create "following" activity
			$firstActivity->editPropertyValues($propHidden, GENERIS_TRUE);
			$connector = $this->authoringService->createConnector($firstActivity);
			
			//get the clone of the intiial acitivty:
			if(is_null($newInitialActivity)){
				throw new Exception("the intial activity has not been cloned: {$initialActivity->getLabel()}({$initialActivity->uriResource})");
			}
			$this->authoringService->createSequenceActivity($connector, $newInitialActivity);//this function also automatically set the former $iniitalAcitivty to "not initial"
			//TODO: rename the function createSequenceActivity to addSequenceActivity, clearer that way
			
			//build the last activity:
			$lastActivity = $activityClass->createInstance("process_end ({$process->getLabel()})", "created by ProcessCloner.Class");
			$lastActivity->editPropertyValues($propHidden, GENERIS_TRUE);
			foreach($newFinalActivities as $newActivity){
				
				//TODO: determine if there is need for merging multiple instances of a parallelized activity that has not been merged 
				$connector = $this->authoringService->createConnector($newActivity);
				
				$this->authoringService->createSequenceActivity($connector, $lastActivity);
			}
			
			$newInitialActivity = $firstActivity;
			$newFinalActivities = $lastActivity;
			
			$this->addClonedActivity(null, $firstActivity);
			$this->addClonedActivity(null, $lastActivity);
		}
		
		return array(
			'in' => $newInitialActivity,
			'out' => $newFinalActivities
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
			
			$this->updateWaitingConnector($connector, $connectorClone);
			
			//set activity reference:
			$propActivityRef = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
			$oldReferenceActivity = $connector->getUniquePropertyValue($propActivityRef);
			
			$newReferenceActivity = $this->getClonedActivity($oldReferenceActivity, 'out');
			
			if(!is_null($newReferenceActivity)){
				if(is_array($newReferenceActivity)){
					$newReferenceActivity = $newReferenceActivity[0];
				}
				if(!$newReferenceActivity instanceof $newReferenceActivity){
					throw new Exception("the cloned reference activity found is not a resource!");
				}
				$connectorClone->setPropertyValue($propActivityRef, $newReferenceActivity->uriResource);
			}else{
				throw new Exception("the new activity reference cannot be found among the cloned activities");
			}
			
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if(!is_null($connectorType)){
				switch($connectorType->uriResource){
					case INSTANCE_TYPEOFCONNECTORS_SPLIT:{
					
						$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						
						$transitionRuleClone= null;
						if(!is_null($transitionRule)){
							//required to recreate the rule:
							$if = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
							if(!is_null($if)){
								$transitionRuleClone = $this->authoringService->createRule($connectorClone, $if->getLabel());
							}
						}
						if(is_null($transitionRuleClone)){
							$transitionRuleClone = $this->authoringService->createTransitionRule($connectorClone);
							if(is_null($transitionRuleClone)) throw new Exception("the transition rule of the cloned connector cannot be created");
						}
						
						$transitionRuleActivityProperties = array(
							'then' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN),
							'else' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE)
						);
						
						foreach($transitionRuleActivityProperties as $activityType => $connectorActivityProperty){
							$activity = $transitionRule->getOnePropertyValue($connectorActivityProperty);
							if(!is_null($activity)){
								
								$newPropActivity = $this->getNewActivityFromOldActivity($activity, $oldReferenceActivity, $activityType, $connectorClone);
								if(!is_null($newPropActivity)){
									if(is_array($newPropActivity)){
										foreach($newPropActivity as $activityResource){
											if($activityResource instanceof core_kernel_classes_Resource){
												$transitionRuleClone->setPropertyValue($connectorActivityProperty, $activityResource->uriResource);
												$activityResource->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
											}
										}
									}else if($newPropActivity instanceof core_kernel_classes_Resource){
										$transitionRuleClone->setPropertyValue($connectorActivityProperty, $newPropActivity->uriResource);
										$newPropActivity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
									}
									
								}
							}
						}
						// break;//do not break!
					}
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
									
									/*
									* "new prop acitivy" can be:
									* 1 - an activity resource
									* 2 - an array of activity resources
									* 3 - a connector resource
									*/
									$newPropActivity = $this->getNewActivityFromOldActivity($activity, $oldReferenceActivity, $activityType, $connectorClone);
									
									// if(!is_null($newPropActivity)){
									
										// if(is_array($newPropActivity)){
											// foreach($newPropActivity as $activityResource){
												// if($activityResource instanceof core_kernel_classes_Resource){
													// $newPropActivitiesUris[] = $activityResource->uriResource;
													// if($activityType == 'next')
														// $activityResource->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
												// }
											// }
										// }else if($newPropActivity instanceof core_kernel_classes_Resource){
											// $newPropActivitiesUris[] = $newPropActivity->uriResource;
											// if($activityType == 'next') 
												// $newPropActivity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
										// }
										
									// }
									
									if(!is_null($newPropActivity)){
										if($activityType == 'next'){
											if($newPropActivity instanceof core_kernel_classes_Resource){
												$newPropActivitiesUris[] = $newPropActivity->uriResource;
												$newPropActivity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
											}else{
												throw new Exception('the next activity must be a single activity resource');
											}
										}else if($activityType == 'prev'){
											//prev:
											if($newPropActivity instanceof core_kernel_classes_Resource){
												$newPropActivitiesUris[] = $newPropActivity->uriResource;
											}else if(is_array($newPropActivity)){
												$count = 0;
												$sequentialConnectorType = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
												foreach($newPropActivity as $inputActivity){
													if($count == 0){
														$newPropActivitiesUris[] = $inputActivity->uriResource;
													}else{
														//required to build a new sequential connector:
														$sequentialConnector = $this->authoringService->createConnector($inputActivity);
														// $this->authoringService->setConnectorType($sequentialConnector, $sequentialConnectorType);
														// $newPropActivitiesUris[] = $sequentialConnector->uriResource;
														$this->authoringService->createSequenceActivity($sequentialConnector, $connectorClone);
													}
													$count++;
												}
											}else{
												throw new Exception('the next activity must be a single activity resource');
											}
										}else{
											throw new Exception('unknown connection type in connector clone: '.$activityType);
										}
									}
									
								}
							}
							$connectorClone->editPropertyValues($connectorActivityProperty, $newPropActivitiesUris);
						}
						break;
					}
				}
			}
			
			$this->addClonedConnector($connector, $connectorClone);
			$returnValue = $connectorClone;
		}
		
		return $returnValue;
		
	}
	
	protected function getNewActivityFromOldActivity(core_kernel_classes_Resource $oldActivity, core_kernel_classes_Resource $oldReferenceActivity, $connectionType, core_kernel_classes_Resource $clonedConnector){
		
		$activity = $oldActivity;
		$returnValue = null;
		$activityIO = '';
		switch($connectionType){
			case 'next':
			case 'then':
			case 'else':{
				//explanation: we are looking for the activity than is in the property "next activity" so it is the activity entering point that should be considered
				$activityIO = 'in';
				break;
			}
			case 'prev':{
				$activityIO = 'out';
				break;
			}
			default:{
				throw new Exception("unknown connectionType");
			}
		}
		//note: most of the time, $this->clonedActivities[$activity->uriResource]['in'] = $this->clonedActivities[$activity->uriResource]['out']
		
		if(!is_null($activity) && !is_null($oldReferenceActivity)){
			if(wfEngine_models_classes_ProcessAuthoringService::isActivity($activity)){
				$newActivity = $this->getClonedActivity($activity, $activityIO);
				if(!is_null($newActivity)){
					$returnValue = $newActivity;
					//note: works for parallel activity too, where multiple branch is created a parallelized branch
				}else{
					//must have been cloned!
					// print_r($this->clonedActivities);
					throw new Exception("the previous activity has not been cloned! {$activity->getLabel()}({$activity->uriResource})");
				}
			}else if(wfEngine_models_classes_ProcessAuthoringService::isConnector($activity)){
				$newConnector = $this->getClonedConnector($activity);
				if(!is_null($newConnector)){
					//it is a reference to a connector with another activity reference and it has been cloned already
					$returnValue = $newConnector;
				}else{
					//not cloned yet:
					//clone it only if the reference id is the current activity
					//OR if the previous activities of a split connector:
					if($oldReferenceActivity->uriResource == $activity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource && $activityIO=='in'){
						//recursively clone it
						
						$nextConnectorClone = $this->cloneConnector($activity);
						
						// $this->setWaitingConnector($activity, 'prev', $nextConnectorClone);//important to set the connector as a required one
						// if(!$this->updateWaitingConnector($activity, $nextConnectorClone)){
							// throw new Exception("the next connector clone cannot be updated");
						// }
						
						if(!is_null($nextConnectorClone)){
							
							$returnValue = $nextConnectorClone;
						}else{
							throw new Exception("the next connector cannot be cloned");
						}
					}else{
						//it is a connector of another activityReference branch and it is not cloned yet, so set it as such:
						//put in the waiting list:
						$this->setWaitingConnector($activity, $connectionType, $clonedConnector);
					}
				}
			}
		}
		
		return $returnValue;
	}
	
	protected function updateWaitingConnector(core_kernel_classes_Resource $expectedConnector, core_kernel_classes_Resource $expectedConnectorClone){
		
		
		
		$returnValue = false;
		
		//check if it is in the waiting expectedConnector list:
		$activityPropertiesMap = array(
			'next' => new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES),
			'prev' => new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES),
			'then' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN),
			'else' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE)
		);
			
		
		if(isset($this->waitingConnectors[$expectedConnector->uriResource])){
			
			foreach($this->waitingConnectors[$expectedConnector->uriResource] as $connectionType=>$connectors){
				if(isset($activityPropertiesMap[$connectionType])){
					
					$connectorProperty = $activityPropertiesMap[$connectionType];
					
					foreach($connectors as $aConnector){
						switch($connectionType){
							case 'next':
							case 'prev':{
								$aConnector->setPropertyValue($connectorProperty, $expectedConnectorClone->uriResource);
								$returnValue = true;
								break;
							}
							case 'then':
							case 'else':{
								//property of the transition rule:
								$transitionRule = $aConnector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
								if(!is_null($transitionRule)){
									//transition rule copied
									$transitionRule->setPropertyValue($connectorProperty, $expectedConnectorClone->uriResource);
									$returnValue = true;
								}else{
									throw new Exception("the transition rule does not exist anymore");
								}
								break;
							}
						}
					}
				}else{
					throw new Exception('unknown connection type :'.$connectionType);
				}
			}
			
			unset($this->waitingConnectors[$expectedConnector->uriResource]);
			
		}
		
		return $returnValue;
	}
	
	protected function setWaitingConnector($waitingOldConnectorToBeCloned, $connectionType, $clonedConnectorToUpdate){
		$authorizedConnectionTypes = array('prev', 'next', 'then', 'else');
		
		if(!in_array($connectionType, $authorizedConnectionTypes)){
			throw new Exception("unavailable connection type");
		}
		if(!isset($this->waitingConnectors[$waitingOldConnectorToBeCloned->uriResource])){
			foreach($authorizedConnectionTypes as $authorizedConnectionType){
				$this->waitingConnectors[$waitingOldConnectorToBeCloned->uriResource][$authorizedConnectionType] = array();
			}
			
		}
		$this->waitingConnectors[$waitingOldConnectorToBeCloned->uriResource][$connectionType][] = $clonedConnectorToUpdate;
	}
	
	protected function cloneWfResource(core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz, $forbiddenProperties = array(), $newLabel='')
    {
        $returnValue = null;
        
   		$returnValue = $this->createInstance($clazz);
		if(!is_null($returnValue)){
			foreach($clazz->getProperties(true) as $property){
				if(!in_array($property->uriResource, $forbiddenProperties)){
					$returnValue->editPropertyValues($property, $instance->getPropertyValues($property));
				}
			}
			// $label = $instance->getLabel();
			$cloneLabel = empty($newLabel)? $instance->getLabel().$this->cloneLabel:$newLabel;
			
			$returnValue->setLabel($cloneLabel);
			
		}

        return $returnValue;
    }
	
	public function revertCloning(){
		
		if(!is_null($this->clonedProcess) && $this->clonedProcess instanceof core_kernel_classes_Resource){
			$this->authoringService->deleteProcess($this->clonedProcess);
		}
		
		foreach($this->getClonedActivities() as $activity){
			$this->authoringService->deleteActivity($activity);
		}
		
		$this->initCloningVariables();
	}
	
} /* end of class wfEngine_models_classes_ProcessAuthoringService */

?>
