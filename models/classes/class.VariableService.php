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
		$property = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
		$formerVariable = $this->getAll();
		$newVariable = !empty($formerVariable) ? array_merge($formerVariable,$variable) : $variable;
		$property = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
		$returnValue = $token->editPropertyValues($property,serialize($newVariable));

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
		$variable = $this->getAll();
		if(!empty($variable)) {
			$property = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);	
			if(is_string($params)){
				$params = array($params);
			}	
			if(is_array($params)){
				$newVariable = array_diff($variable,$params);
				$returnValue = $token->editPropertyValues($property,serialize($newVariable));
				
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
		$variable = $this->getAll();
		if(!empty($variable)) {
			$returnValue = isset($variable[$key]) ? $variable[$key] : null;
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
				
			$property = new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
			$returnValue = unserialize($token->getOnePropertyValue($property));
		}
		// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 end

		return (array) $returnValue;
	}

} /* end of class wfEngine_models_classes_VariableService */

?>