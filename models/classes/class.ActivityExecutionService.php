<?php

error_reporting(E_ALL);

/**
 * This service enables you to manage, control, restrict the process activities
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-includes begin
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-includes end

/* user defined constants */
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-constants begin
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-constants end

/**
 * This service enables you to manage, control, restrict the process activities
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ActivityExecutionService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processExecutionProperty
     *
     * @access protected
     * @var Property
     */
    protected $processExecutionProperty = null;

    /**
     * Short description of attribute currentUserProperty
     *
     * @access protected
     * @var Property
     */
    protected $currentUserProperty = null;

    /**
     * Short description of attribute ACLModeProperty
     *
     * @access protected
     * @var Property
     */
    protected $ACLModeProperty = null;

    /**
     * Short description of attribute restrictedUserProperty
     *
     * @access protected
     * @var Property
     */
    protected $restrictedUserProperty = null;

    /**
     * Short description of attribute restrictedRoleProperty
     *
     * @access protected
     * @var Property
     */
    protected $restrictedRoleProperty = null;

    /**
     * Short description of attribute activityProperty
     *
     * @access protected
     * @var Property
     */
    protected $activityProperty = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1--14d619a:12ce565682e:-8000:000000000000297B begin
        
    	$this->processExecutionProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
        $this->currentUserProperty		= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
    	$this->activityProperty			= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY);
    	$this->ACLModeProperty			= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE);
        $this->restrictedUserProperty	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_USER);
        $this->restrictedRoleProperty	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE);
    	
        // section 127-0-1-1--14d619a:12ce565682e:-8000:000000000000297B end
    }

    /**
     * Get the list of available ACL modes
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getAclModes()
    {
        $returnValue = array();

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6B begin

        $aclModeClass = new core_kernel_classes_Class(CLASS_ACL_MODES);
        foreach($aclModeClass->getInstances() as $mode){
        	$returnValue[$mode->uriResource] = $mode;
        }
        
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6B end

        return (array) $returnValue;
    }

    /**
     * Define the ACL mode of an activity
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @param  Resource mode
     * @param  Resource target
     * @return core_kernel_classes_Resource
     */
    public function setAcl( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $mode,  core_kernel_classes_Resource $target = null)
    {
        $returnValue = null;

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5D begin
        
        //check the kind of resources
        if($this->getClass($activity)->uriResource != CLASS_ACTIVITIES){
        	throw new Exception("Activity must be an instance of the class Activities");
        }
        if(!in_array($mode->uriResource, array_keys($this->getAclModes()))){
        	throw new Exception("Unknow acl mode");
        }
        
        //set the ACL mode
        $properties = array(
        	PROPERTY_ACTIVITIES_ACL_MODE	=> $mode->uriResource
        );
        
        switch($mode->uriResource){
        	case INSTANCE_ACL_ROLE:
        	case INSTANCE_ACL_ROLE_RESTRICTED_USER:
        	case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
        		if(is_null($target)){
        			throw new Exception("Target must reference a role resource");
        		}
        		$properties[PROPERTY_ACTIVITIES_RESTRICTED_ROLE] = $target->uriResource;
        		break;
        		
        	case INSTANCE_ACL_USER:
        		if(is_null($target)){
        			throw new Exception("Target must reference a user resource");
        		}
        		$properties[PROPERTY_ACTIVITIES_RESTRICTED_USER] = $target->uriResource;
        		break;
        }
        
        //bind the mode and the target (user or role) to the activity
        $returnValue = $this->bindProperties($activity, $properties);
        
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5D end

        return $returnValue;
    }

    /**
     * get the execution of this activity for the user
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function getExecution( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--11ec324e:128d9678eea:-8000:0000000000001F80 begin
        
        if(!is_null($activity) && !is_null($currentUser) && !is_null($processExecution)){
        	
        	$filters = array(
        		PROPERTY_ACTIVITY_EXECUTION_ACTIVITY 			=> $activity->uriResource,
        		$this->currentUserProperty->uriResource			=> $currentUser->uriResource,
        		$this->processExecutionProperty->uriResource	=> $processExecution->uriResource
        	);
        	$clazz = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
        	$options = array('recursive'	=> false, 'like' => false);
			
			foreach($clazz->searchInstances($filters, $options) as $activityExecution){
				$returnValue = $activityExecution;
				break;
			}
        }
        // section 127-0-1-1--11ec324e:128d9678eea:-8000:0000000000001F80 end

        return $returnValue;
    }

    /**
     * get the executions of this activity
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @param  Resource processExecution
     * @return array
     */
    public function getExecutions( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-6c2b28ea:1291bc8511a:-8000:0000000000001FB3 begin
        
		if(!is_null($activity) &&  !is_null($processExecution)){
          	
        	$filters = array(
        		PROPERTY_ACTIVITY_EXECUTION_ACTIVITY 			=> $activity->uriResource,
        		$this->processExecutionProperty->uriResource	=> $processExecution->uriResource
        	);
        	$clazz = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
        	$options = array('recursive'	=> false, 'like' => false);
			
			foreach($clazz->searchInstances($filters, $options) as $activityExecution){
				$returnValue[$activityExecution->uriResource] = $activityExecution;
			}
        }
        
        // section 127-0-1-1-6c2b28ea:1291bc8511a:-8000:0000000000001FB3 end

        return (array) $returnValue;
    }

    /**
     * initialize the exectuion of that activity by the currentUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function initExecution( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--11ec324e:128d9678eea:-8000:0000000000001F7C begin
        
        
        if(!is_null($activity) && !is_null($currentUser) && !is_null($processExecution)){
	        
        	$execution = $this->getExecution($activity, $currentUser, $processExecution);
	        
	        //if no activty execution, create one for that user
	        if(is_null($execution)){
	        	
	        	
	        	$label = 'Activity Execution of '.$activity->getLabel();
	        	
	        	//create a new activity execution
	        	$execution = $this->createInstance(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION),  $label);
	        	
	        	//link it to the activity definition:
	        	$execution->setPropertyValue($this->activityProperty, $activity->uriResource);
	        	
	        	//bind this execution of the activity with the current user and the current process execution
	        	$this->bindExecution($execution, $currentUser, $processExecution);
	       		
	        }
	        else{
	       		//execution is initialized back if it already exists
	        	$execution->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED), GENERIS_FALSE);
	        }
	        
	        $returnValue = $execution;
        }
        
        // section 127-0-1-1--11ec324e:128d9678eea:-8000:0000000000001F7C end

        return $returnValue;
    }

    /**
     * Link an activity execution to the current user (it will enables you to
     * the execution for that user or restrict it afterwhile)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return boolean
     */
    public function bindExecution( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F78 begin
        
        if(!is_null($activityExecution) && !is_null($currentUser)){
        	
        	//check if the activity execution isn't already bound
        	if(is_null($activityExecution->getOnePropertyValue($this->currentUserProperty))){

        		//link the current user
	        	$activityExecution->setPropertyValue( $this->currentUserProperty, $currentUser->uriResource);
	        	
				//link the current process execution
	        	$activityExecution->setPropertyValue( $this->processExecutionProperty, $processExecution->uriResource);
	        	
	        	//in case of role and user restriction, set the current user as activty user
	        	$activity		= $activityExecution->getUniquePropertyValue($this->activityProperty);
	        	//$activity		= new core_kernel_classes_Resource($activityUri->uriResource);
	        	$mode			= $activity->getOnePropertyValue($this->ACLModeProperty);

	        	if(!is_null($mode)){
	        		if($mode->uriResource == INSTANCE_ACL_ROLE_RESTRICTED_USER){
	        			if(is_null($activity->getOnePropertyValue($this->restrictedUserProperty))){
	        				$activity->setPropertyValue($this->restrictedUserProperty, $currentUser->uriResource);
	        			}
	        		}
	        	}
        	}
        }
        
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F78 end

        return (bool) $returnValue;
    }

    /**
     * Check the ACL of a user for a given activity.
     * It returns false if the user cannot access the activity.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return boolean
     */
    public function checkAcl( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;
		return true;
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F62 begin
        
        if(!is_null($activity) && !is_null($currentUser)){
        	
        	//activity and current must be set to the activty execution otherwise a common Exception is thrown
        	$modeUri 		= $activity->getOnePropertyValue($this->ACLModeProperty);
        	
        	if(is_null($modeUri)){
        		$returnValue = true;	//if no mode defined, the activity is allowed
        	}
        	else{
        		switch($modeUri->uriResource){
        			
        			//check if th current user is the restricted user
        			case INSTANCE_ACL_USER:
        				$activityUser = $activity->getOnePropertyValue($this->restrictedUserProperty);
        				if(!is_null($activityUser)){
	        				if($activityUser->uriResource == $currentUser->uriResource) {
	        					$returnValue = true;
	        				}
        				}
        				break;
        			
        			//check if the current user has the restricted role
        			case INSTANCE_ACL_ROLE:
        				$activityRole 	= $activity->getOnePropertyValue($this->restrictedRoleProperty);
        				$userRoles 		= $currentUser->getType();
						if(!is_null($activityRole) && is_array($userRoles)){
							foreach($userRoles as $userRole){
								if($activityRole->uriResource == $userRole->uriResource){
									return true;
								}
							}
						}
        				break;	
        			
        			//check if the current user has the restricted role and is the restricted user
        			case INSTANCE_ACL_ROLE_RESTRICTED_USER:
        				
						
        				//check if an activity execution already exists for the current activity or if there are several in parallel, check if there is one spot available. If so create the activity execution:
						//need to know the current process execution, from it, get the process definition and the number of activity executions associated to it.
						//from the process definition get the number of allowed activity executions for this activity definition (normally only 1 but can be more, for a parallel connector)
						
        				$activityRole 	= $activity->getOnePropertyValue($this->restrictedRoleProperty);
						$userRoles 		= $currentUser->getType();
        				if(!is_null($activityRole) && is_array($userRoles)){
        					foreach($userRoles as $userRole){
        						
		        				if($activityRole->uriResource == $userRole->uriResource){
		        					
		        					$activityExecutions = $this->getExecutions($activity, $processExecution);
		        					$estimatedActivityExecutions = $this->getEstimatedExecutionCount($activity);
		        					
		        					if(count($activityExecutions) < $estimatedActivityExecutions){
										$returnValue = true;
		        					}
		        					elseif(!is_null($this->getExecution($activity, $currentUser, $processExecution))){
		        						$returnValue = true;
		        					}
		        					
		        					break;
								}
        					}
						}
        				break;	
        				
        			//check if the current user has the restricted role and is the restricted user based on the previous activity with the given role
        			case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
        				$activityRole 	= $activity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE));
        				$userRoles 		= $currentUser->getType();
        				
        				if(!is_null($activityRole) && is_array($userRoles)){
        				
	        				$actsProp 			= new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES);
			
	        				//retrieve the process containing the activity
							$activityClass = new core_kernel_classes_Class(CLASS_PROCESS);
							$processes = $activityClass->searchInstances(array($actsProp->uriResource => $activity->uriResource), array('like' => false, 'recursive' => true));
					        foreach($processes as $process){
					        	
					        	//get  activities
								foreach ($process->getPropertyValues($actsProp) as $pactivityUri){
									
									$pactivity = new core_kernel_classes_Resource($pactivityUri);
									
					        		//with the same mode
					        		$mode = $pactivity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE));
					        		if($mode->uriResource == INSTANCE_ACL_ROLE_RESTRICTED_USER){
					        			$returnValue = $this->checkAcl($pactivity, $currentUser, $processExecution);
					        			break;
					        		}
						        }
					        }
        				}
						break;
					
					//special case for deliveries
					case INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY:
						if(wfEngine_helpers_ProcessUtil::isActivityInitial($activity)){
							$activityRole 	= $activity->getUniquePropertyValue($this->restrictedRoleProperty);
							$userRoles 		= $currentUser->getType();
							if(!is_null($activityRole) && is_array($userRoles)){
								foreach($userRoles as $userRole){
									if($activityRole->uriResource == $userRole->uriResource){
										return true;
									}
								}
							}
							return false;
						}
						else{
							$process = $processExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF));
							if(!is_null($process)){
								
								$actsProp 			= new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES);
								//get  activities
								foreach ($process->getPropertyValues($actsProp) as $pactivityUri){
									$pactivity = new core_kernel_classes_Resource($pactivityUri);
									if(wfEngine_helpers_ProcessUtil::isActivityInitial($pactivity)){
										if(!is_null($this->getExecution($pactivity, $currentUser, $processExecution))){
											return true;
										}
										break;
									}
								}
							}
						}
					break;
        		}
        	}
        }
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F62 end

        return (bool) $returnValue;
    }

    /**
     * Get the list of available process execution for a user
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Process process
     * @param  Resource currentUser
     * @return array
     */
    public function getProcessActivities( wfEngine_models_classes_Process $process,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = array();

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6F begin

        if(!is_null($process) && !is_null($currentUser)){
        	
        	//loop on all the activities of a process
        	foreach($process->getAllActivities() as $activity){
        		
        		//check if the current user is allowed to access the activity
        		if($this->checkAcl($activity, $currentUser)){
        			$returnValue[] = $activity;
        		}
        	}
        }
        
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6F end

        return (array) $returnValue;
    }

    /**
     * Get the estimated number of execution of this activity
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Session_int
     */
    public function getEstimatedExecutionCount( core_kernel_classes_Resource $activity)
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-6c2b28ea:1291bc8511a:-8000:0000000000001FB7 begin
        
        $processFlow = new wfEngine_models_classes_ProcessFlow();
   		$parallelConnector = $processFlow->findParallelFromActivityBackward($activity);
   		if(!is_null($parallelConnector)){
			 $returnValue = count($parallelConnector->getPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES)));
   		}
   		else{
   			$returnValue = 1;
   		}
   		
        // section 127-0-1-1-6c2b28ea:1291bc8511a:-8000:0000000000001FB7 end

        return (int) $returnValue;
    }

    /**
     * Short description of method remove
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function remove( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--14d619a:12ce565682e:-8000:0000000000002981 begin
        
    	if(!is_null($processExecution)){
          	$activityExecClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
			$activityExecutions = $activityExecClass->searchInstances(array(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION => $processExecution->uriResource), array('like' => false, 'recursive' => false));
        	foreach($activityExecutions as $activityExecution){
				$activityExecution->delete();
        	}
        }
        
        // section 127-0-1-1--14d619a:12ce565682e:-8000:0000000000002981 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityExecutionService */

?>