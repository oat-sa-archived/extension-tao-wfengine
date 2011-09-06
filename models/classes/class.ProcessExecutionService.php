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
                        $returnValue = true;
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
		if($processExecution->hasType($this->processInstancesClass)){
		
			if($finishedOnly){
				if(!$this->isFinished($processExecution)){
					return $returnValue;
				}
			}
			
			//delete associated activity executions
			$activityExecClass = $this->activityExecutionsClass;
			$activityExecutions = $activityExecClass->searchInstances(array($this->activityExecutionsProcessExecutionProp->uriResource => $processExecution->uriResource), array('like' => false));
			if(count($activityExecutions) > 0){
				foreach($activityExecutions as $activityExecution){
					if($activityExecution instanceof core_kernel_classes_Resource){
						$activityExecution->delete();//no need for the second param to "true" since all the related resources are going to be deleted in this method
					}
				}
			}
			
			//delete current tokens:
			$tokenCollection = $processExecution->getPropertyValuesCollection($this->processInstacesCurrentTokensProp);
			if($tokenCollection->count() > 0){
				foreach($tokenCollection->getIterator() as $token){
					if($token instanceof core_kernel_classes_Resource){
						$token->delete();
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
				foreach($this->processInstancesClass->getInstances(false) as $processInstance){
					if($finishedOnly){
						if(!$this->isFinished($processInstance)) continue;
					}
					$processExecutions[] = $processInstance;
				}
				
				$deleteTokens = true;
				if($deleteTokens){
					foreach($processExecutions as $processExecution){
						//delete current tokens:
						$tokenCollection = $processExecution->getPropertyValuesCollection($this->processInstacesCurrentTokensProp);
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

					$activityExecutionCollection = $apiModel->getSubject($this->activityExecutionsProcessExecutionProp->uriResource,  $processExecution->uriResource);
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
		$returnValue = $this->checkStatus($processExecution, 'finished');
        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D78 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getProcessExecutionsByDefinition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getProcessExecutionsByDefinition( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B8C begin
        if(!is_null($processDefinition)){
                $processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
                $returnValue = $processInstancesClass->searchInstances(array(PROPERTY_PROCESSINSTANCES_EXECUTIONOF => $processDefinition->uriResource));
        }
        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B8C end

        return (array) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B9A begin
		
        parent::__construct();
		
		$this->instanceProcessFinished = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_FINISHED);
		
        $this->processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
        $this->processInstacesStatusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
        $this->processInstacesCurrentTokensProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTTOKEN);
		$this->processInstancesStatusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		$this->processInstancesExecutionOfProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$this->processInstancesProcessPathProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_PROCESSPATH);
		
		$this->processVariablesCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		
		$this->activityExecutionsClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$this->activityExecutionsProcessExecutionProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
		
        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B9A end
    }

    /**
     * Short description of method createProcessExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @param  string name
     * @param  string comment
     * @param  array variablesValues
     * @return core_kernel_classes_Resource
     */
    public function createProcessExecution( core_kernel_classes_Resource $processDefinition, $name, $comment = '', $variablesValues = array())
    {
        $returnValue = null;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F51 begin
		
		if(empty($comment)){
			$comment = "create by processExecutionService on ".date("d-m-Y H:i:s");
		}
		$processInstance = $this->processInstancesClass->createInstance($name, $comment);
		$processInstance->setPropertyValue($this->processInstancesStatusProp, INSTANCE_PROCESSSTATUS_STARTED);
		$processInstance->setPropertyValue($this->processInstancesExecutionOfProp, $processDefinition->uriResource);
		
		$processDefinitionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessDefinitionService');
		$initialActivities = $processDefinitionService->getRootActivities($processDefinition);
		
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		$tokens = array();
		
		foreach ($initialActivities as $activity){
			// Add in path
			//need to be modified to accept activity exec and multiple instance at once (for parallel back and forth 
			$processInstance->setPropertyValue($this->processInstancesProcessPathProp, $activity->uriResource);
			
			$token = $tokenService->create($activity);
			$tokens[] = $token;
		}
		
		//foreach first tokens, assign the user input prop values:
		$codes[] = array();
		foreach($variablesValues as $uri => $value) {
			// have to skip name because doesnt work like other variables
			if($uri != RDFS_LABEL) {
				
				$property = new core_kernel_classes_Property($uri);
				
				//assign property values to them:
				foreach($tokens as $token){
					$token->setPropertyValue($property, $value);
				}
				
				//prepare the array of codes to be inserted as the "variables" property of the current token
				$code = $property->getUniquePropertyValue($this->processVariablesCodeProp);
				$codes[] = (string) $code;
				
			}
		}
		
		//set serialized codes array into variable property:
		$tokenVariableProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
		foreach($tokens as $token){
			$token->setPropertyValue($tokenVariableProp, serialize($codes)); 
		}
		
		
		$tokenService->setCurrents($processInstance, $tokens);
		$returnValue = $processInstance;
		// Feed newly created process.
//		$returnValue->feed();//deprecated
		//get currentActivities
		//get Status
		//get path (activityStack + fullStack);
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F51 end

        return $returnValue;
    }

    /**
     * Short description of method isPaused
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function isPaused( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F63 begin
		$returnValue = $this->checkStatus($processExecution, 'paused');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F63 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isClosed
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function isClosed( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F66 begin
		$returnValue = $this->checkStatus($processExecution, 'closed');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F66 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method pause
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function pause( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F69 begin
		$returnValue = $this->setStatus($processExecution, 'paused');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F69 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method resume
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function resume( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F6C begin
		$returnValue = $this->setStatus($processExecution, 'resumed');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F6C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method finish
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function finish( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F70 begin
		
		$returnValue = $this->setStatus($processExecution, 'finished');
		
		//remove the current tokens
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		$tokenService->setCurrents($processExecution, array());
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F70 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method close
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function close( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F76 begin
		
		$returnValue = $this->setStatus($processExecution, 'closed');
		
		//delete process execution data: activity executions, tokens and remove all process execution properties but label, comment and status (+serialize the execution path?)
		//implementation...
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F76 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  string status
     * @return boolean
     */
    public function setStatus( core_kernel_classes_Resource $processExecution, $status)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F79 begin

		//add status information
		if (!empty($status)){
			if($status instanceof core_kernel_classes_Resource){
				switch($status->uriResource){
					case INSTANCE_PROCESSSTATUS_RESUMED:
					case INSTANCE_PROCESSSTATUS_STARTED:
					case INSTANCE_PROCESSSTATUS_FINISHED:
					case INSTANCE_PROCESSSTATUS_PAUSED:
					case INSTANCE_PROCESSSTATUS_CLOSED:{
						$returnValue = $processExecution->editPropertyValues($this->processInstacesStatusProp, $status->uriResource);
						break;
					}
				}
			}else if(is_string($status)){
				switch($status){
					case 'resumed':{$returnValue = $processExecution->editPropertyValues($this->processInstacesStatusProp, INSTANCE_PROCESSSTATUS_RESUMED);break;}
					case 'started':{$returnValue = $processExecution->editPropertyValues($this->processInstacesStatusProp, INSTANCE_PROCESSSTATUS_STARTED);break;}
					case 'finished':{$returnValue = $processExecution->editPropertyValues($this->processInstacesStatusProp, INSTANCE_PROCESSSTATUS_FINISHED);break;}
					case 'paused':{$returnValue = $processExecution->editPropertyValues($this->processInstacesStatusProp, INSTANCE_PROCESSSTATUS_PAUSED);break;}
					case 'closed':{$returnValue = $processExecution->editPropertyValues($this->processInstacesStatusProp, INSTANCE_PROCESSSTATUS_CLOSED);break;}
				}
			}
			
		}
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F79 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function getStatus( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F7D begin
		
		$status = $processExecution->getOnePropertyValue($this->processInstacesStatusProp);
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
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F7D end

        return $returnValue;
    }

    /**
     * Short description of method checkStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  string status
     * @return boolean
     */
    public function checkStatus( core_kernel_classes_Resource $processExecution, $status)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F80 begin
		
		$processStatus = $this->getStatus($processExecution);
		
		if(!is_null($processStatus)){
			if ($status instanceof core_kernel_classes_Resource) {
				if ($processStatus->uriResource == $status->uriResource) {
					$returnValue = true;
				}
			} else if (is_string($status)) {
				
				
				switch ($processStatus->uriResource) {
					case INSTANCE_PROCESSSTATUS_RESUMED: {
							$returnValue = (strtolower($status) == 'resumed');
							break;
						}
					case INSTANCE_PROCESSSTATUS_STARTED: {
						
							$returnValue = (strtolower($status) == 'started');
							break;
						}
					case INSTANCE_PROCESSSTATUS_FINISHED: {
							$returnValue = (strtolower($status) == 'finished');
							break;
						}
					case INSTANCE_PROCESSSTATUS_PAUSED: {
							$returnValue = (strtolower($status) == 'paused');
							break;
						}
					case INSTANCE_PROCESSSTATUS_CLOSED: {
							$returnValue = (strtolower($status) == 'closed');
							break;
						}
				}
			}
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F80 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method performTransition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @return boolean
     */
    public function performTransition( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F84 begin
		
		Session::setAttribute("activityExecutionUri", $activityExecution->uriResource);
		
		//init the services
		$activityDefinitionService	= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$activityExecutionService 	= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$userService 				= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$tokenService 				= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		$notificationService 		= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_NotificationService');
		
		//get the current user
		$currentUser = $userService->getCurrentUser();
		
		$activityExecution = new core_kernel_classes_Resource($activityExecution->uriResource);
		$activityDefinition = $activityExecution->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY));
		$activityBeforeTransition = $activityDefinition;

		//set the activity execution of the current user as finished:
		if(!is_null($activityExecution)){
			$activityExecution->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED), GENERIS_TRUE);
		}else{
			throw new Exception("cannot find the activity execution of the current activity {$activityBeforeTransition->uriResource} in perform transition");
		}
		
		$nextConnector = $activityDefinitionService->getUniqueNextConnector($activityDefinition);
		$newActivities = array();
		if(!is_null($nextConnector)){
			$newActivities = $this->getNewActivities($activityExecution, $nextConnector);
		}
		
		if($newActivities === false){
			//means that the process must be paused before transition: transition condition not fullfilled
			$this->pause($processExecution);
			return;
		}
		
		// The actual transition starts here:
		
		if(!is_null($nextConnector)){
			
			//transition done here the tokens are "moved" to the next step: even when it is the last, i.e. newActivity empty
			$tokenService->move($nextConnector, $newActivities, $currentUser, $processExecution);
			
			//trigger the notifications
			$notificationService->trigger($nextConnector, $processExecution);
			
		}
		
		//transition done: now get the following activities:
		
		
		//get the current activities, whether the user has the right or not:
		$currentActivities = array();
		foreach($tokenService->getCurrentActivities($processExecution) as $currentActivity){
			
			$newActivity = new wfEngine_models_classes_Activity($currentActivity->uriResource);
			
			//manage path here:
//			$activityBeforeTransitionObject = new wfEngine_models_classes_Activity($activityBeforeTransition->uriResource);
//			$this->path->invalidate($activityBeforeTransitionObject, ($this->path->contains($newActivity) ? $newActivity : null));
//			$this->path->insertActivity($newActivity);// We insert in the ontology the last activity in the path stack.
			
			$currentActivities[] = $newActivity;
		}
		
		//if the connector is not a parallel one, let the user continue in his current branch and prevent the pause:
		$uniqueNextActivity = null;
		if(!is_null($nextConnector)){
			$connectorType = $nextConnector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if($connectorType->uriResource != INSTANCE_TYPEOFCONNECTORS_PARALLEL){
				
				if(count($newActivities)==1){
					//TODO: could do a double check here: if($newActivities[0] is one of the activty found in the current tokens):
					
					if($activityExecutionService->checkAcl($newActivities[0], $currentUser, $processExecution)){
						$uniqueNextActivity = $newActivities[0];//the Activity Object
					}
				}
			}
		}
		
		
		$setPause = true;
		$authorizedActivityDefinitions = array();
		
		if (!count($newActivities) || $activityDefinitionService->isFinal($activityBeforeTransition)){
			//there is no following activity so the process ends here:
			$this->finish($processExecution);
			return;
		}elseif(!is_null($uniqueNextActivity)){
			
			//we are certain what the next activity would be for the user so return it:
			$authorizedActivityDefinitions[] = $uniqueNextActivity;
			$currentActivities = array();
			$currentActivities[] = $uniqueNextActivity;
			$setPause = false;
		}else{
			
			foreach ($currentActivities as $activityAfterTransition){
				//check if the current user is allowed to execute the activity
				if($activityExecutionService->checkAcl($activityAfterTransition, $currentUser, $processExecution)){
					$authorizedActivityDefinitions[] = $activityAfterTransition;
					$setPause = false;
				}
				else{
					continue;
				}
			}
			
		}
		
		//finish actions on the authorized acitivty definitions
		foreach($authorizedActivityDefinitions as $activityAfterTransition){
			
			// Last but not least ... is the next activity a machine activity ?
			// if yes, we perform the transition.
			/*
			 * @todo to be tested
			 */
			
			if ($activityDefinitionService->isHidden($activityAfterTransition)){
				//required to create an activity execution here with:
				
				$currentUser = $userService->getCurrentUser();
				if(is_null($currentUser)){
					throw new Exception("No current user found!");
				}
				//security check if the user is allowed to access this activity
				// if(!$activityExecutionService->checkAcl($activity->resource, $currentUser, $processExecution)){
					// Session::removeAttribute("processUri");
					// $this->redirect(_url('index', 'Main'));
				// }//already performed above...
				
				$activityExecutionResource = $activityExecutionService->initExecution($activityAfterTransition, $currentUser, $processExecution);
				if(!is_null($activityExecutionResource)){
					$this->performTransition($processExecution, $activityExecutionResource);
				}else{
					throw new wfEngine_models_classes_WfException('the activit execution cannot be create for the hidden activity');
				}
				
				//service not executed? use curl request?
			}
		}
		
		if($setPause){
			$this->pause($processExecution);
		}else{
			$this->resume($processExecution);
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F84 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method performBackwardTransition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityResource
     * @return boolean
     */
    public function performBackwardTransition( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityResource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F88 begin
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F88 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCurrentActivities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return array
     */
    public function getCurrentActivities( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        // section 127-0-1-1--1cda705:13239584a17:-8000:0000000000002F81 begin
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		foreach($tokenService->getCurrentActivities($processExecution) as $activity){
			$returnValue[] = $activity;
		}
        // section 127-0-1-1--1cda705:13239584a17:-8000:0000000000002F81 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getNewActivities
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource currentConnector
     * @return mixed
     */
    protected function getNewActivities( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentConnector)
    {
        $returnValue = null;

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F87 begin
		
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		
		$returnValue = array();
		if(is_null($currentConnector)){
			return $returnValue;//certainly the last activity
		}
		
		$connectorType = $connectorService->getType($currentConnector);
//		var_dump($connectorType->getLabel().' '.$connectorType->uriResource);
		if(!($connectorType instanceof core_kernel_classes_Resource)){
			throw new common_Exception('Connector type must be a Resource');
		}
		
		switch ($connectorType->uriResource) {
			case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL : {
				
				$returnValue = $this->getConditionalConnectorNewActivities($activityExecution, $currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_PARALLEL : {

				$returnValue = $connectorService->getNextActivities($currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_JOIN : {
			
				$returnValue = $this->getJoinConnectorNewActivities($activityExecution, $currentConnector);
				
				break;
			}
			default : {
				//considered as a sequential connector
				$newActivities = $connectorService->getNextActivities($currentConnector);
				if(count($newActivities)){
					foreach ($newActivities as $nextActivity){

						if($activityService->isActivity($nextActivity)){
							$returnValue[]= $nextActivity;
						}else if($connectorService->isConnector($nextActivity)){
							$returnValue = $this->getNewActivities($activityExecution, $nextActivity);
						}

						if(!empty($returnValue)){
							break;//since it is a sequential one, stop at the first valid loop:
						}
					}
				}
				break;
			}
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F87 end

        return $returnValue;
    }

    /**
     * Short description of method getConditionalConnectorNewActivities
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource conditionalConnector
     * @return array
     */
    protected function getConditionalConnectorNewActivities( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $conditionalConnector)
    {
        $returnValue = array();

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8B begin
		
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		$transitionRuleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TransitionRuleService');
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		
		$transitionRule = $connectorService->getTransitionRule($conditionalConnector);
		if(is_null($transitionRule)){
			return $returnValue;
		}
		
		$processVarValues = $activityExecutionService->getVariables($activityExecution);
		$evaluationResult = $transitionRuleService->getExpression($transitionRule)->evaluate($processVarValues);

		if ($evaluationResult){
			// next activities = THEN
			$thenActivity = $transitionRuleService->getThenActivity($transitionRule);
			if(!is_null($thenActivity)){
				if($activityService->isActivity($thenActivity)){
					$newActivities[] = $thenActivity;
				}else if($activityService->isConnector($thenActivity)){
					$newActivities = $this->getNewActivities($activityExecution, $thenActivity);
				}
			}else{
				throw new wfEngine_models_classes_ProcessDefinitonException('no "then" activity found for the transition rule '.$transitionRule->uriResource);
			}
		}else{
			// next activities = ELSE
			$elseActivity = $transitionRuleService->getElseActivity($transitionRule);
			if(!is_null($elseActivity)){
				if($activityService->isActivity($elseActivity)){
					$newActivities[] = $elseActivity;
				}else{
					$newActivities = $this->getNewActivities($activityExecution, $elseActivity);
				}
			}else{
				throw new wfEngine_models_classes_ProcessDefinitonException('no "else" activity found for the transition rule '.$transitionRule->uriResource);
			}
			
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getJoinConnectorNewActivities
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource joinConnector
     * @return mixed
     */
    protected function getJoinConnectorNewActivities( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $joinConnector)
    {
        $returnValue = null;

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8F begin
		
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		
		$returnValue = false;
		$completed = false;
				
		//count the number of each different activity definition that has to be done parallely:
		$activityResourceArray = array();
		$prevActivites = $connectorService->getPreviousActivities($activityExecution);
		$countPrevActivities = count($prevActivites);
		for($i=0; $i<$countPrevActivities; $i++){
			$activityResource = $prevActivites[$i];
			if($this->activityService->isActivity($activityResource)){
				if(!isset($activityResourceArray[$activityResource->uriResource])){
					$activityResourceArray[$activityResource->uriResource] = 1;
				}else{
					$activityResourceArray[$activityResource->uriResource] += 1;
				}
			}
		}

		$debug = array();
		$tokenClass = new core_kernel_classes_Class(CLASS_TOKEN);
		$propActivityExecIsFinished = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED);
		$propActivityExecProcessExec = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
		$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);				
		foreach($activityResourceArray as $activityDefinition => $count){
			//get all activity execution for the current activity definition and for the current process execution indepedently from the user (which is not known at the authoring time)

			//get the collection of the activity executions performed for the given actiivty definition:

			$activityExecutions = $activityExecutionClass->searchInstances(array(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY => $activityDefinition), array('like'=>false));

			$activityExecutionArray = array();
			$debug[$activityDefinition] = array();
			foreach($activityExecutions as $activityExecutionResource){
				$processExecutionResource = $activityExecutionResource->getOnePropertyValue($propActivityExecProcessExec);

				$debug[$activityDefinition][$activityExecutionResource->getLabel()] = $processExecutionResource->getLabel().':'.$processExecutionResource->uriResource;
				// $debug[$activityDefinition]['$this->resource->uri'] = $this->resource->uri;

				if(!is_null($processExecutionResource)){
					if($processExecutionResource->uriResource == $this->resource->uriResource){
						//check if the activity execution is associated to a token: 
						//take the activity exec into account only if it is the case:
						$tokens = $tokenClass->searchInstances(array(PROPERTY_TOKEN_ACTIVITYEXECUTION => $activityExecutionResource->uriResource), array('like' => false));
						if(count($tokens)){
							//found one: check if it is finished:
							$isFinished = $activityExecutionResource->getOnePropertyValue($propActivityExecIsFinished);
							if(!$isFinished instanceof core_kernel_classes_Resource || $isFinished->uriResource == GENERIS_FALSE){
								$completed = false;
								break(2); //leave the $completed value as false, no neet to continue
							}else{
								//a finished activity execution for the process execution
								$activityExecutionArray[] = $activityExecutionResource;
							}
						}
					}
				}
			}

			$debug[$activityDefinition]['activityExecutionArray'] = $activityExecutionArray;

			if(count($activityExecutionArray) == $count){
				//ok for this activity definiton, continue to the next loop
				$completed = true;
			}else{
				$completed = false;
				break;
			}
		}

		if($completed){
			$returnValue = array();
			//get THE (unique) next activity
			$returnValue = $connectorService->getNextActivities($joinConnector);//normally, should be only ONE, so could actually break after the first loop
		}else{
			//pause, do not allow transition so return boolean false
			$returnValue = false;
		}
				
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8F end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessExecutionService */

?>