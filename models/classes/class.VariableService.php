<?php

error_reporting(E_ALL);

/**
 * Enable you to manage the process variables
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
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes end

/* user defined constants */
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants end

/**
 * Enable you to manage the process variables
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_VariableService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * save a list of process variable by key/value pair
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array variable
     * @return mixed
     */
    public function save($variable)
    {
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 begin
		if(Session::hasAttribute("activityExecutionUri")){
			
			Bootstrap::loadConstants('wfEngine');	//because it could be called anywhere
			
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			if(is_null($token)) {
					throw new Exception('Activity Token should never be null');
			}
			
			$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
			$newVar = unserialize($token->getOnePropertyValue($tokenVarProp));
			foreach($variable as $k => $v) {
				$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $k), array('like' => false));
				if(!empty($processVariables)){
					if(count($processVariables) == 1) {
						$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);

						$returnValue &= $token->editPropertyValues($property,$v);
						if(is_array($newVar)){
							$newVar = array_merge($newVar, array($k)); 
						}
						else{
							$newVar = array($k);
						}
					}
				}
				
			}
			$returnValue &= $token->editPropertyValues($tokenVarProp,serialize($newVar));
		}
		
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 end
    }

    /**
     * Remove the variables in parameter (list the keys)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  mixed params
     * @return boolean
     */
    public function remove($params)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B begin
		if(Session::hasAttribute("activityExecutionUri")){
			
			Bootstrap::loadConstants('wfEngine');	//because it could be called anywhere
			
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			if(is_null($token)) {
					throw new Exception('Activity Token should never be null');
			}
			
			$oldVar = unserialize($token->getOnePropertyValue($tokenVarProp));
			if(is_string($params)){
				$params = array($params);
			}
			
			if(is_array($params)){
				$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
				foreach($params as $param) {
					if(in_array($param,$oldVar)){
						$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $param), array('like' => false));
						if(!empty($processVariables)){
							if(count($processVariables) == 1) {
								$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);
								
								$returnValue &= $token->removePropertyValues($property);
								$oldVar = array_diff($oldVar,array($param));
							}
						}
					}
				}
				$returnValue &= $token->editPropertyValues($tokenVarProp,serialize($oldVar));
			}
		}

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B end

        return (bool) $returnValue;
    }

    /**
     * get the variable matching the key
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string key
     * @return mixed
     */
    public function get($key)
    {
        $returnValue = null;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E begin
		if(Session::hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			if(is_null($token)) {
				throw new Exception('Activity Token should never be null');
			}
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			$vars = unserialize($token->getOnePropertyValue($tokenVarProp));
			if(in_array($key,$vars)){
				$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
				$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $key), array('like' => false));
				if(!empty($processVariables)){
					if(count($processVariables) == 1) {
						$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);
						$values = $token->getPropertyValuesCollection($property);
						if($values->count() == 1){
							$returnValue = $values->get(0);
						}
						if($values->count() > 1){
							$returnValue = (array)$values->getIterator();
						}
					}
				}
			}
		}
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E end

        return $returnValue;
    }

    /**
     * Get all the v ariables
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getAll()
    {
        $returnValue = array();

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 begin
		if(Session::hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			if(is_null($token)) {
				throw new Exception('Activity Token should never be null');
			}
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			$vars = unserialize($token->getOnePropertyValue($tokenVarProp));
			
			$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
				$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false));
               
			foreach($vars as $code){
				$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false));
				if(!empty($processVariables)){
					if(count($processVariables) == 1) {
						$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);
						$values = $token->getPropertyValuesCollection($property);
						if($values->count() == 1){
							$returnValue[$code] = $values->get(0);
						}
						if($values->count() > 1){
							$returnValue[$code] = (array)$values->getIterator();
						}
					}
				}
			}

		}
		
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 end

        return (array) $returnValue;
    }

    /**
     * add a variable (different of save in case of multiple values)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string key
     * @param  string value
     * @return mixed
     */
    public function push($key, $value)
    {
        // section 127-0-1-1--55065e1d:1294a729605:-8000:0000000000002006 begin
        
    	if(Session::hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			if(is_null($token)) {
					throw new Exception('Activity Token should never be null');
			}
			
			$newVar = unserialize($token->getOnePropertyValue($tokenVarProp));
			
			$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
			$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $key), array('like' => false));
			if(!empty($processVariables)){
				if(count($processVariables) == 1) {
					$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);
					
					$returnValue &= $token->setPropertyValue($property, $value);
					if(is_array($newVar)){
						$newVar = array_merge($newVar, array($key)); 
					}
					else{
						$newVar = array($key);
					}
				}
			}
				
			$returnValue &= $token->editPropertyValues($tokenVarProp, serialize($newVar));
		}
    	
        // section 127-0-1-1--55065e1d:1294a729605:-8000:0000000000002006 end
    }

} /* end of class wfEngine_models_classes_VariableService */

?>