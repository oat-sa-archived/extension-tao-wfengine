<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/actions/form/class.InstanceServiceDefinition.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 21.11.2011, 15:54:32 with ArgoUML PHP module 
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
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:0000000000003419-includes begin
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:0000000000003419-includes end

/* user defined constants */
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:0000000000003419-constants begin
// section 127-0-1-1--1c42fdef:133c68cec06:-8000:0000000000003419-constants end

/**
 * Short description of class wfEngine_actions_form_InstanceServiceDefinition
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form
 */
class wfEngine_actions_form_InstanceServiceDefinition
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

        // section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341A begin
		if(!is_null($this->topClazz)){
        	$returnValue = $this->topClazz;
        }
        else{
        	$returnValue = new core_kernel_classes_Class(CLASS_SERVICESDEFINITION);
        }
        // section 127-0-1-1--1c42fdef:133c68cec06:-8000:000000000000341A end

        return $returnValue;
    }

} /* end of class wfEngine_actions_form_InstanceServiceDefinition */

?>