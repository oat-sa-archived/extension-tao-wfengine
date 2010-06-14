<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 08.06.2010, 17:04:50 with ArgoUML PHP module
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
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
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes end

/* user defined constants */
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants end

/**
 * Short description of class wfEngine_models_classes_VariableService
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_VariableService
extends tao_models_classes_Service
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	// --- OPERATIONS ---

	/**
	 * Short description of method save
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  array variable
	 * @return boolean
	 */
	public function save($variable)
	{
		$returnValue = (bool) false;

		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 begin
		if(isset($_SESSION["activityExecutionUri"])){
			$activityExecutionUri = urldecode($_SESSION["activityExecutionUri"]);
			$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			if(is_null($token)) {
					throw new Exception('Activity Token should never be null');
			}
			
			$newVar = unserialize($token->getOnePropertyValue($tokenVarProp));
			foreach($variable as $k) {
				$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE,$k);
				if(!$collection->isEmpty()){
						if($collection->count() == 1) {
							$property = new core_kernel_classes_Property($collection->get(0)->uriResource);

							$returnValue &= $token->editPropertyValues($property,$v);
							$newVar = array_merge($newVar, array($k)); 
						}
				}
				
			}
			$returnValue &= $token->editPropertyValues($tokenVarProp,serialize($newVar));
		}
		
		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 end

		return (bool) $returnValue;
	}

	/**
	 * Short description of method remove
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  mixed params
	 * @return boolean
	 */
	public function remove($params)
	{
		$returnValue = (bool) false;

		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B begin
		if(isset($_SESSION["activityExecutionUri"])){
			$activityExecutionUri = urldecode($_SESSION["activityExecutionUri"]);
			$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
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
				foreach($params as $param) {
					if(in_array($param,$oldVar)){
						$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $param);
						if(!$collection->isEmpty()){
							if($collection->count() == 1) {
								$property = new core_kernel_classes_Property($collection->get(0)->uriResource);
								// $apiModel->removeStatement($subjectCollection->get(0)->uriResource, $property->uriResource, $object->uriResource, '');
								
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
	 * Short description of method get
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  string key
	 * @return mixed
	 */
	public function get($key)
	{
		$returnValue = null;

		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E begin
		if(isset($_SESSION["activityExecutionUri"])){
			$activityExecutionUri = urldecode($_SESSION["activityExecutionUri"]);
			$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			if(is_null($token)) {
				throw new Exception('Activity Token should never be null');
			}
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			$vars = unserialize($token->getOnePropertyValue($tokenVarProp));
			if(in_array($key,$vars)){
				$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE,$key);
				if(!$collection->isEmpty()){
					if($collection->count() == 1) {
						$property = new core_kernel_classes_Property($collection->get(0)->uriResource);
						$returnValue = $token->getOnePropertyValue($property);
					}
				}
			}
		}
		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E end

		return $returnValue;
	}

	/**
	 * Short description of method getAll
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return array
	 */
	public function getAll()
	{
		$returnValue = array();

		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 begin
		if(isset($_SESSION["activityExecutionUri"])){
			$activityExecutionUri = urldecode($_SESSION["activityExecutionUri"]);
			$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$token = $tokenService->getCurrent($activityExecution);
			if(is_null($token)) {
				throw new Exception('Activity Token should never be null');
			}
			$tokenVarProp = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			$vars = unserialize($token->getOnePropertyValue($tokenVarProp));
			
			foreach($vars as $code){
				$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE,$code);
				if(!$collection->isEmpty()){
					if($collection->count() == 1) {
						$property = new core_kernel_classes_Property($collection->get(0)->uriResource);
						$returnValue[$code] = $token->getOnePropertyValue($property);
					}
				}
			}

		}
		
		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 end

		return (array) $returnValue;
	}

} /* end of class wfEngine_models_classes_VariableService */

?>