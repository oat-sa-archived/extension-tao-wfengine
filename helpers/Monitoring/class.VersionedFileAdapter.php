<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.VersionedFileAdapter.php
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
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003302-constants end

/**
 * Short description of class wfEngine_helpers_Monitoring_VersionedFileAdapter
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_VersionedFileAdapter
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

        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003337 begin
		
		if (isset($this->data[$rowId])) {

			if (isset($this->data[$rowId][$columnId])) {
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		} else {
			
			if(common_Utils::isUri($rowId)){
				
				$this->data[$rowId] = array();
				
				$activityExecution = new core_kernel_classes_Resource($rowId);

				$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
				$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
				$unit = $processVariableService->get('unitUri', $activityExecution);
				$countryCode = (string) $processVariableService->get('countryCode', $activityExecution);
				$languageCode = (string) $processVariableService->get('languageCode', $activityExecution);

				if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
					
					$activity = $activityExecutionService->getExecutionOf($activityExecution);

					//check if it is the translation activity or not:
					$xliff = null;
					$vff = null;
					if($activity->getLabel() == 'Translate'){
						$xliff = $unit->getOnePropertyValue($this->getTranslationFileProperty('xliff_working', $countryCode, $languageCode));
						$vff = $unit->getOnePropertyValue($this->getTranslationFileProperty('vff_working', $countryCode, $languageCode));
					}else{
						$xliff = $unit->getOnePropertyValue($this->getTranslationFileProperty('xliff', $countryCode, $languageCode));
						$vff = $unit->getOnePropertyValue($this->getTranslationFileProperty('vff', $countryCode, $languageCode));
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
	
	private function getTranslationFileProperty($type, $countryCode, $langCode){
		
		$returnValue = null;
		
		$uri = LOCAL_NAMESPACE.'#Property_'.strtoupper($type).'_'.strtoupper($countryCode).'_'.strtolower($langCode);
		$property = new core_kernel_classes_Property($uri);
		if($property->exists()){
			$returnValue = $property;
		}
		
		return $returnValue;
	}

} /* end of class wfEngine_helpers_Monitoring_VersionedFileAdapter */

?>