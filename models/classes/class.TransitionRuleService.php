<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.TransitionRuleService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.09.2011, 19:16:11 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfEngine_models_classes_RuleService
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('wfEngine/models/classes/class.RuleService.php');

/* user defined includes */
// section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED6-includes begin
// section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED6-includes end

/* user defined constants */
// section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED6-constants begin
// section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED6-constants end

/**
 * Short description of class wfEngine_models_classes_TransitionRuleService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_TransitionRuleService
    extends wfEngine_models_classes_RuleService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getElseActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource rule
     * @return core_kernel_classes_Resource
     */
    public function getElseActivity( core_kernel_classes_Resource $rule)
    {
        $returnValue = null;

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED8 begin
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED8 end

        return $returnValue;
    }

    /**
     * Short description of method getThenActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource rule
     * @return core_kernel_classes_Resource
     */
    public function getThenActivity( core_kernel_classes_Resource $rule)
    {
        $returnValue = null;

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EDB begin
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EDB end

        return $returnValue;
    }

    /**
     * Short description of method isTransitionRule
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource rule
     * @return boolean
     */
    public function isTransitionRule( core_kernel_classes_Resource $rule)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EDE begin
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EDE end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_TransitionRuleService */

?>