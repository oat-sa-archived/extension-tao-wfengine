<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/actions/form/class.InstanceProcessVariable.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.11.2011, 10:52:36 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-includes begin
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-includes end

/* user defined constants */
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-constants begin
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341C-constants end

/**
 * Short description of class wfEngine_actions_form_InstanceProcessVariable
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form
 */
class wfEngine_actions_form_InstanceProcessVariable
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getTopClazz
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getTopClazz()
    {
        $returnValue = null;

        // section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341D begin
		if(!is_null($this->topClazz)){
        	$returnValue = $this->topClazz;
        }
        else{
        	$returnValue = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
        }
        // section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341D end

        return $returnValue;
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:000000000000342F begin
		parent::initElements();
		$codeElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_PROCESSVARIABLES_CODE));
		$codeElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$codeElt->addValidator(new wfEngine_actions_form_validators_VariableCode(array('uri'=>$this->getInstance()->uriResource)));
        // section 127-0-1-1--193aa0be:133cfb90ad2:-8000:000000000000342F end
    }

} /* end of class wfEngine_actions_form_InstanceProcessVariable */

?>