<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.CurrentActivitiesAdapter.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.11.2011, 11:15:19 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_Cell_Adapter
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.Adapter.php');

/* user defined includes */
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-includes begin
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-includes end

/* user defined constants */
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-constants begin
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_CurrentActivitiesAdapter
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        // section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003357 begin
		
		if(isset($this->data[$rowId]) && is_a($this->data[$rowId], 'wfEngine_helpers_Monitoring_ActivityMonitoringGrid')){
			
			$returnValue = $this->data[$rowId];
			
		}else{

			$activityMonitoringGridClass = 'wfEngine_helpers_Monitoring_ActivityMonitoringGrid';
			if(is_array($this->options) && isset($this->options['MonitoringGridClass']) && class_exists($this->options['MonitoringGridClass'])){
				$activityMonitoringGridClass = $this->options['MonitoringGridClass'];
			}

			$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			$processInstance = new core_kernel_classes_Resource($rowId);
			$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$activityMonitoringClass = new $activityMonitoringGridClass(array_keys($currentActivityExecutions));
			if(is_a($activityMonitoringClass, 'wfEngine_helpers_Monitoring_ActivityMonitoringGrid')){
				$returnValue = $activityMonitoringClass;
			}else{
				$returnValue = new wfEngine_helpers_Monitoring_ActivityMonitoringGrid(array_keys($currentActivityExecutions), array('excludedProperties' => $this->excludedProperties));
			}

			$this->data[$rowId] = $returnValue;
			
		}
        // section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003357 end

        return $returnValue;
    }

} /* end of class wfEngine_helpers_Monitoring_CurrentActivitiesAdapter */

?>