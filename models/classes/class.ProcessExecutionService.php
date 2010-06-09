<?php

error_reporting(E_ALL);

/**
 * This service enables you to manage, control, restrict the process activities
 *
 * @author firstname and lastname of author, <author@example.org>
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
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('tao/models/classes/class.Service.php');

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
 * @author firstname and lastname of author, <author@example.org>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessExecutionService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    
    /**
     * Check the ACL of a user for a given process.
     * It returns false if the user cannot access the process.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource process
     * @param  Resource currentUser
     * @return boolean
     */
    public function checkAcl( core_kernel_classes_Resource $process,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F62 begin
        
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
						throw new Exception('unknown mode');
        			
        		}
        	}
        }
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F62 end

        return (bool) $returnValue;
    }
    
} /* end of class wfEngine_models_classes_ProcessExecutionService */

?>