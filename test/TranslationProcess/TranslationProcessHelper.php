<?php
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class TranslationProcessHelper{
	
	const PREFIX = 'Translation_';

	public static function getPropertyName($type, $countryCode = '', $langCode = ''){
		
		$returnValue = '';
		
		if(!empty($countryCode) && !empty($langCode)){
			$returnValue = self::PREFIX.'Property_'.strtoupper($type).'_'.strtoupper($countryCode).'_'.strtolower($langCode);
		}else{
			$returnValue = self::PREFIX.'Property_'.strtoupper($type);
		}
		
		return $returnValue;
		
	}
	
	public static function getFileName($unitLabel, $countryCode, $langCode, $type, core_kernel_classes_Resource $user = null){
		
		$fileName = $unitLabel.'_'.strtoupper($countryCode).'_'.strtolower($langCode);
		if(!is_null($user)){
			$fileName .= '_'.$user->getLabel();
		}
		$fileName .= '.'.strtolower($type);
		
		return $fileName;
	}
	
	public static function getProperty($type, $countryCode = '', $langCode = ''){
		
		$returnValue = new core_kernel_classes_Property(LOCAL_NAMESPACE.'#'.self::getPropertyName($type, $countryCode, $langCode));
		
		return $returnValue;
	}
	
}
	
?>
