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
		$this->authoringService = new taoDelivery_models_classes_ProcessAuthoringService();
		$this->defaultData();
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
			'http://www.tao.lu/middleware/Interview.rdf#122354397139712'
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
		
		$this->setData('formTitle', 'Edit process');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_process.tpl');
	}
	
	
	/**
	 * Add a group subclass
	 * @return void
	 */
	public function addGroupClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createGroupClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->uriResource)
			));
		}
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
			$deleted = $this->service->deleteGroup($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteGroupClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	
	/**
	 * Get the data to populate the tree of group's subjects
	 * @return void
	 */
	public function getMembers(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_SUBJECT_CLASS), true, true, ''));
	}
	
	/**
	 * Save the group related subjects
	 * @return void
	 */
	public function saveMembers(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$members = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($members, tao_helpers_Uri::decode($value));
			}
		}
		$group = $this->getCurrentInstance();
		
		if($this->service->setRelatedSubjects($group, $members)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	/**
	 * Get the data to populate the tree of group's tests
	 * @return void
	 */
	public function getTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		echo json_encode($this->service->toTree( new core_kernel_classes_Class(TAO_TEST_CLASS), true, true, ''));
	}
	
	/**
	 * Save the group related subjects
	 * @return void
	 */
	public function saveTests(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		$tests = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($tests, tao_helpers_Uri::decode($value));
			}
		}
		$group = $this->getCurrentInstance();
		
		if($this->service->setRelatedTests($group, $tests)){
			$saved = true;
		}
		echo json_encode(array('saved'	=> $saved));
	}
	
	
}
?>