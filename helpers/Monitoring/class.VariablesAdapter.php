<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.VariablesAdapter.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.11.2011, 16:42:32 with ArgoUML PHP module 
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
 * include tao_helpers_grid_Cell_SubgridAdapter
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.SubgridAdapter.php');

/* user defined includes */
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-includes begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-includes end

/* user defined constants */
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-constants begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-constants end

/**
 * Short description of class wfEngine_helpers_Monitoring_VariablesAdapter
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_VariablesAdapter
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

        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F8 begin
		
		$activityExecution = new core_kernel_classes_Resource($rowId);
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$variables = $activityExecutionService->getVariables($activityExecution);
		foreach($variables as $variableData){
			$returnValue[$variableData['propertyUri']] = $variableData;
		}
		
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F8 end

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
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FB begin
		$this->subgridOptions = array('excludedProperties' => $this->excludedProperties);
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FB end
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
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FE begin
		$this->subgridClass = 'wfEngine_helpers_Monitoring_ActivityVariablesGrid';
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FE end
    }

} /* end of class wfEngine_helpers_Monitoring_VariablesAdapter */

?>