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

/**
 * include tao_models_classes_ServiceCacheInterface
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/classes/interface.ServiceCacheInterface.php');

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
        implements tao_models_classes_ServiceCacheInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @param  array value
     * @return boolean
     */
    public function setCache($methodName, $args, $value)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB begin
		if($this->cache){
			
			switch($methodName):
				case __CLASS__.'::getExecutionOf':
				case __CLASS__.'::getStatus':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$processExecution = $args[0];
						if(!isset($this->instancesCache[$processExecution->uriResource])){
							$this->instancesCache[$processExecution->uriResource] = array();
						}
						$this->instancesCache[$processExecution->uriResource][$methodName] = $value;
						$returnValue = true;
					}
					break;
				}
				case __CLASS__.'::getCurrentActivityExecutions':{
					if(count($args) == 1 && isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$processExecution = $args[0];
						if(!isset($this->instancesCache[$processExecution->uriResource])){
							$this->instancesCache[$processExecution->uriResource] = array();
						}
						$this->instancesCache[$processExecution->uriResource][$methodName] = $value;
						$returnValue = true;
					}
					break;
				}
			endswitch;
		}
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return mixed
     */
    public function getCache($methodName, $args = array())
    {
        $returnValue = null;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 begin
		if($this->cache){
			switch($methodName):
				case __CLASS__.'::getCurrentActivityExecutions':{
					if(count($args) != 1){
						//only allow the simplest version of the method
						break;
					}
				}
				case __CLASS__.'::getExecutionOf':
				case __CLASS__.'::getStatus':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$processExecution = $args[0];
						if(isset($this->instancesCache[$processExecution->uriResource])
						&& isset($this->instancesCache[$processExecution->uriResource][$methodName])){

							$returnValue = $this->instancesCache[$processExecution->uriResource][$methodName];

						}
					}
					break;
				}
			endswitch;
//			var_dump($methodName);
		}
		
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 end

        return $returnValue;
    }

    /**
     * Short description of method clearCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return boolean
     */
    public function clearCache($methodName = '', $args = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 begin
		if($this->cache){
			
			if(empty($methodName)){
				$this->instancesCache = array();
				$returnValue = true;
			}

			switch($methodName){
				case __CLASS__.'::getCurrentActivityExecutions': {
					if (count($args) == 1 && isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource) {
						$processExecution = $args[0];
						if(isset($this->instancesCache[$processExecution->uriResource])
						&& $this->instancesCache[$processExecution->uriResource][$methodName]){
							unset($this->instancesCache[$processExecution->uriResource][$methodName]);
							$returnValue = true;
						}
					}else if(count($args) == 2 
						&& isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource
						&& isset($args[1]) && is_array($args[1])){

						$processExecution = $args[0];
						if(isset($this->instancesCache[$processExecution->uriResource])
							&& isset($this->instancesCache[$processExecution->uriResource][$methodName])){

							foreach($args[1] as $activityExecution) {
								if($activityExecution instanceof core_kernel_classes_Resource){
									if(isset($this->instancesCache[$processExecution->uriResource][$methodName][$activityExecution->uriResource])){
										unset($this->instancesCache[$processExecution->uriResource][$methodName][$activityExecution->uriResource]);
									}
								}
							}
							unset($this->instancesCache[$processExecution->uriResource][$methodName]);
							$returnValue = true;
						}
					}
					break;
				}
			}
		}
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 end

        return (bool) $returnValue;
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
             
            $modeUri = $process->getOnePropertyValue($processModeProp);
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
		//deprecated
        if(!is_null($processExecution) && !is_null($activity) && !is_null($user)){
             
            //initialise the acitivity execution
            $activityExecutionResource = $this->activityExecutionService->initExecution($activity, $user, $processExecution);
           
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
		
		$this->instancesCache = array();
		$this->cache = false;
		
		$this->instanceProcessFinished = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_FINISHED);
		$this->instanceProcessResumed = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_RESUMED);
		
        $this->processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
        $this->processInstancesStatusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
        $this->processInstacesCurrentTokensProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTTOKEN);//deprecated
		$this->processInstancesExecutionOfProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$this->processInstancesProcessPathProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_PROCESSPATH);//deprecated
		$this->processInstancesCurrentActivityExecutionsProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS);
		$this->processInstancesActivityExecutionsProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_ACTIVITYEXECUTIONS);
		
		$this->processVariablesCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		
		$this->activityExecutionsClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$this->activityExecutionsProcessExecutionProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
		
		$this->activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
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
		
		$activityExecutions = array();
		
		foreach ($initialActivities as $activity){
			$activityExecution = $this->activityExecutionService->createActivityExecution($activity, $processInstance);
			if(!is_null($activityExecution)){
				$activityExecutions[$activityExecution->uriResource] = $activityExecution;
			}
		}
		
		//foreach first tokens, assign the user input prop values:
		$codes = array();
		foreach($variablesValues as $uri => $value) {
			// have to skip name because doesnt work like other variables
			if($uri != RDFS_LABEL) {
				
				$property = new core_kernel_classes_Property($uri);
				
				//assign property values to them:
				foreach($activityExecutions as $activityExecution){
					$activityExecution->setPropertyValue($property, $value);
				}
				
				//prepare the array of codes to be inserted as the "variables" property of the current token
				$code = $property->getUniquePropertyValue($this->processVariablesCodeProp);
				$codes[] = (string) $code;
				
			}
		}
		
		//set serialized codes array into variable property:
		$propActivityExecutionVariables = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
		foreach($activityExecutions as $activityExecution){
			$activityExecution->setPropertyValue($propActivityExecutionVariables, serialize($codes)); 
		}
		
		if($this->setCurrentActivityExecutions($processInstance, $activityExecutions)){
			$returnValue = $processInstance;
		}
		
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

		if (!empty($status)){
			if($status instanceof core_kernel_classes_Resource){
				switch($status->uriResource){
					case INSTANCE_PROCESSSTATUS_RESUMED:
					case INSTANCE_PROCESSSTATUS_STARTED:
					case INSTANCE_PROCESSSTATUS_FINISHED:
					case INSTANCE_PROCESSSTATUS_PAUSED:
					case INSTANCE_PROCESSSTATUS_CLOSED:{
						$returnValue = $processExecution->editPropertyValues($this->processInstancesStatusProp, $status->uriResource);
						break;
					}
				}
			}else if(is_string($status)){
				$status = strtolower(trim($status));
				switch($status){
					case 'resumed':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_RESUMED);
						break;
					}
					case 'started':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_STARTED);
						break;
					}
					case 'finished':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_FINISHED);
						break;
					}
					case 'paused':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_PAUSED);
						break;
					}
					case 'closed':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_CLOSED);
						break;
					}
				}
				if($status instanceof core_kernel_classes_Resource){
					$returnValue = $processExecution->editPropertyValues($this->processInstancesStatusProp, $status->uriResource);
				}
			}
			
			if($returnValue){
				$this->setCache(__CLASS__.'::getStatus', array($processExecution), $status);
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
		$returnValue = $this->getCache(__METHOD__, array($processExecution));
		if(empty($returnValue)){
			
			$status = $processExecution->getOnePropertyValue($this->processInstancesStatusProp);
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
			
			$this->setCache(__METHOD__, array($processExecution), $returnValue);
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
     * @return array
     */
    public function performTransition( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F84 begin
		
		Session::setAttribute("activityExecutionUri", $activityExecution->uriResource);
		
		//init the services
		$activityDefinitionService	= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$connectorService			= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		$userService 				= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$notificationService 		= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_NotificationService');
		
		$currentUser = $userService->getCurrentUser();
		
		//set the activity execution of the current user as finished:
		if($activityExecution->exists()){
			$this->activityExecutionService->finish($activityExecution);
		}else{
			throw new Exception("cannot find the activity execution of the current activity {$activityBeforeTransition->uriResource} in perform transition");
		}
		
		$activityBeforeTransition = $this->activityExecutionService->getExecutionOf($activityExecution);
		$nextConnector = $activityDefinitionService->getUniqueNextConnector($activityBeforeTransition);
		$newActivities = array();
		if(!is_null($nextConnector)){
			$newActivities = $this->getNewActivities($processExecution, $activityExecution, $nextConnector);
		}
		
		if($newActivities === false){
			//means that the process must be paused before transition: transition condition not fullfilled
			$this->pause($processExecution);
			return $returnValue;
		}
		
		// The actual transition starts here:
		if(!is_null($nextConnector)){
			
			//trigger the forward transition:
			$newActivityExecutions = $this->activityExecutionService->moveForward($activityExecution, $nextConnector, $newActivities, $processExecution);
			
			//trigger the notifications
			$notificationService->trigger($nextConnector, $processExecution);
			
		}
		
		//transition done from here: now get the following activities:
		
		$currentActivities = $newActivities;
		//if the connector is not a parallel one, let the user continue in his current branch and prevent the pause:
		$uniqueNextActivity = null;
		if(!is_null($nextConnector)){
			if($connectorService->getType($nextConnector)->uriResource != INSTANCE_TYPEOFCONNECTORS_PARALLEL){
				
				if(count($newActivities)==1){
					//TODO: could do a double check here: if($newActivities[0] is one of the activty found in the current tokens):
					
					if($this->activityExecutionService->checkAcl($newActivities[0], $currentUser, $processExecution)){
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
			//we are certain that the next activity would be for the user so return it:
			$authorizedActivityDefinitions[$uniqueNextActivity->uriResource] = $uniqueNextActivity;
			$setPause = false;
		}else{
			
			foreach ($currentActivities as $activityAfterTransition){
				//check if the current user is allowed to execute the activity
				if($this->activityExecutionService->checkAcl($activityAfterTransition, $currentUser, $processExecution)){
					$authorizedActivityDefinitions[$activityAfterTransition->uriResource] = $activityAfterTransition;
					$setPause = false;
				}
				else{
					continue;
				}
			}
			
		}
		
		//finish actions on the authorized acitivty definitions
		foreach($authorizedActivityDefinitions as $uri => $activityAfterTransition){
			
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
				
				$activityExecutionResource = $this->initCurrentActivityExecutions($activityAfterTransition, $currentUser, $processExecution);
				//service not executed? use curl request?
				if(!is_null($activityExecutionResource)){
					$followingActivities = $this->performTransition($processExecution, $activityExecutionResource);
					unset($authorizedActivityDefinitions[$uri]);
					foreach($followingActivities as $followingActivity){
						$authorizedActivityDefinitions[$followingActivity->uriResource] = $followingActivity;
					}
				}else{
					throw new wfEngine_models_classes_WfException('the activity execution cannot be create for the hidden activity');
				}
				
				
			}
		}
		
		if($setPause){
			$this->pause($processExecution);
		}else{
			$this->resume($processExecution);
		}
		
		$returnValue = $authorizedActivityDefinitions;
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F84 end

        return (array) $returnValue;
    }

    /**
     * Short description of method performBackwardTransition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @return array
     */
    public function performBackwardTransition( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F88 begin
		
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
			
		$newActivityExecutions = $this->activityExecutionService->moveBackward($activityExecution, $processExecution);
		$count = count($newActivityExecutions);
		
		$newActivityDefinitions = array();
		
		if($count){
			//see if needs to go back again
			foreach($newActivityExecutions as $newActivityExecution){
				$newActivityDefinition = $this->activityExecutionService->getExecutionOf($newActivityExecution);
				if($activityService->isHidden($newActivityDefinition) && !$activityService->isInitial($newActivityDefinition)){
					$newNewActivityDefinitions = $this->performBackwardTransition($processExecution, $newActivityExecution);
					foreach($newNewActivityDefinitions as $newNewActivityDefinition){
						$newActivityDefinitions[$newNewActivityDefinition->uriResource] = $newNewActivityDefinition;
					}
				}else{
					$newActivityDefinitions[$newActivityDefinition->uriResource] = $newActivityDefinition;
				}
			}
			
			if($count == 1){
				$this->resume($processExecution);
			}else{
				$this->pause($processExecution);
			}
			
			$returnValue = $newActivityDefinitions;
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F88 end

        return (array) $returnValue;
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
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @param  Resource currentConnector
     * @return mixed
     */
    protected function getNewActivities( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentConnector)
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
		if(!($connectorType instanceof core_kernel_classes_Resource)){
			throw new common_Exception('Connector type must be a Resource');
		}
		
		switch ($connectorType->uriResource) {
			case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL:{
				
				$returnValue = $this->getConditionalConnectorNewActivities($activityExecution, $currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{

				$returnValue = $connectorService->getNextActivities($currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_JOIN:{
			
				$returnValue = $this->getJoinConnectorNewActivities($processExecution, $currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:
			default:{
				//considered as a sequential connector
				$newActivities = $connectorService->getNextActivities($currentConnector);
				if(count($newActivities)){
					foreach ($newActivities as $nextActivity){

						if($activityService->isActivity($nextActivity)){
							$returnValue[]= $nextActivity;
						}else if($connectorService->isConnector($nextActivity)){
							$returnValue = $this->getNewActivities($processExecution, $activityExecution, $nextActivity);
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
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @param  Resource conditionalConnector
     * @return array
     */
    protected function getConditionalConnectorNewActivities( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $conditionalConnector)
    {
        $returnValue = array();

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8B begin
		
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		$transitionRuleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TransitionRuleService');
		
		$transitionRule = $connectorService->getTransitionRule($conditionalConnector);
		if(is_null($transitionRule)){
			return $returnValue;
		}
		
		$processVarValues = $this->activityExecutionService->getVariables($activityExecution);
		$evaluationResult = $transitionRuleService->getExpression($transitionRule)->evaluate($processVarValues);

		if ($evaluationResult){
			// next activities = THEN
			$thenActivity = $transitionRuleService->getThenActivity($transitionRule);
			if(!is_null($thenActivity)){
				if($activityService->isActivity($thenActivity)){
					$newActivities[] = $thenActivity;
				}else if($activityService->isConnector($thenActivity)){
					$newActivities = $this->getNewActivities($processExecution, $activityExecution, $thenActivity);
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
					$newActivities = $this->getNewActivities($processExecution, $activityExecution, $elseActivity);
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
     * @param  Resource processExecution
     * @param  Resource joinConnector
     * @return mixed
     */
    protected function getJoinConnectorNewActivities( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $joinConnector)
    {
        $returnValue = null;

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8F begin
		
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		
		$returnValue = false;
		$completed = false;
				
		//count the number of each different activity definition that has to be done parallely:
		$activityResourceArray = array();
		$prevActivites = $connectorService->getPreviousActivities($joinConnector);
		$countPrevActivities = count($prevActivites);
		for($i=0; $i<$countPrevActivities; $i++){
			$activityResource = $prevActivites[$i];
			if($activityService->isActivity($activityResource)){
				if(!isset($activityResourceArray[$activityResource->uriResource])){
					$activityResourceArray[$activityResource->uriResource] = 1;
				}else{
					$activityResourceArray[$activityResource->uriResource] += 1;
				}
			}
		}

		$debug = array();
		//count finished activity execution by activity definition
		foreach($activityResourceArray as $activityDefinitionUri => $count){
			
			$activityDefinition = new core_kernel_classes_Resource($activityDefinitionUri);
			$debug[$activityDefinitionUri] = array();
			$activityExecutionArray = array();
			
			//get all activity execution for the current activity definition and for the current process execution indepedently from the user (which is not known at the authoring time)
			$activityExecutions = $this->getCurrentActivityExecutions($processExecution, $activityDefinition);
			foreach($activityExecutions as $activityExecutionResource){
				
				if($this->activityExecutionService->isFinished($activityExecutionResource)){
					//a finished activity execution for the process execution
					$activityExecutionArray[] = $activityExecutionResource;
				}else{
					$completed = false;
					break(2); //leave the $completed value as false, no neet to continue
				}
				
			}

			$debug[$activityDefinitionUri]['activityExecutionArray'] = $activityExecutionArray;

			if(count($activityExecutionArray) == $count){
				//ok for this activity definiton, continue to the next loop
				$completed = true;
			}else{
				$completed = false;
				break;
			}
		}
		
		if($completed){
			//get THE (unique) next activity
			$returnValue = $connectorService->getNextActivities($joinConnector);//normally, should be only ONE, so could actually break after the first loop
		}else{
			//pause, do not allow transition so return boolean false
			$returnValue = false;
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8F end

        return $returnValue;
    }

    /**
     * Short description of method getExecutionOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function getExecutionOf( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB6 begin
		
		$returnValue = $this->getCache(__METHOD__, array($processExecution));
		if(empty($returnValue)){
			$returnValue = $processExecution->getUniquePropertyValue($this->processInstancesExecutionOfProp);
			$this->setCache(__METHOD__, array($processExecution), $returnValue);
		}
		
        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB6 end

        return $returnValue;
    }

    /**
     * Short description of method setCurrentActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  array activityExecutions
     * @return boolean
     */
    public function setCurrentActivityExecutions( core_kernel_classes_Resource $processExecution, $activityExecutions)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FC7 begin
		
		if(!is_null($processExecution)){
            if(!is_array($activityExecutions) && !empty($activityExecutions) && $activityExecutions instanceof core_kernel_classes_Resource){
                $activityExecutions = array($activityExecutions);
            }
			if(is_array($activityExecutions)){
				foreach($activityExecutions as $activityExecution){
					$returnValue = $processExecution->setPropertyValue($this->processInstancesCurrentActivityExecutionsProp, $activityExecution->uriResource);
				}
				//associative array mendatory in cache!
				$this->setCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution), $activityExecutions);
			}
        }
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FC7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCurrentActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityDefinition
     * @param  string user
     * @return array
     */
    public function getCurrentActivityExecutions( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityDefinition = null, $user = null)
    {
        $returnValue = array();

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FCD begin
		
		//TODO: to be cached !!!
		
		$allCurrentActivityExecutions = array();
		
		$cachedValues = $this->getCache(__METHOD__,array($processExecution));
		if(!is_null($cachedValues)){
			$allCurrentActivityExecutions = $cachedValues;
		}else{
			$currentActivityExecutions = $processExecution->getPropertyValues($this->processInstancesCurrentActivityExecutionsProp);
			$count = count($currentActivityExecutions);
			for($i=0;$i<$count;$i++){
				$uri = $currentActivityExecutions[$i];
				if(common_Utils::isUri($uri)){
					$allCurrentActivityExecutions[$uri] = new core_kernel_classes_Resource($uri);
				}
			}
			$this->setCache(__METHOD__,array($processExecution), $allCurrentActivityExecutions);
		}
		
		if(is_null($activityDefinition) && is_null($user)){
			
			$returnValue = $allCurrentActivityExecutions;
			
		}else{
			//search by criteria:
			$propertyFilter = array(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION =>	$processExecution->uriResource);
			if(!is_null($activityDefinition)){
				$propertyFilter[PROPERTY_ACTIVITY_EXECUTION_ACTIVITY] = $activityDefinition->uriResource;
			}
			if(!is_null($user) && $user instanceof core_kernel_classes_Resource){
				$propertyFilter[PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER] = $user->uriResource;
			}
				
			$foundActivityExecutions = $this->activityExecutionsClass->searchInstances($propertyFilter, array('like' => false, 'recursive' => false));
			$returnValue = array_intersect_key($allCurrentActivityExecutions, $foundActivityExecutions);
			
			//special case:check if we want an 'empty-user' activityExecution:
			if(!is_null($activityDefinition) && is_string($user) && empty($user)){
				
				foreach($returnValue as $uri => $currentActivityExecution){
					if(!is_null($this->activityExecutionService->getActivityExecutionUser($currentActivityExecution))){
						unset($returnValue[$uri]);
					}
				}
			}
		}
        
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FCD end

        return (array) $returnValue;
    }

    /**
     * Create or retrieve the current activity execution of a process execution
     * a given activity definition and a user
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityDefinition
     * @param  Resource user
     * @return core_kernel_classes_Resource
     */
    public function initCurrentActivityExecution( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityDefinition,  core_kernel_classes_Resource $user)
    {
        $returnValue = null;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FD5 begin
		
		if(!is_null($processExecution) && !is_null($activityDefinition) && !is_null($user)){
             
			//find if the an user is already given a *current* activity execution (among the possible one), given an activity definition:
			$activityExecutions = $this->getCurrentActivityExecutions($processExecution, $activityDefinition, $user);
			
			if(empty($activityExecutions)){
				//not found, so retrieve *available* ones and assign it to the user:
				$activityExecutionsByActivity = $this->getCurrentActivityExecutions($processExecution, $activityDefinition, '');
				foreach($activityExecutionsByActivity as $activityExec){
					//if user empty, bind execution to current 
					if($this->activityExecutionService->setActivityExecutionUser($activityExec, $user)){
						$returnValue = $activityExec;
						$this->activityExecutionService->setStatus($activityExec, 'started');//or "resumed"?
						break;
					}
				}
			}else if(count($activityExecutions) == 1){
				//the activity execution for the given activity definiiton and the current user is already exists, so retirieve it and set the execution to the status resumed:
				$currentActivityExec = array_pop($activityExecutions);
				if($this->activityExecutionService->resume($currentActivityExec)){
					$returnValue = $currentActivityExec;
				}
			}else{
				throw new wfEngine_models_classes_ProcessExecutionException('too many activity executions found');
			}
        }
		
		//if the returnValue is empty here, it means that there is no more activity execution available for that user...
		
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FD5 end

        return $returnValue;
    }

    /**
     * Short description of method getAvailableCurrentActivityDefinitions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource currentUser
     * @param  boolean checkACL
     * @return array
     */
    public function getAvailableCurrentActivityDefinitions( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $currentUser, $checkACL = false)
    {
        $returnValue = array();

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FE5 begin
		
		$currentActivityExecutions = $this->getCurrentActivityExecutions($processExecution);
		$propActivityExecutionCurrentUser = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
		foreach($currentActivityExecutions as $currentActivityExecution){
			$ok = false;
			$activityDefinition = null;
			$assignedUser = $currentActivityExecution->getOnePropertyValue($propActivityExecutionCurrentUser);
			if(!is_null($assignedUser)){
				if($assignedUser->uriResource == $currentUser->uriResource){
					$ok = true;
				}
			}else{
				if($checkACL){
					$activityDefinition = $this->activityExecutionService->getExecutionOf($currentActivityExecution);
					if($this->activityExecutionService->checkACL($activityDefinition, $currentUser, $processExecution)){
						$ok = true;
					}
				}else{
					$ok = true;
				}
			}
			
			if($ok){
				if(is_null($activityDefinition)){
					$activityDefinition = $this->activityExecutionService->getExecutionOf($currentActivityExecution);
				}
				$returnValue[$activityDefinition->uriResource] = $activityDefinition;
			}
		}
		
		//suggestion: check ACL on the return values of this method
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FE5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method removeCurrentActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  array activityExecutions
     * @return boolean
     */
    public function removeCurrentActivityExecutions( core_kernel_classes_Resource $processExecution, $activityExecutions = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:0000000000003022 begin
		
		if(!is_null($processExecution)){
            if(!is_array($activityExecutions) && !empty($activityExecutions) && $activityExecutions instanceof core_kernel_classes_Resource){
                $activityExecutions = array($activityExecutions);
            }
			if(is_array($activityExecutions)){
				if(empty($activityExecutions)){
					$returnValue = $processExecution->removePropertyValues($this->processInstancesCurrentActivityExecutionsProp);
					if($returnValue){
						$this->clearCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution));
					}
				
				}else{
					$removePattern = array();
					foreach($activityExecutions as $activityExecution){
						$removePattern[] = $activityExecution->uriResource;
					}
					
					$returnValue = $processExecution->removePropertyValues($this->processInstancesCurrentActivityExecutionsProp, array(
						'like' => false,
						'pattern' => $removePattern
					));
					
					if($returnValue){
						$this->clearCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution, $activityExecutions));
					}
				}
				
				if($returnValue){
					$this->clearCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution, $activityExecutions));
				}
			}
        }
		
        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:0000000000003022 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processInstance
     * @return array
     */
    public function getAllActivityExecutions( core_kernel_classes_Resource $processInstance)
    {
        $returnValue = array();

        // section 127-0-1-1--1e75179b:1325dc5c4e1:-8000:0000000000003012 begin
		
		$previousProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS);
		$followingProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING);
					
					
		$currentActivityExecutions = $this->getCurrentActivityExecutions($processInstance);
		
		$allActivityExecutions = $processInstance->getPropertyValues($this->processInstancesActivityExecutionsProp);
		$count = count($allActivityExecutions);
		for($i=0;$i<$count;$i++){
			$uri = $allActivityExecutions[$i];
			if(common_Utils::isUri($uri)){
				$activityExecution = new core_kernel_classes_Resource($uri);
				$activityDefinition = $this->activityExecutionService->getExecutionOf($activityExecution);
				$previousArray = array();
				$followingArray = array();

				$previous = $activityExecution->getPropertyValues($previousProperty);
				$countPrevious = count($previous);
				for($j=0; $j<$countPrevious; $j++){
					if(common_Utils::isUri($previous[$j])){
						$prevousActivityExecution = new core_kernel_classes_Resource($previous[$j]);
						$previousArray[$prevousActivityExecution->uriResource] = $prevousActivityExecution->getLabel();
					}
				}

				$following = $activityExecution->getPropertyValues($followingProperty);
				$countFollowing = count($following);
				for($k=0; $k<$countFollowing; $k++){
					if(common_Utils::isUri($following[$k])){
						$followingActivityExecution = new core_kernel_classes_Resource($following[$k]);
						$followingArray[$followingActivityExecution->uriResource] = $followingActivityExecution->getLabel();
					}
				}
				
				$user = $this->activityExecutionService->getActivityExecutionUser($activityExecution);
				
				$status = $this->activityExecutionService->getStatus($activityExecution);
				$returnValue[$uri] = array(
					'label' => $activityExecution->getLabel(),
					'executionOf' => $activityDefinition->getLabel().' ('.$activityDefinition->uriResource.')',
					'user' => (is_null($user))?'none':$user->getLabel().' ('.$user->uriResource.')',
					'status' => (is_null($status))?'none':$status->getLabel(),
					'current' => array_key_exists($activityExecution->uriResource, $currentActivityExecutions),
					'previous' => $previousArray,
					'following' => $followingArray
				);
			}
		}
		
		ksort($returnValue);
        // section 127-0-1-1--1e75179b:1325dc5c4e1:-8000:0000000000003012 end

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessExecutionService */

?>