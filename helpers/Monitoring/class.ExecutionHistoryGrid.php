<?php

error_reporting(E_ALL);

/**
 * TAO - wfAuthoring/helpers/Monitoring/class.ExecutionHistoryGrid.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.10.2012, 09:48:14 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('wfAuthoring/helpers/Monitoring/class.ActivityMonitoringGrid.php');

/* user defined includes */
// section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A3-includes begin
// section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A3-includes end

/* user defined constants */
// section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A3-constants begin
// section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A3-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_ExecutionHistoryGrid
    extends wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource processExecution
     * @param  array options
     * @return boolean
     */
    public function __construct( core_kernel_classes_Resource $processExecution, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A5 begin
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$this->activityExecutions = $processExecutionService->getExecutionHistory($processExecution);
		parent::__construct($this->activityExecutions, $options);
        // section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A5 end

        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_ExecutionHistoryGrid */

?>