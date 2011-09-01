<?php

error_reporting(E_ALL);

/**
 * Connector Services
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
 * Connector Services
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
     * Check if the resource is a connector
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

    /**
     * retrieve connector nexts activities
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource connector
     * @return array
     */
    public function getNextActivities( core_kernel_classes_Resource $connector)
    {
        $returnValue = array();

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EC5 begin
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002EC5 end

        return (array) $returnValue;
    }

    /**
     * retrieve connector previous activities
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource connector
     * @return array
     */
    public function getPreviousActivities( core_kernel_classes_Resource $connector)
    {
        $returnValue = array();

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECB begin
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECB end

        return (array) $returnValue;
    }

    /**
     * retrive type of Connector Conditionnal, Sequestionnal Parallele...
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    public function getType( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECF begin
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ECF end

        return $returnValue;
    }

    /**
     * Short description of method getTransitionnalRules
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    public function getTransitionnalRules( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED3 begin
        // section 127-0-1-1-66b8afb4:1322473370c:-8000:0000000000002ED3 end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ConnectorService */

?>