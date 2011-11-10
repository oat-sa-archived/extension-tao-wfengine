<?php

error_reporting(E_ALL);

/**
 * Gives the activity monitoring Grid
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_Cell_SubgridAdapter
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.SubgridAdapter.php');

/* user defined includes */
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-includes begin
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-includes end

/* user defined constants */
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-constants begin
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-constants end

/**
 * Gives the activity monitoring Grid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_CurrentActivitiesAdapter
    extends tao_helpers_grid_Cell_SubgridAdapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getSubgridRows
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string rowId
     * @return array
     */
    public function getSubgridRows($rowId)
    {
        $returnValue = array();

        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339D begin
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$processInstance = new core_kernel_classes_Resource($rowId);
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$returnValue = array_keys($currentActivityExecutions);
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339D end

        return (array) $returnValue;
    }

    /**
     * Short description of method initSubgridOptions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function initSubgridOptions()
    {
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339F begin
		$this->subgridOptions = array('excludedProperties' => $this->excludedProperties);
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339F end
    }

    /**
     * Short description of method initSubgridClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  subgridClass
     * @return mixed
     */
    public function initSubgridClass($subgridClass = '')
    {
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:00000000000033A1 begin
		$this->subgridClass = 'wfEngine_helpers_Monitoring_ActivityMonitoringGrid';
		if(!empty($subgridClass)){
			if(class_exists($subgridClass)){
				$this->subgridClass = $subgridClass;
			}else{
				throw new common_Exception('The given subgrid class in argument is not valid : '.$subgridClass);
			}
		}
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:00000000000033A1 end
    }

} /* end of class wfEngine_helpers_Monitoring_CurrentActivitiesAdapter */

?>