<?php

error_reporting(E_ALL);

/**
 * This service enables you to manage, control, restrict the process activities
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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

    /**
     * Short description of attribute activityService
     *
     * @access protected
     * @var ActivityService
     */
    protected $activityService = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1--14d619a:12ce565682e:-8000:000000000000297B begin
        $this->activityExecutionClass	= new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$this->activityExecutionStatusProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_STATUS);
			
    	$this->processExecutionProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
        $this->currentUserProperty		= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
    	$this->activityProperty			= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY);
    	$this->ACLModeProperty			= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE);
        $this->restrictedUserProperty	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_USER);
        $this->restrictedRoleProperty	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE);
		
		$this->processInstanceActivityExecutionsProperty = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_ACTIVITYEXECUTIONS); 
		
        $this->activityService          = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
        // section 127-0-1-1--14d619a:12ce565682e:-8000:000000000000297B end
    }

    /**
     * Get the list of available ACL modes
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
        	$options = array('recursive'	=> 0, 'like' => false);
			
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
        	$options = array('recursive'	=> 0, 'like' => false);
			
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function initExecution( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--11ec324e:128d9678eea:-8000:0000000000001F7C begin
        //deprecated
        
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return boolean
     */
    public function checkAcl( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

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
        				
	        				$actsProp = new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES);
			
	        				//retrieve the process containing the activity
							$activityClass = new core_kernel_classes_Class(CLASS_PROCESS);
							$processes = $activityClass->searchInstances(array($actsProp->uriResource => $activity->uriResource), array('like' => false, 'recursive' => 0));
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
						if($this->activityService->isInitial($activity)){
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
									if($this->activityService->isInitial($pactivity)){
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Process process
     * @param  Resource currentUser
     * @return array
     */
    public function getProcessActivities( Process $process,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = array();

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6F begin
		//not used
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function remove( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--14d619a:12ce565682e:-8000:0000000000002981 begin
        
    	if(!is_null($processExecution)){
          	$activityExecClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
			$activityExecutions = $activityExecClass->searchInstances(array(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION => $processExecution->uriResource), array('like' => false, 'recursive' => 0));
        	foreach($activityExecutions as $activityExecution){
				$activityExecution->delete();
        	}
        }
        
        // section 127-0-1-1--14d619a:12ce565682e:-8000:0000000000002981 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getVariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getVariables( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F9B begin
		
		if(!is_null($activityExecution)){
            $tokenVarKeys = @unserialize($activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES)));
            if($tokenVarKeys !== false){
                if(is_array($tokenVarKeys)){
					$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
                    foreach($tokenVarKeys as $key){
						$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $key), array('like' => false));
						if(count($processVariables) == 1) {//not necessary
							$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);
							$returnValue[] = array(
									'code'			=> $key,
									'propertyUri'	=> $property->uriResource,
									'value'			=> $activityExecution->getPropertyValues($property)
								);
						}else{
							throw new wfEngine_models_classes_ProcessExecutionException('More than one process variable share the same code');
						}
                    }
                }
            }
        }
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F9B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getExecutionOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getExecutionOf( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB3 begin
		$returnValue = $activityExecution->getUniquePropertyValue($this->activityProperty);
        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB3 end

        return $returnValue;
    }

    /**
     * Short description of method getRelatedProcessExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getRelatedProcessExecution( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB9 begin
		$returnValue = $activityExecution->getUniquePropertyValue($this->processExecutionProperty);
        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB9 end

        return $returnValue;
    }

    /**
     * Short description of method setStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  string status
     * @return boolean
     */
    public function setStatus( core_kernel_classes_Resource $activityExecution, $status)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--4a6e3e05:1323e2d5c53:-8000:0000000000002FBE begin
		
		//add status information
		if (!empty($status)){
			if($status instanceof core_kernel_classes_Resource){
				switch($status->uriResource){
					case INSTANCE_PROCESSSTATUS_RESUMED:
					case INSTANCE_PROCESSSTATUS_STARTED:
					case INSTANCE_PROCESSSTATUS_FINISHED:
					case INSTANCE_PROCESSSTATUS_PAUSED:
					case INSTANCE_PROCESSSTATUS_CLOSED:{
						$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, $status->uriResource);
						break;
					}
				}
			}else if(is_string($status)){
				switch($status){
					case 'resumed':{$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, INSTANCE_PROCESSSTATUS_RESUMED);break;}
					case 'started':{$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, INSTANCE_PROCESSSTATUS_STARTED);break;}
					case 'finished':{$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, INSTANCE_PROCESSSTATUS_FINISHED);break;}
					case 'paused':{$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, INSTANCE_PROCESSSTATUS_PAUSED);break;}
					case 'closed':{$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, INSTANCE_PROCESSSTATUS_CLOSED);break;}
				}
			}
		}
		
        // section 127-0-1-1--4a6e3e05:1323e2d5c53:-8000:0000000000002FBE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method createActivityExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityDefinition
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function createActivityExecution( core_kernel_classes_Resource $activityDefinition,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FC1 begin
		
		$activityExecution = $this->createInstance($this->activityExecutionClass, 'Execution of '.$activityDefinition->getLabel().' at '.time());//d-m-Y H:i:s u
		$activityExecution->setPropertyValue($this->activityProperty, $activityDefinition->uriResource);
		
		//add bijective relation for performance optimization (not modifiable)
		$activityExecution->setPropertyValue($this->processExecutionProperty, $processExecution->uriResource);
		if($processExecution->setPropertyValue($this->processInstanceActivityExecutionsProperty, $activityExecution->uriResource)){
			$returnValue = $activityExecution;
		}
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FC1 end

        return $returnValue;
    }

    /**
     * Short description of method setActivityExecutionUser
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource user
     * @param  boolean forced
     * @return boolean
     */
    public function setActivityExecutionUser( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $user, $forced = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FE0 begin
		
		if($forced){
			$returnValue = $activityExecution->editPropertyValues($this->currentUserProperty, $user->uriResource);
		}else{
			$currentUser = $activityExecution->getOnePropertyValue($this->currentUserProperty);
			if(!is_null($currentUser)){
				$errorMessage = "the activity execution {$activityExecution->getLabel()}({$activityExecution->uriResource}) has already been assigned to the user {$user->getLabel()}({$user->uriResource})";
				throw new wfEngine_models_classes_ProcessExecutionException($errorMessage);
			}else{
				$returnValue = $activityExecution->editPropertyValues($this->currentUserProperty, $user->uriResource);
			}
		}
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FE0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method finish
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return boolean
     */
    public function finish( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FF3 begin
		$this->setStatus($activityExecution, 'finished');
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FF3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isFinished
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return boolean
     */
    public function isFinished( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FF7 begin
		$status = $processExecution->getOnePropertyValue($this->activityExecutionStatusProperty);
		if(!is_null($status)){
			if($status->uriResource == INSTANCE_PROCESSSTATUS_FINISHED){
				$returnValue = true;
			}
		}
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FF7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method resume
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return boolean
     */
    public function resume( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000003005 begin
		$this->setStatus($activityExecution, 'resumed');
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000003005 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getActivityExecutionUser
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getActivityExecutionUser( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000002FF9 begin
		$returnValue = $activityExecution->getOnePropertyValue($this->currentUserProperty);
        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000002FF9 end

        return $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getStatus( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000002FFC begin
		$status = $activityExecution->getOnePropertyValue($this->activityExecutionStatusProperty);
		if (!is_null($status)){
			switch($status->uriResource){
				case INSTANCE_PROCESSSTATUS_RESUMED:
				case INSTANCE_PROCESSSTATUS_STARTED:
				case INSTANCE_PROCESSSTATUS_FINISHED:
				case INSTANCE_PROCESSSTATUS_PAUSED:
				case INSTANCE_PROCESSSTATUS_CLOSED:{
					$returnValue = $status;
					break;
				}
			}
			
		}
        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000002FFC end

        return $returnValue;
    }

    /**
     * Short description of method moveForward
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource connector
     * @param  array nextActivities
     * @param  Resource processExecution
     * @return array
     */
    public function moveForward( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $connector, $nextActivities,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000002FFF begin
		
		if(!is_null($activityExecution) && !is_null($connector) && !is_null($processExecution)){
             
            $activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
            
             
            $currentTokens = array();
            $type = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
            switch($type->uriResource){

                /// SEQUENCE & CONDITIONAL ///
                case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:
                case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL:{
                     
                    if(count($nextActivities) == 0){
                        throw new wfEngine_models_classes_ProcessExecutionException("No next activity defined");
                    }
                    if(count($nextActivities) > 1){
                        throw new wfEngine_models_classes_ProcessExecutionException("Too many next activities, only one is required after a conditional or a sequence connector");
                    }
                    $nextActivity = $nextActivities[0];
                    
					$newActivityExecution = $this->duplicateActivityExecutionVariables($activityExecution, $nextActivity, $processExecution);
					if(!is_null($newActivityExecution)){
						//set backward and forward property values:
						$activityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING), $newActivityExecution->uriResource);
						$newActivityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS), $activityExecution->uriResource);
						$returnValue[$newActivityExecution->uriResource] = $newActivityExecution;
					}
		
                    break;
                }
				/// PARALLEL ///
                case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
                    
					foreach($nextActivities as $nextActivity){
						$newActivityExecution = $this->duplicateActivityExecutionVariables($activityExecution, $nextActivity, $processExecution);
						if(!is_null($newActivityExecution)){
							$activityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING), $newActivityExecution->uriResource);
							$newActivityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS), $activityExecution->uriResource);
							$returnValue[$newActivityExecution->uriResource] = $newActivityExecution;
						}
					}
					
                    break;
                }
				/// JOIN ///
                case INSTANCE_TYPEOFCONNECTORS_JOIN:{
					
					$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
					
                    if(count($nextActivities) == 0){
                        throw new wfEngine_models_classes_ProcessExecutionException("No next activity defined");
                    }
                    if(count($nextActivities) > 1){
                        throw new wfEngine_models_classes_ProcessExecutionException("Too many next activities, only one is allowed after a join connector");
                    }
                    $nextActivity = $nextActivities[0];
                    
					//get the activity around the connector
		            $previousActivities = $connector->getPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES));
			
                    $activityResourceArray = array();
                    $mergingActivityExecutions = array();
					$previousActivitiesCount = count($previousActivities);
                    for($i=0; $i<$previousActivitiesCount; $i++){
						$activityResource = new core_kernel_classes_Resource($previousActivities[$i]);
                        if($activityService->isActivity($activityResource)){
                            if(!isset($activityResourceArray[$activityResource->uriResource])){
                                $activityResourceArray[$activityResource->uriResource] = 1;
                            }else{
                                $activityResourceArray[$activityResource->uriResource] += 1;
                            }
                        }
						unset($activityResource);
                    }
                    foreach($activityResourceArray as $activityDefinitionUri => $count){
                        //compare with execution and get tokens:
						$activityDefinition = new core_kernel_classes_Resource($activityDefinitionUri);
                        $previousActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processExecution, $activityDefinition);
                        if(count($previousActivityExecutions) == $count){
                            foreach($previousActivityExecutions as $previousActivityExecution){
                                $mergingActivityExecutions[$previousActivityExecution->uriResource] = $previousActivityExecution;
                            }
                        }else{
                            throw new wfEngine_models_classes_ProcessExecutionException("the number of activity execution does not correspond to the join connector definition (".count($previousActivityExecutions)." against {$count})");
                        }
						unset($activityDefinition);
                    }
                    	
                    //create the token for next activity
                    $newActivityExecution = $this->mergeActivityExecutionVariables($mergingActivityExecutions, $nextActivity, $processExecution);
                    if(!is_null($newActivityExecution)){
						foreach ($mergingActivityExecutions as $oldActivityExecution){
							$oldActivityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING), $newActivityExecution->uriResource);
							$newActivityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS), $oldActivityExecution->uriResource);
						}
						$returnValue[$newActivityExecution->uriResource] = $newActivityExecution;
					}
                    break;
				}	
            }
        }
		//do not forget to set current activity exec after this method execution to 
		
		
        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000002FFF end

        return (array) $returnValue;
    }

    /**
     * Short description of method jump
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource nextActivity
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function jump( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $nextActivity,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000003002 begin
        // section 127-0-1-1-6bd62662:1324d269203:-8000:0000000000003002 end

        return $returnValue;
    }

    /**
     * Short description of method moveBackward
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource processExecution
     * @return array
     */
    public function moveBackward( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-6bd62662:1324d269203:-8000:000000000000300A begin
        // section 127-0-1-1-6bd62662:1324d269203:-8000:000000000000300A end

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicateActivityExecutionVariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource oldActivityExecution
     * @param  Resource newActivityDefinition
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function duplicateActivityExecutionVariables( core_kernel_classes_Resource $oldActivityExecution,  core_kernel_classes_Resource $newActivityDefinition,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:0000000000003001 begin
		
		$excludedProperties = array(
			RDF_LABEL,
			PROPERTY_ACTIVITY_EXECUTION_ACTIVITY,
			PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY,
			PROPERTY_ACTIVITY_EXECUTION_PREVIOUS,
			PROPERTY_ACTIVITY_EXECUTION_FOLLOWING,
			PROPERTY_ACTIVITY_EXECUTION_STATUS
		);
		
		$newActivityExecution = $oldActivityExecution->duplicate($excludedProperties);
		$newActivityExecution->setLabel($newActivityDefinition->getLabel());
		$newActivityExecution->setPropertyValue($this->activityProperty, $newActivityDefinition->uriResource);
		
		if($processExecution->setPropertyValue($this->processInstanceActivityExecutionsProperty, $newActivityExecution->uriResource)){
			$returnValue = $newActivityExecution;
		}
		
        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:0000000000003001 end

        return $returnValue;
    }

    /**
     * Short description of method mergeActivityExecutionVariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array currentActivityExecutions
     * @param  Resource newActivityDefinition
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function mergeActivityExecutionVariables($currentActivityExecutions,  core_kernel_classes_Resource $newActivityDefinition,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:000000000000300B begin
		
		//get tokens variables
        $allVars = array();
        foreach($currentActivityExecutions as $i => $token){
            $allVars[$i] = $this->getVariables($token);
        }

        //merge the variables
        $mergedVars = array();
        foreach($allVars as $tokenVars){
            foreach($tokenVars as $tokenVar){
                $key = $tokenVar['code'];
                foreach($tokenVar['value'] as $value){
                    if(array_key_exists($key, $mergedVars)){
                        if(is_array($mergedVars[$key])){
                            $found = false;
                            foreach($mergedVars[$key] as $tValue){
                                if($tValue instanceof core_kernel_classes_Resource && $value instanceof core_kernel_classes_Resource){
                                    if($tValue->uriResource == $value->uriResource){
                                        $found = true;
                                        break;
                                    }
                                }
                                else if($tValue == $value){
                                    $found = true;
                                    break;
                                }
                            }
                            if(!$found){
                                $mergedVars[$key][] = $value;
                            }
                        }
                        else{
                            $tValue = $mergedVars[$key];
                            if($tValue instanceof core_kernel_classes_Resource && $value instanceof core_kernel_classes_Resource){
                                if($tValue->uriResource != $value->uriResource){
                                    $mergedVars[$key] = array($tValue, $value);
                                }
                            }
                            else if($tValue != $value){
                                $mergedVars[$key] = array($tValue, $value);
                            }
                        }
                    }
                    else{
                        $mergedVars[$key] = $value;
                    }
                }
            }
        }

        //create the merged token
		$newActivityExecution = $this->createActivityExecution($newActivityDefinition, $processExecution);
        if(count($mergedVars) > 0){
            $keys = array();
			$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
			//TODO: use Resource::setPropertyValues() here when implemented to improve performance:
            foreach($mergedVars as $code => $values){
				$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => 0));
                if(!empty($processVariables)){
                    if(count($processVariables) == 1) {
                        if(is_array($values)){
                            foreach($values as $value){
                                $newActivityExecution->setPropertyValue(new core_kernel_classes_Property(array_shift($processVariables)->uriResource), $value);
                            }
                        }
                        else{
                            $newActivityExecution->setPropertyValue(new core_kernel_classes_Property(array_shift($processVariables)->uriResource), $values);
                        }
                    }
                }
                $keys[] = $code;
            }
             
            $newActivityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES), serialize($keys));
        }
		
		if($processExecution->setPropertyValue($this->processInstanceActivityExecutionsProperty, $newActivityExecution->uriResource)){
			$returnValue = $newActivityExecution;
		}
		
        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:000000000000300B end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityExecutionService */

?>