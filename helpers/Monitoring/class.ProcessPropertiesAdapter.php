<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.ProcessPropertiesAdapter.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.11.2011, 11:06:27 with ArgoUML PHP module 
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
// section 127-0-1-1-6c609706:1337d294662:-8000:00000000000032FB-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:00000000000032FB-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:00000000000032FB-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:00000000000032FB-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_ProcessPropertiesAdapter
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

        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000333D begin
		
		if(isset($this->data[$rowId])){
			
			//return values:
			if(isset($this->data[$rowId][$columnId])){
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		}else{
		
			if(common_Utils::isUri($rowId)){
				
				$excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
				$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
				$processInstance = new core_kernel_classes_Resource($rowId);
				$this->data[$rowId] = array();
				
				if(!in_array(RDFS_LABEL, $excludedProperties)){
					$this->data[$rowId][RDFS_LABEL] = $processInstance->getLabel();
				}
				
				if(!in_array(PROPERTY_PROCESSINSTANCES_STATUS, $excludedProperties)){
					$status = $processExecutionService->getStatus($processInstance);
					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_STATUS] = is_null($status)?null:$status->uriResource;
				}
				
				if(!in_array(PROPERTY_PROCESSINSTANCES_EXECUTIONOF, $excludedProperties)){
					$executionOf = $processExecutionService->getExecutionOf($processInstance);
					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_EXECUTIONOF] = is_null($executionOf)?null:$executionOf->uriResource;
				}
				
				if(!in_array(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS, $excludedProperties)){
					$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS] = new wfEngine_helpers_Monitoring_ActivityMonitoringGrid(array_keys($currentActivityExecutions));
				}
				
				if(!in_array(PROPERTY_PROCESSINSTANCES_TIME_STARTED, $excludedProperties)){
					$startedTime = (string) $processExecutionService->getOnePropertyValue(PROPERTY_PROCESSINSTANCES_TIME_STARTED);
					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_TIME_STARTED] = $startedTime;
				}

				if(isset($this->data[$rowId][$columnId])){
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
			
		}
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000333D end

        return $returnValue;
    }

} /* end of class wfEngine_helpers_Monitoring_ProcessPropertiesAdapter */

?>