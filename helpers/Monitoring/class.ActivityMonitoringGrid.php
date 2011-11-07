<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.ActivityMonitoringGrid.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 07.11.2011, 15:34:21 with ArgoUML PHP module 
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
 * include tao_helpers_grid_Grid
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/class.Grid.php');

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
    extends tao_helpers_grid_Grid
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
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array activityExecutions
     * @param  array excludedProperties
     * @return mixed
     */
    public function __construct($activityExecutions, $excludedProperties = array())
    {
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000335D begin
		$this->activityExecutions = $activityExecutions;
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000335D end
    }

} /* end of class wfEngine_helpers_Monitoring_ActivityMonitoringGrid */

?>