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
 * Short description of class wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_TranslationProcessMonitoringGrid
    extends wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid
{

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
		
		$this->grid->addColumn('unit', __('Unit'));
		$this->grid->addColumn('country', __('Country'));
		$this->grid->addColumn('language', __('Language'));
		
		$returnValue = parent::initColumns();
		
		$returnValue = $this->grid->setColumnsAdapter(
			array('unit', 'country', 'language'),
			new wfAuthoring_helpers_Monitoring_TranslationMetaAdapter()
		);	
		
        return (bool) $returnValue;
    }
	
	/**
     * Can be easily extended to adapt the current activity executions column
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    protected function initCurrentActivityColumn()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003364 begin
		$this->grid->addColumn(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS, __('Current Activities'));
		$returnValue = $this->grid->setColumnsAdapter(
			PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS,
			new wfAuthoring_helpers_Monitoring_CurrentActivitiesAdapter(
				array('excludedProperties' => $this->excludedProperties),
				'wfAuthoring_helpers_Monitoring_TranslationActivityMonitoringGrid'
			)
		);	
        // section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003364 end

        return (bool) $returnValue;
    }
}
?>
