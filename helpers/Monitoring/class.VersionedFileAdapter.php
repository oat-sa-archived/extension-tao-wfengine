<?php

error_reporting(E_ALL);

/**
 * TAO - wfAuthoring/helpers/Monitoring/class.VersionedFileAdapter.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.10.2012, 09:08:10 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_Cell_Adapter
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.Adapter.php');

/* user defined includes */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_VersionedFileAdapter
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003337 begin
		
		if (isset($this->data[$rowId])) {

			if (isset($this->data[$rowId][$columnId])) {
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		} else {
			
			if(common_Utils::isUri($rowId)){
				
				$this->data[$rowId] = array();
				
				$activityExecution = new core_kernel_classes_Resource($rowId);

				$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
				$processVariableService = wfEngine_models_classes_VariableService::singleton();
				$unit = $processVariableService->get('unitUri', $activityExecution);
				$countryCode = (string) $processVariableService->get('countryCode', $activityExecution);
				$languageCode = (string) $processVariableService->get('languageCode', $activityExecution);

				if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
					
					$activity = $activityExecutionService->getExecutionOf($activityExecution);

					//check if it is the translation activity or not:
					$xliff = null;
					$vff = null;
					if($activity->getLabel() == 'Translate'){
						$xliffWorkingProperty = $this->getTranslationFileProperty('xliff_working', $countryCode, $languageCode);
						if(!is_null($xliffWorkingProperty)){
							$xliff = $unit->getOnePropertyValue($xliffWorkingProperty);
						}
						$vffWorkingProperty = $this->getTranslationFileProperty('vff_working', $countryCode, $languageCode);
						if(!is_null($vffWorkingProperty)){
							$vff = $unit->getOnePropertyValue($vffWorkingProperty);
						}
					}else{
						$xliffProperty = $this->getTranslationFileProperty('xliff', $countryCode, $languageCode);
						if(!is_null($xliffProperty)){
							$xliff = $unit->getOnePropertyValue($xliffProperty);
						}
						$vffProperty = $this->getTranslationFileProperty('vff', $countryCode, $languageCode);
						if(!is_null($vffProperty)){
							$vff = $unit->getOnePropertyValue($vffProperty);
						}
					}
					
					if($xliff instanceof core_kernel_classes_Resource){
						$xliff = new core_kernel_versioning_File($xliff->uriResource);
						$this->data[$rowId]['xliff'] = $xliff->uriResource;
						$this->data[$rowId]['xliff_version'] = (string) $processVariableService->get('xliff', $activityExecution);
					}else{
						$this->data[$rowId]['xliff'] = 'n/a';
						$this->data[$rowId]['xliff_version'] = 'n/a';
					}
					
					if($vff instanceof core_kernel_classes_Resource){
						$vff = new core_kernel_versioning_File($vff->uriResource);
						$this->data[$rowId]['vff'] = $vff->uriResource;
						$this->data[$rowId]['vff_version'] = (string) $processVariableService->get('vff', $activityExecution);
					}
					else{
						$this->data[$rowId]['vff'] = 'n/a';
						$this->data[$rowId]['vff_version'] = 'n/a';
					}
				}else{
					$this->data[$rowId] = array(
						'xliff' => 'n/a',
						'xliff_version' => 'n/a',
						'vff' => 'n/a',
						'vff_version' => 'n/a'
						);
				}
				
				if (isset($this->data[$rowId][$columnId])) {
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
			
		}
        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003337 end

        return $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_VersionedFileAdapter */

?>