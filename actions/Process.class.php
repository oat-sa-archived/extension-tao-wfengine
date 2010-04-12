<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Groups Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Process extends TaoModule {
	
	
	protected $authoringService = null;
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Groups
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new wfEngine_models_classes_ProcessService();
		$this->authoringService = new wfEngine_models_classes_ProcessAuthoringService();
		$this->defaultData();
		
		Session::setAttribute('currentSection', 'process');
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected group from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $group
	 */
	protected function getCurrentInstance(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$process = $this->service->getProcess($uri, 'uri', $clazz);
		if(is_null($process)){
			throw new Exception("No process found for the uri {$uri}");
		}
		
		return $process;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getProcessClass();
	}
	
/*
 * controller actions
 */
	
	/**
	 * Edit a group class
	 * @see tao_helpers_form_GenerisFormFactory::classEditor
	 * @return void
	 */
	public function editProcessClass(){
		$clazz = $this->getCurrentClass();
		$myForm = $this->editClass($clazz, $this->service->getProcessClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit group class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', true);
	}
	
	/**
	 * Edit a group instance
	 * @see tao_helpers_form_GenerisFormFactory::instanceEditor
	 * @return void
	 */
	public function editProcess(){
		$clazz = $this->getCurrentClass();
		$process = $this->getCurrentInstance();
		
		$excludedProperties = array(
			PROPERTY_PROCESS_VARIABLE,
			PROPERTY_PROCESS_ACTIVITIES,
			'http://www.tao.lu/middleware/Interview.rdf#i122354397139712'
		);
		
		// $myForm = wfEngine_helpers_ProcessFormFactory::instanceEditor($clazz, $process, $excludedProperties);
		$myForm = wfEngine_helpers_ProcessFormFactory::instanceEditor($clazz, $process, 'processEditor', array("noSubmit"=>true,"noRevert"=>true) , $excludedProperties);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$process = $this->service->bindProperties($process, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($process->uriResource));
				$this->setData('message', __('Process saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($process->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', 'Edit process');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_process.tpl');
	}

	/**
	 * Delete a group or a group class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteProcess($this->getCurrentInstance());
		}
		// else{
			// $deleted = $this->service->deleteGroupClass($this->getCurrentClass());
		// }//no subclass available, therefore no delete action associated
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	public function authoring(){
		$this->setData('error', false);
		try{
			//get process instance to be authored
			$processDefinition = $this->getCurrentInstance();
			$this->setData('processUri', tao_helpers_Uri::encode($processDefinition->uriResource));
		}
		catch(Exception $e){
			$this->setData('error', true);
			$this->setData('errorMessage', $e);
		}
		$this->setView('process_authoring_tool.tpl');
	}
	
	
}
?>