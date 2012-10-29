<?php

error_reporting(E_ALL);

/**
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}
/**
 * Short description of class wfAuthoring_helpers_Monitoring_ExecutionHistoryGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_TranslationExecutionHistoryGrid
    extends wfAuthoring_helpers_Monitoring_TranslationActivityMonitoringGrid
{
    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  array options
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $processExecution, $options = array())
    {
        // section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A5 begin
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$this->activityExecutions = $processExecutionService->getExecutionHistory($processExecution);
		parent::__construct($this->activityExecutions, $options);
        // section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A5 end
    }

}

?>