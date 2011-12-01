<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/actions/form/validators/class.VariableCode.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.11.2011, 10:37:15 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form_validators
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The validators enable you to perform a validation callback on a form element.
 * It's provide a model of validation and must be overriden.
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/form/class.Validator.php');

/* user defined includes */
// section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003426-includes begin
// section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003426-includes end

/* user defined constants */
// section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003426-constants begin
// section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003426-constants end

/**
 * Short description of class wfEngine_actions_form_validators_VariableCode
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form_validators
 */
class wfEngine_actions_form_validators_VariableCode
    extends tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003429 begin
		
		parent::__construct($options);
		
		if(isset($this->options['uri'])){
    		$this->message = __("Code already used");
    	}
		
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:0000000000003429 end
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function evaluate()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:000000000000342D begin
		
		$returnValue = true;
		if(isset($this->options['uri'])){
			$variableService = wfEngine_models_classes_VariableService::singleton();
			$processVar = $variableService->getProcessVariable($this->getValue());
			if(!is_null($processVar)) {
				if ($this->options['uri'] != $processVar->uriResource) {
					$returnValue = false;
				}
			}
		}
		
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:000000000000342D end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_actions_form_validators_VariableCode */

?>