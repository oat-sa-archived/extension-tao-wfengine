<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine\models\classes\class.ProcessCloner.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 04.01.2011, 11:16:39 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('tao/models/classes/class.GenerisService.php');

/**
 * include wfEngine_models_classes_ProcessAuthoringService
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 */
require_once('wfEngine/models/classes/class.ProcessAuthoringService.php');

/* user defined includes */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FAB-includes begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FAB-includes end

/* user defined constants */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FAB-constants begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FAB-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessCloner
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessCloner
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute authoringService
     *
     * @access protected
     * @var ProcessAuthoringService
     */
    protected $authoringService = null;

    /**
     * Short description of attribute currentActivity
     *
     * @access protected
     * @var Resource
     */
    protected $currentActivity = null;

    /**
     * Short description of attribute clonedProcess
     *
     * @access protected
     * @var Resource
     */
    protected $clonedProcess = null;

    /**
     * Short description of attribute cloneLabel
     *
     * @access protected
     * @var string
     */
    protected $cloneLabel = '';

    /**
     * Short description of attribute clonedActivities
     *
     * @access protected
     * @var array
     */
    protected $clonedActivities = array();

    /**
     * Short description of attribute clonedConnectors
     *
     * @access protected
     * @var array
     */
    protected $clonedConnectors = array();

    /**
     * Short description of attribute waitingConnectors
     *
     * @access protected
     * @var array
     */
    protected $waitingConnectors = array();

    /**
     * Short description of attribute debugClonedActivities
     *
     * @access public
     * @var array
     */
    public $debugClonedActivities = array();

    /**
     * Short description of attribute debugClonedConnectors
     *
     * @access public
     * @var array
     */
    public $debugClonedConnectors = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  string cloneLabel
     */
    public function __construct($cloneLabel = '')
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FB5 begin
		$this->cloneLabel = $cloneLabel;
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$this->initCloningVariables();
		parent::__construct();
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FB5 end
    }

    /**
     * Short description of method addClonedActivity
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource newActivityIn
     * @param  Resource oldActivity
     * @param  array newActivityOut
     * @return mixed
     */
    public function addClonedActivity( core_kernel_classes_Resource $newActivityIn,  core_kernel_classes_Resource $oldActivity = null, $newActivityOut = null)
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FCC begin
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
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FCC end
    }

    /**
     * Short description of method addClonedConnector
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource oldConnector
     * @param  Resource newConnector
     * @return mixed
     */
    public function addClonedConnector( core_kernel_classes_Resource $oldConnector,  core_kernel_classes_Resource $newConnector)
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FDC begin
		$this->clonedConnectors[$oldConnector->uriResource] = $newConnector->uriResource;
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FDC end
    }

    /**
     * Short description of method cloneActivity
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function cloneActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FE3 begin
		if(wfEngine_helpers_ProcessUtil::isActivity($activity)){
			$activityClone = $this->cloneWfResource(
				$activity, 
				new core_kernel_classes_Class(CLASS_ACTIVITIES),
				array(
					PROPERTY_ACTIVITIES_INTERACTIVESERVICES
			));
			
			if(!is_null($activityClone)){
				//clone the interactive service:
				$services = $this->authoringService->getServicesByActivity($activity);
				foreach($services as $service){
					$serviceClone = $this->cloneInteractiveService($service);
					if(!is_null($serviceClone)){
						$activityClone->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $serviceClone->uriResource);
					}else{
						throw new Exception("the interactive service cannot be cloned: {$service->getLabel()}({$service->uriResource}) for the activity {$activityClone->getLabel()}({$activityClone->uriResource}) ");
					}
					
				}
				
				//TODO: the related rules, when implementation has been confirmed
				
				$returnValue = $activityClone;
			}
			
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FE3 end

        return $returnValue;
    }

    /**
     * Short description of method cloneConnector
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    public function cloneConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FE6 begin
		if(wfEngine_helpers_ProcessUtil::isConnector($connector)){
			$connectorClone = $this->cloneWfResource(
				$connector, 
				new core_kernel_classes_Class(CLASS_CONNECTORS),
				array(
					PROPERTY_CONNECTORS_TRANSITIONRULE,
					PROPERTY_CONNECTORS_NEXTACTIVITIES,
					PROPERTY_CONNECTORS_ACTIVITYREFERENCE,
					PROPERTY_CONNECTORS_PREVIOUSACTIVITIES
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
				//echo 'oldReferenceActivity label: '.$oldReferenceActivity->getLabel();
				//print_r($this);
				throw new Exception("the new activity reference cannot be found among the cloned activities");
			}
			
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if(!is_null($connectorType)){
				switch($connectorType->uriResource){
					case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL:{
					
						$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						
						$transitionRuleClone= null;
						if(!is_null($transitionRule)){
							//required to recreate the rule:
							$if = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
							if(!is_null($if)){
								$transitionRuleClone = $this->authoringService->createTransitionRule($connectorClone, $if->getLabel());
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
							'prev' => new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES),
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
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FE6 end

        return $returnValue;
    }

    /**
     * Short description of method cloneInteractiveService
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource service
     * @return core_kernel_classes_Resource
     */
    public function cloneInteractiveService( core_kernel_classes_Resource $service)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FE9 begin
		$classCallOfServices = new core_kernel_classes_Class(CLASS_CALLOFSERVICES);
		$classActualParam = new core_kernel_classes_Class(CLASS_ACTUALPARAMETER);
		$propActualParamIn = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);
		
		$serviceClone = $this->cloneWfResource($service, $classCallOfServices, array($propActualParamIn->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT));
		if(!is_null($serviceClone)){
			foreach($service->getPropertyValuesCollection($propActualParamIn)->getIterator() as $actualParamIn){
				if($actualParamIn instanceof core_kernel_classes_Resource){
					$actualParamInClone = $this->cloneWfResource($actualParamIn, $classActualParam);
					if(!is_null($actualParamInClone)){
						$serviceClone->setPropertyValue($propActualParamIn, $actualParamInClone->uriResource);
					}else{
						throw new Exception('an input actual parameter cannot be clonned');
					}
				}
				
			}
			
			$returnValue = $serviceClone;
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FE9 end

        return $returnValue;
    }

    /**
     * Short description of method cloneProcess
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource process
     * @return core_kernel_classes_Resource
     */
    public function cloneProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FEC begin
		$processClone = $this->cloneWfResource($process, new core_kernel_classes_Class(CLASS_PROCESS), array(PROPERTY_PROCESS_ACTIVITIES, PROPERTY_PROCESS_DIAGRAMDATA));
		
		$this->initCloningVariables();
		
		if(!is_null($processClone)){
			//get all activity processes and clone them:
			$activities = $this->authoringService->getActivitiesByProcess($process);
			foreach($activities as $activityUri => $activity){
				$activityClone = $this->cloneActivity($activity);
				if(!is_null($activityClone)){
					$this->addClonedActivity($activityClone, $activity);
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
			
			$returnValue = $processClone;
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FEC end

        return $returnValue;
    }

    /**
     * Short description of method cloneProcessSegment
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource process
     * @param  boolean addTransitionalActivity
     * @param  Resource startActivity
     * @param  Resource endActivity
     * @return core_kernel_classes_Array
     */
    public function cloneProcessSegment( core_kernel_classes_Resource $process, $addTransitionalActivity = false,  core_kernel_classes_Resource $startActivity = null,  core_kernel_classes_Resource $endActivity = null)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FEF begin
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
					$this->addClonedActivity($activityClone, $activity);
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
			
			$this->addClonedActivity($firstActivity);
			$this->addClonedActivity($lastActivity);
		}
		
		$returnValue = array(
			'in' => $newInitialActivity,
			'out' => $newFinalActivities
		);
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000004FEF end

        return $returnValue;
    }

    /**
     * Short description of method cloneWfResource
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource instance
     * @param  Class clazz
     * @param  array forbiddenProperties
     * @param  string newLabel
     * @return core_kernel_classes_Resource
     */
    public function cloneWfResource( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz, $forbiddenProperties = array(), $newLabel = '')
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005004 begin
		$cloneLabel = empty($newLabel)? $instance->getLabel().$this->cloneLabel:$newLabel;
		$returnValue = $this->createInstance($clazz, $cloneLabel);
		if(!is_null($returnValue)){
			foreach($clazz->getProperties(true) as $property){
				if(!in_array($property->uriResource, $forbiddenProperties)){
					$returnValue->editPropertyValues($property, $instance->getPropertyValues($property));
				}
			}
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005004 end

        return $returnValue;
    }

    /**
     * Short description of method getClonedActivities
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return array
     */
    public function getClonedActivities()
    {
        $returnValue = array();

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005015 begin
		foreach($this->clonedActivities as $newActivityIO){
			if(is_array($newActivityIO)){
				foreach(array('in', 'out') as $interface){
					if(is_array($newActivityIO[$interface])){
						foreach($newActivityIO[$interface] as $activityUri){
							$activity = new core_kernel_classes_Resource($activityUri);
							if(wfEngine_helpers_ProcessUtil::isActivity($activity)){
								$returnValue[$activity->uriResource] = $activity;
							}
						}
					}
					else{
						$activity = new core_kernel_classes_Resource($newActivityIO[$interface]);
						if(wfEngine_helpers_ProcessUtil::isActivity($activity)){
							$returnValue[$activity->uriResource] = $activity;
						}
					}
				}
			}else if(is_string($newActivityIO)){
				$activity = new core_kernel_classes_Resource($newActivityIO);
				if(wfEngine_helpers_ProcessUtil::isActivity($activity)){
					$returnValue[$activity->uriResource] = $activity;
				}
				
			}
			
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005015 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getClonedActivity
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource oldActivity
     * @param  string InOut
     * @return core_kernel_classes_Resource
     */
    public function getClonedActivity( core_kernel_classes_Resource $oldActivity, $InOut = 'in')
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005018 begin
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
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005018 end

        return $returnValue;
    }

    /**
     * Short description of method getClonedConnector
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource oldConnector
     * @return core_kernel_classes_Resource
     */
    public function getClonedConnector( core_kernel_classes_Resource $oldConnector)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005020 begin
		if(isset($this->clonedConnectors[$oldConnector->uriResource])){
			$returnValue = new core_kernel_classes_Resource($this->clonedConnectors[$oldConnector->uriResource]);
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005020 end

        return $returnValue;
    }

    /**
     * Short description of method getClonedConnectors
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return array
     */
    public function getClonedConnectors()
    {
        $returnValue = array();

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005024 begin
		foreach($this->clonedConnectors as $connectorUri){
			$connector = new core_kernel_classes_Resource($connectorUri);
			if(wfEngine_helpers_ProcessUtil::isConnector($connector)){
				$returnValue[$connector->uriResource] = $connector;
			}
		}
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005024 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getCloneLabel
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return string
     */
    public function getCloneLabel()
    {
        $returnValue = (string) '';

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005026 begin
		$returnValue = $this->cloneLabel;
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005026 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getNewActivityFromOldActivity
     *
     * @access protected
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource oldActivity
     * @param  Resource oldReferenceActivity
     * @param  string connectionType
     * @param  Resource clonedConnector
     * @return core_kernel_classes_Resource
     */
    protected function getNewActivityFromOldActivity( core_kernel_classes_Resource $oldActivity,  core_kernel_classes_Resource $oldReferenceActivity, $connectionType,  core_kernel_classes_Resource $clonedConnector)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005028 begin
		$activity = $oldActivity;
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
			if(wfEngine_helpers_ProcessUtil::isActivity($activity)){
				$newActivity = $this->getClonedActivity($activity, $activityIO);
				if(!is_null($newActivity)){
					$returnValue = $newActivity;
					//note: works for parallel activity too, where multiple branch is created a parallelized branch
				}else{
					//must have been cloned!
					// print_r($this->clonedActivities);
					throw new Exception("the previous activity has not been cloned! {$activity->getLabel()}({$activity->uriResource})");
				}
			}else if(wfEngine_helpers_ProcessUtil::isConnector($activity)){
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
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000005028 end

        return $returnValue;
    }

    /**
     * Short description of method initCloningVariables
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function initCloningVariables()
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000714F begin
		$this->currentActivity = null;
		$this->clonedProcess = null;
		$this->clonedActivities = array();
		$this->clonedConnectors = array();
		$this->waitingConnectors = array();
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000714F end
    }

    /**
     * Short description of method revertCloning
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return mixed
     */
    public function revertCloning()
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007151 begin
		if(!is_null($this->clonedProcess) && $this->clonedProcess instanceof core_kernel_classes_Resource){
			$this->authoringService->deleteProcess($this->clonedProcess);
		}
		
		foreach($this->getClonedActivities() as $activity){
			$this->authoringService->deleteActivity($activity);
		}
		
		$this->initCloningVariables();
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007151 end
    }

    /**
     * Short description of method setCloneLabel
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  string cloneLabel
     */
    public function setCloneLabel($cloneLabel = '')
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007153 begin
		$this->cloneLabel = $cloneLabel;
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007153 end
    }

    /**
     * Short description of method setDebugClonedActivities
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource activity
     * @return mixed
     */
    public function setDebugClonedActivities( core_kernel_classes_Resource $activity)
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000715A begin
		if(!is_null($activity))
			$this->debugClonedActivities[$activity->uriResource] = $activity->getLabel();
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000715A end
    }

    /**
     * Short description of method setWaitingConnector
     *
     * @access protected
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource waitingOldConnectorToBeCloned
     * @param  string connectionType
     * @param  Resource clonedConnectorToUpdate
     * @return mixed
     */
    protected function setWaitingConnector( core_kernel_classes_Resource $waitingOldConnectorToBeCloned, $connectionType,  core_kernel_classes_Resource $clonedConnectorToUpdate)
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000715D begin
		
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
		
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000715D end
    }

    /**
     * Short description of method updateWaitingConnector
     *
     * @access protected
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @param  Resource expectedConnector
     * @param  Resource expectedConnectorClone
     * @return boolean
     */
    protected function updateWaitingConnector( core_kernel_classes_Resource $expectedConnector,  core_kernel_classes_Resource $expectedConnectorClone)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007162 begin
		
		//check if it is in the waiting expectedConnector list:
		$activityPropertiesMap = array(
			'next' => new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES),
			'prev' => new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES),
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
		
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007162 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessCloner */

?>