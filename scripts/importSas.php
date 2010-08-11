<?php
/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 */
require_once dirname(__FILE__).'/../../generis/common/inc.extension.php';	
require_once dirname(__FILE__).'/../includes/common.php';
require_once dirname(__FILE__).'/SaSManager.php';

/**
 * 
 * @author Bertrand Chevrier <bertrand.chevrier@tudor.lu>
 *
 */
class SaSImporter extends SasManager{

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
	private $processInstanceClass = null;
	
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
	 * @var core_kernel_classes_Property
	 */
	private $rdfTypeProp = null;
	
	/**
	 * @var core_kernel_classes_Class
	 */
	private $rdfLiteralClass = null;
	
	/**
	 * Constructor: init api connection and the ref API resources
	 */
	public function __construct(){
		
		parent::__construct();
		
		//connec to the api
		core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS, DATABASE_NAME);

		//initialize ref to API classes and properties
		
		$this->rdfTypeProp					= new core_kernel_classes_Property(RDF_TYPE);
		$this->rdfLiteralClass				= new core_kernel_classes_Class(RDFS_LITERAL);
		$this->processInstanceClass			= new core_kernel_classes_Class(CLASS_PROCESSINSTANCE);
		
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
							var_dump($params, $value);
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
				//set the new instance of process variable as a property of the class process instance:
				if($processVar->setPropertyValue($this->rdfTypeProp, RDF_PROPERTY)){
					$newProcessInstanceProperty = new core_kernel_classes_Property($processVar->uriResource);
					$newProcessInstanceProperty->setDomain(new core_kernel_classes_Class(CLASS_TOKEN));
					$newProcessInstanceProperty->setRange($this->rdfLiteralClass);
				}
				
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
	
}

set_time_limit(900);
ini_set('max_execution_time', 900);

/*
 * Run the importer by calling me
 */
$importer = new SasImporter();
$importer->import();

?>