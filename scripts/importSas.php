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
	 * @var boolean
	 */
	private $outputModeWeb = true;
	
	/**
	 * @var array
	 */
	private $services = array();
	
	/**
	 * @var array
	 */
	private $processVars = array();
	
	/**
	 * @var array
	 */
	private $formalParams = array();
	
	/**
	 * @var core_kernel_classes_Class
	 */
	private $serviceDefClass = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $serviceUrlProp = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $serviceFormalParamInProp = null;
	
	/**
	 * @var core_kernel_classes_Class
	 */
	private $processVarClass = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $processVarCodeProp = null;
	
	/**
	 * @var core_kernel_classes_Class
	 */
	private $formalParamClass = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $formalParamNameProp = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $formalParamDefProcessVarProp = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	private $formalParamDefConstantProp = null;
	
	/**
	 * Constructor: init api connection and the ref API resources
	 */
	public function __construct(){
		
		if(PHP_SAPI == 'cli'){
			$this->outputModeWeb = false;
		}
		
		//connec to the api
		core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS, DATABASE_NAME);

		//initialize ref to API classes and properties
		
		$this->serviceDefClass 				= new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
		$this->serviceUrlProp 				= new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_URL);
		$this->serviceFormalParamInProp 	= new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN);
		
		$this->processVarClass 				= new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$this->processVarCodeProp 			= new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		
		$this->formalParamClass 			= new core_kernel_classes_Class(CLASS_FORMALPARAMETER);
		$this->formalParamNameProp			= new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME);
		$this->formalParamDefProcessVarProp	= new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE);
		$this->formalParamDefConstantProp	= new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE);
	}
	
	/**
	 * Main method: run the import
	 */
	public function import(){
		
		$this->log();
		foreach($this->getSasFiles() as $sasFile){
			log($sasFile." found");
			$this->parseSasFile($sasFile);
		}
		
		//insert process vars
		$processVarNum = count($this->processVars);
		$processVarInserted = 0;
		foreach($this->processVars as $processVar){
			if($this->addProcessVariable($processVar)){
				$processVarInserted++;
				if(!$this->outputModeWeb){
					echo "\r$processVarInserted / $processVarNum  process variable inserted";
				}
			}
		}
		if($this->outputModeWeb){
			$this->log("$processVarInserted / $processVarNum  process variable inserted");
		}
		else{
			echo "\n";
		}
		
		//insert formal params
		$formaParamNum = count($this->formalParams);
		$formaParamInserted = 0;
		foreach($this->formalParams as $formalParam){
			if($this->addFormalParameter($formalParam)){
				$formaParamInserted++;
				if(!$this->outputModeWeb){
					echo "\r$formaParamInserted / $formaParamNum  formal parameters inserted";
				}
			}
		}
		if($this->outputModeWeb){
			$this->log("$formaParamInserted / $formaParamNum  formal parameters inserted");
		}
		else{
			echo "\n";
		}
		
		//insert service definitions
		$serviceNum = count($this->services);
		$serviceInserted = 0;
		foreach($this->services as $service){
			if($this->addService($service['name'], $service['url'], $service['description'], $service['params'])){
				$serviceInserted++;
				if(!$this->outputModeWeb){
					echo "\r$serviceInserted / $serviceNum  services definition inserted";
				}
			}
		}
		if($this->outputModeWeb){
			$this->log("$serviceInserted / $serviceNum  services definition inserted");
		}
		else{
			echo "\n";
		}
		
		$this->log("import finished");
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
	 * Add a service definition in the model
	 * @param string $name
	 * @param string $url
	 * @param string $description
	 * @param array $params
	 * @return boolean
	 */
	private function addService($name, $url,  $description ='', $params = array()){
		if(!$this->serviceExists($url)){
			$service = $this->serviceDefClass->createInstance($name, trim($description));
			if(!is_null($service)){
				if($service->setPropertyValue($this->serviceUrlProp, $url)){
					foreach($params as $key => $value){
						$formalParam = $this->getFormalParameter($key, $value);
						if(!is_null($formalParam)){
							$service->setPropertyValue($this->serviceFormalParamInProp, $formalParam->uriResource);
						}
						else{
							echo "\nError\n";
							var_dump($params);
							exit;
						}
					}
					return true;
				}
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
	 * get a process var with the code in property
	 * @param string $code
	 * @return core_kernel_classes_Resource
	 */
	private function getProcessVar($code){
		
		foreach($this->processVarClass->getInstances(false) as $processVar){
			try{
				if($code == $processVar->getUniquePropertyValue($this->processVarCodeProp)){
					return $processVar;
				}
			}	
			catch(common_Exception $ce){}		
		}
		return null;
	}
	
	/**
	 * 
	 * @param array $formalParam
	 * @return boolean
	 */
	private function addFormalParameter($formalParam){
		if(is_array($formalParam)){
			
			if($formalParam['processVar']){
				$label = self::unCamelize(str_replace('^', '', $formalParam['processVar'])); 
			}
			else{
				$label = self::unCamelize($formalParam['name']);
			}
			
			
			$formalParamResource = $this->formalParamClass->createInstance($label);
			if(!is_null($formalParamResource)){
				$formalParamResource->setPropertyValue($this->formalParamNameProp, $formalParam['name']);
				if($formalParam['processVar']){
					$processVar = $this->getProcessVar(str_replace('^', '', $formalParam['processVar']));
					if(!is_null($processVar)){
						$formalParamResource->setPropertyValue($this->formalParamDefProcessVarProp, $processVar->uriResource);
					}
				}
				if($formalParam['constant']){
					$formalParamResource->setPropertyValue($this->formalParamDefConstantProp, $formalParam['constant']);
				}
				return true;
			}
		}
		return false;
	}
	
	/**
	 * get a formal parameter
	 * @param string $key
	 * @param string $value
	 * @return core_kernel_classes_Resource
	 */
	private function getFormalParameter($key, $value){
		
		foreach($this->formalParamClass->getInstances(false) as $formalParam){
			
			$name = $formalParam->getOnePropertyValue($this->formalParamNameProp);
			if(trim($key) == trim($name)){
				$foundProcessVar = $this->getProcessVar(str_replace('^', '', $value));
				if(!is_null($foundProcessVar)){
					try{
						$processVar = $formalParam->getUniquePropertyValue($this->formalParamDefProcessVarProp);
						if($foundProcessVar->uriResource == $processVar->uriResource){
							return $formalParam;
						}
					}	
					catch(common_Exception $ce){}	
				}
				
				try{
					if($value == $formalParam->getUniquePropertyValue($this->formalParamDefConstantProp)){
						return $formalParam;
					}
				}	
				catch(common_Exception $ce){}
				
			}
		}
		return null;
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
	
	
	/**
	 * @param string $message
	 */
	private function log($message = ''){
		if($this->outputModeWeb){
			echo "{$message}</br>";
		}
		else{
			echo "{$message}\n";
		}
	}
	
}

/*
 * Run the importer
 */
$importer = new SasImporter();
$importer->import();

?>