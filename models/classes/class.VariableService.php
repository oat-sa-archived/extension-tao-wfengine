<?php

error_reporting(E_ALL);

/**
 * Enable you to manage the process variables
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
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes end

/* user defined constants */
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants end

/**
 * Enable you to manage the process variables
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array variable
     * @return boolean
     */
    public function save($variable)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 begin
    	
		if(Session::hasAttribute("activityExecutionUri")){
			
			Bootstrap::loadConstants('wfEngine');	//because it could be called anywhere
			
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$variablesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
			$newVar = unserialize($activityExecution->getOnePropertyValue($variablesProp));
			
			$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
			foreach($variable as $k => $v) {
				$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $k), array('like' => false));
				if(!empty($processVariables)){
					if(count($processVariables) == 1) {
						$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);

						$returnValue &= $activityExecution->editPropertyValues($property,$v);
						if(is_array($newVar)){
							$newVar = array_merge($newVar, array($k)); 
						}
						else{
							$newVar = array($k);
						}
					}
				}
				
			}
			$returnValue &= $activityExecution->editPropertyValues($variablesProp, serialize($newVar));
		}
		
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 end

        return (bool) $returnValue;
    }

    /**
     * Remove the variables in parameter (list the keys)
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  mixed params
     * @return boolean
     */
    public function remove( mixed $params)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B begin
		if(Session::hasAttribute("activityExecutionUri")){
			
			Bootstrap::loadConstants('wfEngine');	//because it could be called anywhere
			
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$variablesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
			$oldVar = unserialize($activityExecution->getOnePropertyValue($variablesProp));
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
								
								$returnValue &= $activityExecution->removePropertyValues($property);
								$oldVar = array_diff($oldVar,array($param));
							}
						}
					}
				}
				$returnValue &= $activityExecution->editPropertyValues($variablesProp, serialize($oldVar));
			}
		}

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B end

        return (bool) $returnValue;
    }

    /**
     * get the variable matching the key
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string key
     * @return mixed
     */
    public function get($key)
    {
        $returnValue = null;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E begin
		if(Session::hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			$variablesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
			$vars = unserialize($activityExecution->getOnePropertyValue($variablesProp));
			if(in_array($key,$vars)){
				$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
				$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $key), array('like' => false));
				if(!empty($processVariables)){
					if(count($processVariables) == 1) {
						$property = new core_kernel_classes_Property(reset($processVariables)->uriResource);
						$values = $activityExecution->getPropertyValuesCollection($property);
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getAll()
    {
        $returnValue = array();

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 begin
		if(Session::hasAttribute("activityExecutionUri")){
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$variablesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
			$vars = unserialize($activityExecution->getOnePropertyValue($variablesProp));
			
			$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
			
            if(is_array($vars)){
				foreach($vars as $code){
					$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false));
					if(!empty($processVariables)){
						if(count($processVariables) == 1) {
							$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);
							$values = $activityExecution->getPropertyValuesCollection($property);
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
		}
		
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 end

        return (array) $returnValue;
    }

    /**
     * add a variable (different of save in case of multiple values)
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string key
     * @param  string value
     * @return boolean
     */
    public function push($key, $value)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--55065e1d:1294a729605:-8000:0000000000002006 begin
        
    	if(Session::hasAttribute("activityExecutionUri")){
			
			$activityExecution = new core_kernel_classes_Resource(Session::getAttribute("activityExecutionUri"));
			
			$variablesProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
			$newVar = unserialize($activityExecution->getOnePropertyValue($variablesProp));
			
			$processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
			$processVariables = $processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $key), array('like' => false));
			if(!empty($processVariables)){
				if(count($processVariables) == 1) {
					$property = new core_kernel_classes_Property(array_shift($processVariables)->uriResource);
					
					$returnValue &= $activityExecution->setPropertyValue($property, $value);
					if(is_array($newVar)){
						$newVar = array_merge($newVar, array($key)); 
					}
					else{
						$newVar = array($key);
					}
				}
			}
				
			$returnValue &= $activityExecution->editPropertyValues($variablesProp, serialize($newVar));
		}
    	
        // section 127-0-1-1--55065e1d:1294a729605:-8000:0000000000002006 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string code
     * @param  boolean forceCreation
     * @return core_kernel_classes_Resource
     */
    public function getProcessVariable($code, $forceCreation = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F0C begin
		
		$processVariableClass =  new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$variables = $processVariableClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => 0));
		if(!empty($variables)){
			$returnValue = array_shift($variables);
		}else if($forceCreation){
			$returnValue = $this->createProcessVariable($code, $code);
			if(is_null($returnValue)){
				throw new Exception("the process variable ({$code}) cannot be created.");
			}
		}
		
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F0C end

        return $returnValue;
    }

    /**
     * Short description of method createProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string label
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public function createProcessVariable($label = '', $code = '')
    {
        $returnValue = null;

        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003073 begin
		
		if(!empty($code) && $this->getProcessVariable($code)){
			throw new Exception("A process variable with the code '{$code}' already exists");
		}
		
		$classCode = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		if(empty($label)){
			$label = "Process variable";
            if(!empty($code)){
				$label .= " ".$code;
			}
		}
		$returnValue = $this->createInstance($classCode, $label);
		
		if(!empty($code)){
			$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE), $code);
		}
		
		//set the new instance of process variable as a property of the class process instance:
		$ok = $returnValue->setType(new core_kernel_classes_Class(RDF_PROPERTY));
		if($ok){
			$newTokenProperty = new core_kernel_classes_Property($returnValue->uriResource);
			$newTokenProperty->setDomain(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION));
			$newTokenProperty->setRange(new core_kernel_classes_Class(RDFS_LITERAL));//literal only!
			$newTokenProperty->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MULTIPLE), GENERIS_TRUE);
		}else{
			throw new Exception("the newly created process variable {$label} ({$returnValue->uriResource}) cannot be set as a property of the class Token");
		}
		
		
        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003073 end

        return $returnValue;
    }

    /**
     * Short description of method deleteProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string code
     * @return boolean
     */
    public function deleteProcessVariable($code)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003089 begin
		
		if(!is_null($code)){
		
			$processVariableToDelete = null;
			$processVariableToDelete = $this->getProcessVariable($code);
			if(!is_null($processVariableToDelete)){
				$returnValue = $processVariableToDelete->delete(true);
			}
			
		}
		
        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003089 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource variable
     * @return boolean
     */
    public function isProcessVariable( core_kernel_classes_Resource $variable)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:0000000000003096 begin
		$returnValue = $variable->hasType(new core_kernel_classes_Class(CLASS_PROCESSVARIABLES));
        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:0000000000003096 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_VariableService */

?>