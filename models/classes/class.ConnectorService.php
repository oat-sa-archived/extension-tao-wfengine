<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.ConnectorService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.08.2011, 17:36:50 with ArgoUML PHP module 
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
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-includes begin
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-includes end

/* user defined constants */
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-constants begin
// section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBB-constants end

/**
 * Short description of class wfEngine_models_classes_ConnectorService
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ConnectorService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method isConnector
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource connector
     * @return boolean
     */
    public function isConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBD begin
    	if(!is_null($connector)){
			$returnValue = $connector->hasType( new core_kernel_classes_Class(CLASS_CONNECTORS));
		}
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002EBD end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ConnectorService */

?>