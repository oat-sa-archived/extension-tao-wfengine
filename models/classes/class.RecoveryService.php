<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.RecoveryService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 04.11.2010, 14:57:56 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
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
// section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F2-includes begin
// section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F2-includes end

/* user defined constants */
// section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F2-constants begin
// section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F2-constants end

/**
 * Short description of class wfEngine_models_classes_RecoveryService
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_RecoveryService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute contextRecoveryProperty
     *
     * @access protected
     * @var Property
     */
    protected $contextRecoveryProperty = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:0000000000002806 begin
        
    	$this->contextRecoveryProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY);
    	
        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:0000000000002806 end
    }

    /**
     * Short description of method saveContext
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @param  array context
     * @return boolean
     */
    public function saveContext( core_kernel_classes_Resource $activityExecution, $context)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F4 begin
        
        if(!is_null($activityExecution) && is_array($context)){
        	$returnValue = $activityExecution->editPropertyValues($this->contextRecoveryProperty, serialize($context));
        }
        
        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F4 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getContext
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getContext( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F8 begin
        
         if(!is_null($activityExecution)){
         	$contextData = (string)$activityExecution->getOnePropertyValue($this->contextRecoveryProperty);
         	if(!empty($contextData)){
         		$returnValue = unserialize($contextData);
         	}
         }
        
        
        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027F8 end

        return (array) $returnValue;
    }

    /**
     * Short description of method removeContext
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @return boolean
     */
    public function removeContext( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027FC begin
        
    	if(!is_null($activityExecution)){
         	$returnValue = $activityExecution->removePropertyValues($this->contextRecoveryProperty);
         }
        
        // section 127-0-1-1-1a24352c:12c1717dc9c:-8000:00000000000027FC end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_RecoveryService */

?>