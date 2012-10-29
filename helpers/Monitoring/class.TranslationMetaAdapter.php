<?php

error_reporting(E_ALL);
if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}
require_once('tao/helpers/grid/class.GridContainer.php');
require_once('wfEngine/test/TranslationProcess/TranslationProcessHelper.php');

/**
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_TranslationMetaAdapter
    extends tao_helpers_grid_Cell_Adapter
{
	public function getValue($rowId, $columnId, $data = null)
    {
		$returnValue = null;
		 
		if(isset($this->data[$rowId])){
			
			//return values:
			if(isset($this->data[$rowId][$columnId])){
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		}else{
		
			if(common_Utils::isUri($rowId)){
				
				$processInstance = new core_kernel_classes_Resource($rowId);
				
				//TODO: property uris need to be set in the constant files:
				$unit = $processInstance->getOnePropertyValue(TranslationProcessHelper::getProperty('unitUri'));
				$countryCode = $processInstance->getOnePropertyValue(TranslationProcessHelper::getProperty('CountryCOde'));
				$langCode = $processInstance->getOnePropertyValue(TranslationProcessHelper::getProperty('LanguageCode'));
				
				$this->data[$rowId] = array(
					'unit' => is_null($unit)?'n/a':$unit->getLabel(),
					'country' => ($countryCode instanceof core_kernel_classes_Literal)?$countryCode->literal:'n/a',
					'language' => ($langCode instanceof core_kernel_classes_Literal)?$langCode->literal:'n/a'
				);
				
				if(isset($this->data[$rowId][$columnId])){
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
			
		}
		
		return $returnValue;
	}
}
