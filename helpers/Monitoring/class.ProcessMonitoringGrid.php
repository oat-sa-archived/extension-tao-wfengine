<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.ProcessMonitoringGrid.php
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
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-includes begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-includes end

/* user defined constants */
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-constants begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-constants end

/**
 * Short description of class wfEngine_helpers_Monitoring_ProcessMonitoringGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_ProcessMonitoringGrid
    extends tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processExecutions
     *
     * @access public
     * @var array
     */
    public $processExecutions = array();

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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032DD begin
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032DD end

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

        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335C begin
		
		$excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
		$columnNames = (is_array($this->options) && isset($this->options['columnNames']))?$this->options['columnNames']:array();
		
		$processProperties = array(
			RDFS_LABEL => __('Label'),
			PROPERTY_PROCESSINSTANCES_STATUS => __('Status'),
			PROPERTY_PROCESSINSTANCES_EXECUTIONOF => __('Process Definition'),
			PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS => __('Current Activities'),
			PROPERTY_PROCESSINSTANCES_TIME_STARTED => __('Started Time')
		);
		
		$propertyUris = array();
		
		foreach($processProperties as $processPropertyUri => $label){
			if(!isset($excludedProperties[$processPropertyUri])){
				$this->grid->addColumn($processPropertyUri, $label);
				$propertyUris[] = $processPropertyUri;
			}
			
		}
		
		$returnValue = $this->grid->setColumnsAdapter(
			$propertyUris,
			new wfEngine_helpers_Monitoring_ProcessPropertiesAdapter(array('excludedProperties' => $excludedProperties))
		);
		
        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335C end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_helpers_Monitoring_ProcessMonitoringGrid */

?>