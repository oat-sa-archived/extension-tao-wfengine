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

    // --- OPERATIONS ---

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
        	
        	$processExecutionProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
        	$currentUserProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
        	
        	//retrieve the process containing the activity
        	$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
        	$activityExecutionCollection = $apiModel->getSubject(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY, $activity->uriResource);
        	foreach($activityExecutionCollection->getIterator() as $activityExecution){
        		$activityExecutionUserCollection = $activityExecution->getPropertyValuesCollection($currentUserProp);
				
        		
        		$activityExecutionProcessExecution = $activityExecution->getOnePropertyValue($processExecutionProp);
        		foreach ($activityExecutionUserCollection->getIterator() as $activityExecutionUser) {
    				if(!is_null($activityExecutionUser) && !is_null($activityExecutionProcessExecution)){
    				    if($currentUser->uriResource == $activityExecutionUser->uriResource && $processExecution->uriResource == $activityExecutionProcessExecution->uriResource){
    	        			$returnValue = $activityExecution;
    	        			break;
    	        			break;
    				    }
    	        		
    	        		
    				}
			
        		}
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
          	
          	$processExecutionProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
          	
          	$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
        	$activityExecutionCollection = $apiModel->getSubject(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY, $activity->uriResource);
        	foreach($activityExecutionCollection->getIterator() as $activityExecution){
				$activityExecutionProcessExecution = $activityExecution->getOnePropertyValue($processExecutionProp);
        		
				if(!is_null($activityExecutionProcessExecution)){
	        		if($processExecution->uriResource == $activityExecutionProcessExecution->uriResource){
	        			$returnValue[$activityExecution->uriResource] = $activityExecution;
	        		}
        		}
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
	        	
	        	//create a new activity execution
	        	$execution = $this->createInstance(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION));
	        	
	        	//link it to the activity definition:
	        	$execution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY), $activity->uriResource);
	        	
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
        	
        	$currentUserProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
        	
        	//check if the activity execution isn't already bound
        	if(is_null($activityExecution->getOnePropertyValue($currentUserProp))){

        		//link the current user
	        	$activityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER), $currentUser->uriResource);
	        	
				//link the current process execution
	        	$activityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION), $processExecution->uriResource);
	        	
	        	//in case of role and user restriction, set the current user as activty user
	        	$activityUri	= $activityExecution->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY));
	        	$activity		= new core_kernel_classes_Resource($activityUri);
	        	$mode			= $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE));
	        	if(!is_null($mode)){

	        		$restrictedUserProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_USER);
	        		
	        		if($mode->uriResource == INSTANCE_ACL_ROLE_RESTRICTED_USER){
	        			if(is_null($activity->getOnePropertyValue($restrictedUserProp))){
	        				$activity->setPropertyValue($restrictedUserProp, $currentUser->uriResource);
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

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F62 begin
        
        if(!is_null($activity)){
        	
        	$activityModeProp	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE);
        	$restrictedUserProp	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_USER);
        	$restrictedRoleProp	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE);
        	$rdfsTypeProp		= new core_kernel_classes_Property(RDF_TYPE);
        	$currentUserProp 	= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
        	
        	//activity and current must be set to the activty execution otherwise a common Exception is thrown
        	
        	$modeUri 		= $activity->getOnePropertyValue($activityModeProp);
        	
        	if(is_null($modeUri)){
        		$returnValue = true;	//if no mode defined, the activity is allowed
        	}
        	else{
        		switch($modeUri->uriResource){
        			
        			//check if th current user is the restricted user
        			case INSTANCE_ACL_USER:
        				$activityUser = $activity->getOnePropertyValue($restrictedUserProp);
        				if(!is_null($activityUser)){
	        				if($activityUser->uriResource == $currentUser->uriResource) {
	        					$returnValue = true;
	        				}
        				}
        				break;
        			
        			//check if the current user has the restricted role
        			case INSTANCE_ACL_ROLE:
        				$activityRole 	= $activity->getOnePropertyValue($restrictedRoleProp);
        				$userRoles 		= $currentUser->getPropertyValues($rdfsTypeProp);
						if(!is_null($activityRole) && is_array($userRoles)){
	        				if(in_array($activityRole->uriResource, $userRoles)){
								return true;
							}
						}
        				break;	
        			
        			//check if the current user has the restricted role and is the restricted user
        			case INSTANCE_ACL_ROLE_RESTRICTED_USER:
        				
						
        				//check if an activity execution already exists for the current activity or if there are several in parallel, check if there is one spot available. If so create the activity execution:
						//need to know the current process execution, from it, get the process definition and the number of activity executions associated to it.
						//from the process definition get the number of allowed activity executions for this activity definition (normally only 1 but can be more, for a parallel connector)
						
        				$activityRole 	= $activity->getOnePropertyValue($restrictedRoleProp);
						$userRoles 		= $currentUser->getPropertyValues($rdfsTypeProp);
        				if(!is_null($activityRole) && is_array($userRoles)){
	        				if(in_array($activityRole->uriResource, $userRoles)){
	        					
	        					$activityExecutions = $this->getExecutions($activity, $processExecution);
	        					$estimatedActivityExecutions = $this->getEstimatedExecutionCount($activity);
	        					
	        					if(count($activityExecutions) < $estimatedActivityExecutions){
									$returnValue = true;
	        					}
	        					elseif(!is_null($this->getExecution($activity, $currentUser, $processExecution))){
	        						$returnValue = true;
	        					}
							}
						}
        				break;	
        				
        			//check if the current user has the restricted role and is the restricted user based on the previous activity with the given role
        			case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
        				
        				$activityRole 	= $activity->getUniquePropertyValue($restrictedRoleProp);
        				$userRoles 		= $currentUser->getPropertyValues($rdfsTypeProp);
        				
        				if(!is_null($activityRole) && is_array($userRoles)){
        				
	        				$actsProp 			= new core_kernel_classes_Property(PROCESS_ACTIVITIES);
			
	        				//retrieve the process containing the activity
							$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
	        				$subjects 	= $apiModel->getSubject(PROPERTY_PROCESS_ACTIVITIES, $activity->uriResource);
					        foreach($subjects->getIterator() as $process){
					        	
					        	//get  activities
								foreach ($process->getPropertyValues($actsProp) as $pactivityUri){
									
									$pactivity = new core_kernel_classes_Resource($pactivityUri);
									
					        		//with the same mode
					        		$mode = $pactivity->getOnePropertyValue($activityModeProp);
					        		if($mode->uriResource == INSTANCE_ACL_ROLE_RESTRICTED_USER){
					        			$returnValue = $this->checkAcl($pactivity, $currentUser, $processExecution);
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
    public function getProcessActivities( Process $process,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = array();

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6F begin

        if(!is_null($process) && !is_null($currentUser)){
        	
        	//loop on all the activities of a process
        	foreach($process->activities as $activity){
        		
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

} /* end of class wfEngine_models_classes_ActivityExecutionService */

?>