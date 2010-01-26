<?php
class FileSystemUtil
{
	/**
	 * @param $dirPath
	 * @param $filter
	 * @return string
	 */
	public static function getDirectoryContentNames($dirPath, array $filter)
    {
	    $fileNames = array();
		$dir = opendir($dirPath);
		
		while (false !== ($fname = readdir($dir)))
		{
			if ($fname != '.' && $fname != '..' && !in_array($fname, $filter))
				$fileNames[] = $fname;
		}
		
		return $fileNames;
    }	
    

}
?>