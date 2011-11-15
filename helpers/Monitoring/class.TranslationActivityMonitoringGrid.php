<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.TranslationProcessMonitoringGrid.php
 *
 * This file is part of TAO.
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}


/**
 * Short description of class wfEngine_helpers_Monitoring_ProcessMonitoringGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_TranslationActivityMonitoringGrid
    extends wfEngine_helpers_Monitoring_ActivityMonitoringGrid
{

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
		
		parent::initColumns();
		
		$this->grid->addColumn('xliff_version', __('XLIFF version'));
		$this->grid->addColumn('xliff', __('XLIFF'), array('widget'=>'Download'));
		
		$this->grid->addColumn('vff_version', __('VFF version'));
		$this->grid->addColumn('vff', __('VFF'));
		
		$returnValue = $this->grid->setColumnsAdapter(
			array(
				'xliff',
				'xliff_version',
				'vff',
				'vff_version'
			),
			new wfEngine_helpers_Monitoring_VersionedFileAdapter()
		);
		
		$this->grid->setColumnsAdapter(
			array(
				'xliff'
			),
			new tao_helpers_grid_Cell_ResourceVersionedFileAdapter()
		);
		
        return (bool) $returnValue;
    }

}
?>
