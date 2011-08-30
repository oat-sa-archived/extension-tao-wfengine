<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.InteractiveServiceService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 30.08.2011, 18:24:16 with ArgoUML PHP module 
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
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-includes begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-includes end

/* user defined constants */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-constants begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-constants end

/**
 * Short description of class wfEngine_models_classes_InteractiveServiceService
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_InteractiveServiceService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E97 begin
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E97 end
    }

    /**
     * Short description of method getCallUrl
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource interactiveService
     * @param  array variable
     * @return string
     */
    public function getCallUrl( core_kernel_classes_Resource $interactiveService, $variable)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E99 begin
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E99 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getStyle
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return string
     */
    public function getStyle()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002E9D begin
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002E9D end

        return (string) $returnValue;
    }

    /**
     * Short description of method isInteractiveService
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource service
     * @return boolean
     */
    public function isInteractiveService( core_kernel_classes_Resource $service)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EC1 begin
        if(!is_null($service)){
			$returnValue = $service->hasType( new core_kernel_classes_Class(CLASS_CALLOFSERVICES));
		}
        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EC1 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_InteractiveServiceService */

?>