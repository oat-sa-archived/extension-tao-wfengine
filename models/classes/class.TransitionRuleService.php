<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.TransitionRuleService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.09.2011, 14:35:59 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfEngine_models_classes_RuleService
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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

    /**
     * Short description of method getExpression
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource rule
     * @return core_kernel_classes_Expression
     */
    public function getExpression( core_kernel_classes_Resource $rule)
    {
        $returnValue = null;

        // section 127-0-1-1-74734511:1327233d503:-8000:0000000000003032 begin
        // section 127-0-1-1-74734511:1327233d503:-8000:0000000000003032 end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_TransitionRuleService */

?>