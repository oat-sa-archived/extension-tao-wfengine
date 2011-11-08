<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.ActivityMonitoringGrid.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.11.2011, 10:19:43 with ArgoUML PHP module 
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
 * include tao_helpers_grid_GridContainer
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/class.GridContainer.php');

/* user defined includes */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-constants end

/**
 * Short description of class wfEngine_helpers_Monitoring_ActivityMonitoringGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_ActivityMonitoringGrid
    extends tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute activityExecutions
     *
     * @access public
     * @var array
     */
    public $activityExecutions = array();

    // --- OPERATIONS ---

    /**
     * Short description of method initGrid
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function initGrid()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000335D begin
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000335D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method initColumns
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function initColumns()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335E begin
		$excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
		$columnNames = (is_array($this->options) && isset($this->options['columnNames']))?$this->options['columnNames']:array();
		
		$activityProperties = array(
			PROPERTY_ACTIVITY_EXECUTION_ACTIVITY => __('Label'),
			PROPERTY_ACTIVITY_EXECUTION_STATUS => __('Status'),
			PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED => __('Process Definition'),
			PROPERTY_ACTIVITY_EXECUTION_TIME_STARTED => __('Current Activities'),
			PROPERTY_ACTIVITY_EXECUTION_TIME_LASTACCESS => __('Started Time'),
			PROPERTY_ACTIVITY_EXECUTION_ACL_MODE => __('Access Control Mode'),
			PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER => __('Restricted to'),
			PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER => __('Current User')
		);
		
		$propertyUris = array();
		
		foreach($activityProperties as $activityPropertyUri => $label){
			if(!isset($excludedProperties[$activityPropertyUri])){
				$this->grid->addColumn($activityPropertyUri, $label);
				$propertyUris[] = $activityPropertyUri;
			}
			
		}
		
		$returnValue = $this->grid->setColumnsAdapter(
			$propertyUris,
			new wfEngine_helpers_Monitoring_ActivityPropertiesAdapter(array('excludedProperties' => $excludedProperties))
		);
        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335E end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_helpers_Monitoring_ActivityMonitoringGrid */

?>