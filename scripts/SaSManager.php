<?php
/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 */
require_once dirname(__FILE__).'/../../generis/common/inc.extension.php';	
require_once dirname(__FILE__).'/../includes/common.php';

/**
 * 
 * @author Bertrand Chevrier <bertrand.chevrier@tudor.lu>
 *
 */
class SaSManager{
	
	/**
	 * @var boolean
	 */
	protected $outputModeWeb = true;
	
	/**
	 * @var array
	 */
	protected $services = array();
	

	
	/**
	 * Constructor
	 */
	public function __construct(){
		
		if(PHP_SAPI == 'cli'){
			$this->outputModeWeb = false;
		}
	}
	
	/**
	 * get the sas file definition in every extensions
	 * @return array
	 */
	protected function getSasFiles(){
		
		$sasFiles = array();
		//$sasFiles['taoQual'] = ROOT_PATH . '/taoQual/actions/sas.xml';
		$extensionsManager = common_ext_ExtensionsManager::singleton();
		foreach($extensionsManager->getInstalledExtensions() as $extension){
			$filePath = ROOT_PATH . '/'. $extension->id . '/actions/sas.xml';
			if(file_exists($filePath)){
				$sasFiles[$extension->id] = $filePath;
			}
		}
		
		return $sasFiles;
	}
	
	/**
	 * Parse the sas xml file and populate the services and processVar attributes
	 * @param string $file path
	 */
	protected function parseSasFile($file){
		
		$xml = simplexml_load_file($file);
		
		if($xml instanceof SimpleXMLElement){
			foreach($xml->service as $service){
				
				$loc = $service->location;
				$url = (string)$loc["url"];
				if(count($loc->param) > 0 && !preg_match("/(\?|\&)$/", $url)){
					$url .= '?';
				}
				
				$formalParamsIn = array();
				foreach($loc->param as $param){
					if(isset($param['key'])){
						
						$url .= ((string)$param['key']) . '=';
						if(isset($param['value'])) {
							$url .= (string)$param['value'];		

							//set the processVars
							if(!in_array((string)$param['value'], $this->processVars)){
								$this->processVars[] = (string)$param['value'];
							}	

							$formalParamsIn[(string)$param['key']] = (string)$param['value']; 
							
							//set the formatParams
							$key = (string)$param['key'].(string)$param['value'];
							if(!array_key_exists($key, $this->formalParams)){
								$this->formalParams[$key] = array(
									'name'			=> (string)$param['key'],
									'processVar'	=> ( preg_match("/^\^/", (string)$param['value'])) ? (string)$param['value'] : false,
									'constant'		=> (!preg_match("/^\^/", (string)$param['value'])) ? (string)$param['value'] : false 
								);
							}
						}
						else{
							$url .= "^".((string)$param['key']);
						}
						$url .= "&";
						
					}
				}
				if(isset($service->return)){
					foreach($service->return->param as $param){
						if(isset($param['key'])) {
							$code = "^".(string)$param['key'];
							if(!in_array($code, $this->processVars)){
								$this->processVars[] = $code;
							}		
						}
					}
				}
				$this->services[] = array(
					'name' 			=> (string)$service->name,
					'description' 	=> (string)$service->description,
					'url'			=>	$url,
					'params'		=> $formalParamsIn
				);
			}
		}
	}
	
	/**
	 * Utility method to (unCamelize -> un camelize) a string
	 * @param string $input
	 * @return string
	 */
	protected static function unCamelize($input){
		$matches = array();
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$output = $matches[0];
		foreach ($output as &$match) {
			$match = ($match == strtoupper($match)) ? strtolower($match) : ucfirst($match);
		}
		return implode(' ', $output);
	}
	
	
	/**
	 * @param string $message
	 */
	protected function log($message = ''){
		if($this->outputModeWeb){
			echo "{$message}</br>";
		}
		else{
			echo "{$message}\n";
		}
	}
}

?>