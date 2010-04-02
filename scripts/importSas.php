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
class SasImporter{

	/**
	 * @var array
	 */
	private $services = array();
	
	/**
	 * @var array
	 */
	private $processVars = array();
	
	/**
	 * @var core_kernel_classes_Class
	 */
	private $serviceDefClass = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $serviceUrlProp = null;
	
	/**
	 * @var core_kernel_classes_Class
	 */
	private $processVarClass = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $processVarCodeProp = null;
	
	/**
	 * Constructor: init api connection and the ref API resources
	 */
	public function __construct(){
		core_control_FrontController::connect('generis', md5('g3n3r1s'), DATABASE_NAME);

		$this->serviceDefClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
		$this->serviceUrlProp = new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_URL);
		$this->processVarClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$this->processVarCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
	}
	
	/**
	 * Main method: run the import
	 */
	public function import(){
		
		echo "\n";
		foreach($this->getSasFiles() as $sasFile){
			$this->parseSasFile($sasFile);
		}
		echo "\n";
		echo "\n";
		
		$serviceNum = count($this->services);
		$serviceInserted = 0;
		foreach($this->services as $service){
			if($this->addService($service['name'], $service['url'], $service['description'])){
				$serviceInserted++;
				echo "\r$serviceInserted / $serviceNum  services definition inserted";
			}
			
		}
		
		echo "\n";
		echo "\n";
		
		$processVarNum = count($this->processVars);
		$processVarInserted = 0;
		foreach($this->processVars as $processVar){
			if($this->addProcessVariable($processVar)){
				$processVarInserted++;
				echo "\r$processVarInserted / $processVarNum  process variable inserted";
			}
		}
		
		
		
		echo "\n";
	}
	
	/**
	 * get the sas file definition in every extensions
	 * @return array
	 */
	private function getSasFiles(){
		
		$sasFiles = array();
		
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
	private function parseSasFile($file){
		
		$xml = simplexml_load_file($file);
		
		if($xml instanceof SimpleXMLElement){
			foreach($xml->service as $service){
				
				$loc = $service->location;
				$url = (string)$loc["url"];
				if(count($loc->param) > 0 && !preg_match("/(\?|\&)$/", $url)){
					$url .= '?';
				}
				
				foreach($loc->param as $param){
					if(isset($param['key'])){
						$url .= ((string)$param['key']) . '=';
						if(isset($param['value'])) {
							$url .= (string)$param['value'];		
							if(!in_array((string)$param['value'], $this->processVars)){
								$this->processVars[] = (string)$param['value'];
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
					'url'			=>	$url
				);
			}
		}
	}
	
	/**
	 * Add a service definition in the model
	 * @param string $name
	 * @param string $url
	 * @param string $description
	 * @return boolean
	 */
	private function addService($name, $url,  $description =''){
		if(!$this->serviceExists($url)){
			$service = $this->serviceDefClass->createInstance($name, $description);
			if(!is_null($service)){
				return $service->setPropertyValue($this->serviceUrlProp, $url);
			}
		}
		return false;
	}
	
	/**
	 * Chekc if the service owning the url has already been inserted
	 * @param string $url
	 * @return boolean
	 */
	private function serviceExists($url){
		foreach($this->serviceDefClass->getInstances(false) as $service){
			try{
				if($url == $service->getUniquePropertyValue($this->serviceUrlProp)){
					return true;
				}
			}	
			catch(common_Exception $ce){}		
		}
		return false;
	}
	
	/**
	 * Add a process variable in the model
	 * @param string $code
	 * @return boolean
	 */
	private function addProcessVariable($code){
		$code  = preg_replace("/^\^/", '', $code);
		
		if(!$this->processVarExists($code)){
			$processVar = $this->processVarClass->createInstance(self::unCamelize($code));
			if(!is_null($processVar)){
				return $processVar->setPropertyValue($this->processVarCodeProp, $code);
			}
		}
		return false;
	}
	
	/**
	 * Chekc if the process var owning the code has already been inserted
	 * @param string $code
	 * @return boolean
	 */
	private function processVarExists($code){
		
		foreach($this->processVarClass->getInstances(false) as $processVar){
			try{
				if($code == $processVar->getUniquePropertyValue($this->processVarCodeProp)){
					return true;
				}
			}	
			catch(common_Exception $ce){}		
		}
		return false;
	}
	
	
	/**
	 * Utility method to (unCamelize -> un camelize) a string
	 * @param string $input
	 * @return string
	 */
	private static function unCamelize($input){
		$matches = array();
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$output = $matches[0];
		foreach ($output as &$match) {
			$match = ($match == strtoupper($match)) ? strtolower($match) : ucfirst($match);
		}
		return implode(' ', $output);
	}
}
$importer = new SasImporter();
$importer->import();
?>