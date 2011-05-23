<?php

error_reporting(E_ALL);

/**
 * Manage the particular executions of a process definition
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
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-includes begin
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-includes end

/* user defined constants */
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-constants begin
// section 127-0-1-1--2bba7ca5:129262ff3bb:-8000:0000000000001FE7-constants end

/**
 * Manage the particular executions of a process definition
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessExecutionService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
	
	public function __construct(){
		parent::__construct();
		$this->classProcessInstances = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$this->propProcessInstacesStatus = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		$this->propProcessInstacesCurrentTokens = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTTOKEN);
		$this->propActivityExecutionsProcessExecution = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
		$this->instanceProcessFinished = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_FINISHED);
		$tis->classActivityExecutions = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
	}
    /**
     * Check the ACL of a user for the given process
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
                        $userRoles 		= $currentUser->getType();
                        if(!is_null($processRole) && is_array($userRoles)){
                        	foreach($userRoles as $userRole){
                        		if($processRole->uriResource == $userRole->uriResource){
                        			return true;
                        		}
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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

    /**
     * Short description of method deleteProcessExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  boolean finishedOnly
     * @return boolean
     */
    public function deleteProcessExecution( core_kernel_classes_Resource $processExecution, $finishedOnly = false)
    {
        $returnValue = (bool) false;

        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D5F begin
		if(wfEngine_helpers_ProcessUtil::checkType($processExecution, $this->classProcessInstances)){
		
			if($finishedOnly){
				if(!$this->isFinished($processExecution)){
					return $returnValue;
				}
			}
			
			//delete associated activity executions
			$activityExecClass = $this->classActivityExecutions;
			$activityExecutions = $activityExecClass->searchInstances(array($this->propActivityExecutionsProcessExecution->uriResource => $processExecution->uriResource), array('like' => false));
			if(count($activityExecutions) > 0){
				foreach($activityExecutions as $activityExecution){
					if($activityExecution instanceof core_kernel_classes_Resource){
						$activityExecution->delete(true);
					}
				}
			}
			
			//delete current tokens:
			$tokenCollection = $processExecution->getPropertyValuesCollection($this->propProcessInstacesCurrentTokens);
			if($tokenCollection->count() > 0){
				foreach($tokenCollection->getIterator() as $token){
					if($token instanceof core_kernel_classes_Resource){
						$token->delete(true);
					}
				}
			}
			
			$returnValue = $processExecution->delete();
		}
        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D5F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteProcessExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array processExecutions
     * @param  boolean finishedOnly
     * @return boolean
     */
    public function deleteProcessExecutions($processExecutions = array(), $finishedOnly = false)
    {
        $returnValue = (bool) false;

        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D68 begin
		if(is_array($processExecutions)){
			if(empty($processExecutions)){
				//get all instances!
				foreach($this->classProcessInstances->getInstances(false) as $processInstance){
					if($finishedOnly){
						if(!$this->isFinished($processInstance)) continue;
					}
					$processExecutions[] = $processInstance;
				}
				
				$deleteTokens = true;
				if($deleteTokens){
					foreach($processExecutions as $processExecution){
						//delete current tokens:
						$tokenCollection = $processExecution->getPropertyValuesCollection($this->propProcessInstacesCurrentTokens);
						if($tokenCollection->count() > 0){
							foreach($tokenCollection->getIterator() as $token){
								if($token instanceof core_kernel_classes_Resource){
									$token->delete();//do not delete ref right now, since it should be done later
								}
							}
						}
					}
				}
				
				$dbWrapper = core_kernel_classes_DbWrapper::singleton();
				$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
				foreach($processExecutions as $processExecution){ 
					$activityExecutionSubject = array();

					$activityExecutionCollection = $apiModel->getSubject($this->propActivityExecutionsProcessExecution->uriResource,  $processExecution->uriResource);
					if($activityExecutionCollection->count() > 0){
						foreach($activityExecutionCollection->getIterator() as $activityExecution){
							$activityExecutionSubject[] = $activityExecution->uriResource;
						}
							
						$queryRemove =  "DELETE FROM statements WHERE subject IN ( ";
						$queryRemove  .= "'".$processExecution->uriResource."',";
						foreach($activityExecutionSubject as $subject){
							$queryRemove  .= "'".$subject."',";
						}
						$queryRemove = substr($queryRemove, 0, strlen($queryRemove) - 1).")";
						
						$dbWrapper->execSql($queryRemove);
					}
				}

			}
			
			foreach($processExecutions as $processExecution){
				if(!is_null($processExecution) && $processExecution instanceof core_kernel_classes_Resource){
					$returnValue = $this->deleteProcessExecution($processExecution, $finishedOnly);
				}
			}
		}
        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D68 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isFinished
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function isFinished( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D78 begin
		$status = $processExecution->getOnePropertyValue($this->propProcessInstacesStatus);
		if($status instanceof core_kernel_classes_Resource){
			if($status->uriResource == $this->instanceProcessFinished->uriResource){
				$returnValue = true;
			}
		}
        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D78 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessExecutionService */

?>