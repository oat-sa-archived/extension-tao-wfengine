<?php
class I18nUtil
{
	public static function getAvailableLanguages(){
		// skip reading the locales directory while using adapted versions	
		//batch
		//return array("EN_EN");

		$lgs = array();
		
		if (defined("PIAAC_ENABLED"))
			return $GLOBALS["countryUILanguages"][PIAAC_VERSION];
				
		$lgs = FileSystemUtil::getDirectoryContentNames(dirname(__FILE__) . '/../locales', array('.svn'));
    	// should restrict it using the variable $countryLanguages defined in constants.php
		return $lgs;
    }
    public static function getAvailableServiceContentLanguages(){
		//batch
		//return array("EN_EN");
		$lgs = array();

		if (defined("PIAAC_ENABLED")) {
			$lgs =  $GLOBALS["countryActivityContentLanguages"][PIAAC_VERSION];
		}
		
		return $lgs;
	}
	
    public static function getNextLanguage($currentLg) {
    	$lgs = self::getAvailableLanguages();
    	$pos = array_search($currentLg, $lgs);
    	if (!$pos && isset($lgs[$pos + 1]))
    		return $lgs[$pos + 1];
    	else if($pos != false && isset($lgs[$pos + 1]))
			return $lgs[$pos + 1];
    	else if ($pos == (count($lgs) - 1))
    		return $lgs[0];
    	else
    		throw new common_Exception('Unable to determine next language');
    }
    
	public static function getServiceContentNextLanguage($currentLg){
		$lgs = self::getAvailableServiceContentLanguages();
    	$pos = array_search($currentLg, $lgs);
    	if (!$pos && isset($lgs[$pos + 1]))
    		return $lgs[$pos + 1];
    	else if($pos != false && isset($lgs[$pos + 1]))
			return $lgs[$pos + 1];
    	else if ($pos == (count($lgs) - 1))
    		return $lgs[0];
    	else
    		throw new Exception('Unable to determine next language');
		return $lgs;
	}
	
	public static function extractLgLabelFromLgCode($lgCode)
	{
		return strtoupper(substr($lgCode, -2));
	}
}
?>