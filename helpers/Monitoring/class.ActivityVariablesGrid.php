<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.ActivityVariablesGrid.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.11.2011, 17:07:59 with ArgoUML PHP module 
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
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-includes begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-includes end

/* user defined constants */
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-constants begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-constants end

/**
 * Short description of class wfEngine_helpers_Monitoring_ActivityVariablesGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_ActivityVariablesGrid
    extends tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

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

        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003406 begin
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003406 end

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

        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:000000000000340C begin
		
		$this->grid->addColumn('code', __('Code'));
		$this->grid->addColumn('value', __('Value'));
		
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:000000000000340C end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_helpers_Monitoring_ActivityVariablesGrid */

?>