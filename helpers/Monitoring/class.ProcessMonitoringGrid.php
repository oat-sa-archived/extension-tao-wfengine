<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.ProcessMonitoringGrid.php
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
    extends tao_helpers_grid_Grid
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
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array processExecutions
     * @param  array excludedProperties
     * @return mixed
     */
    public function __construct($processExecutions, $excludedProperties = array())
    {
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032DD begin
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032DD end
    }

} /* end of class wfEngine_helpers_Monitoring_ProcessMonitoringGrid */

?>