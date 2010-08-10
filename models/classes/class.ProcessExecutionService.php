<?php

error_reporting(E_ALL);

/**
 * Manage the particular executions of a process definition
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
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-includes begin
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-includes end

/* user defined constants */
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-constants begin
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-constants end

/**
 * Manage the particular executions of a process definition
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessExecutionService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Check the ACL of a user for the given process
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource process
     * @param  Resource currentUser
     * @return boolean
     */
    public function checkAcl( core_kernel_classes_Resource $process,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE9 begin
        
      	if(!is_null($process)){
		
        	$processModeProp	= new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE);
        	$restrictedUserProp	= new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_USER);
        	$restrictedRoleProp	= new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE);
        	$rdfsTypeProp		= new core_kernel_classes_Property(RDF_TYPE);
        	
        	//process and current must be set to the activty execution otherwise a common Exception is thrown
        	
        	$modeUri 		= $process->getOnePropertyValue($processModeProp);
        	if(is_null($modeUri) || (string)$modeUri == ''){
        		$returnValue = true;	//if no mode defined, the process is allowed
        	}
        	else{
        		switch($modeUri->uriResource){
        			
        			//check if th current user is the restricted user
        			case INSTANCE_ACL_USER:
        				$processUser = $process->getOnePropertyValue($restrictedUserProp);
        				if(!is_null($processUser)){
	        				if($processUser->uriResource == $currentUser->uriResource) {
	        					$returnValue = true;
	        				}
        				}
        				break;
        			
        			//check if the current user has the restricted role
        			case INSTANCE_ACL_ROLE:
        				$processRole 	= $process->getOnePropertyValue($restrictedRoleProp);
        				$userRoles 		= $currentUser->getPropertyValues($rdfsTypeProp);
						if(!is_null($processRole) && is_array($userRoles)){
	        				if(in_array($processRole->uriResource, $userRoles)){
								return true;
							}
						}
        				break;	
        			default:
						throw new Exception('unknown process init mode');
        			
        		}
        	}
        }
        
        // section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE9 end

        return (bool) $returnValue;
    }

    /**
     * Initialize the current process execution
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activity
     * @param  Resource user
     * @return boolean
     */
    public function initCurrentExecution( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FED begin
        
        if(!is_null($processExecution) && !is_null($activity) && !is_null($user)){
        	
        	//initialise the acitivity execution
        	$activityExecutionService 	= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			$activityExecutionResource = $activityExecutionService->initExecution($activity, $user, $processExecution);
			
			if(!is_null($activityExecutionResource)){
				//dispatch the tokens to the user and assign him
				$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
				$tokenService->dispatch($tokenService->getCurrents($processExecution), $activityExecutionResource);
				$returnValue = true;
			}
		}
        
        // section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FED end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessExecutionService */

?>