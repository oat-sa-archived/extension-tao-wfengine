<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * Role Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Role extends TaoModule {
	
	
	protected $authoringService = null;
	protected $forbidden = null;
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Role
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new wfEngine_models_classes_RoleService();
		$this->defaultData();
		
		Session::setAttribute('currentSection', 'role');
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
		$role = $this->service->getRole($uri);
		if(is_null($role)){
			throw new Exception("No role found for the uri {$uri}");
		}
		
		return $role;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getRoleClass();
	}
	
/*
 * controller actions
 */
	
	/**
	*	forbidden to edit class and create subclass
	*/
	
	/**
	*	index:
	*/
	public function index(){
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		$this->setData('section',Session::getAttribute('currentSection'));
		$this->setView('role/index.tpl');
	}
	
	/**
	 * Edit a group instance
	 * @see tao_helpers_form_GenerisFormFactory::instanceEditor
	 * @return void
	 */
	public function editRole(){
		$clazz = $this->getCurrentClass();
		$role = $this->getCurrentInstance();
		
		$excludedProperties = array(
			'http://www.tao.lu/middleware/Interview.rdf#i122354397139712'
		);
		
		
		$myForm = wfEngine_helpers_ProcessFormFactory::instanceEditor($clazz, $role, 'roleEditor', array("noSubmit"=>true,"noRevert"=>true), $excludedProperties);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$role = $this->service->bindProperties($role, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($role->uriResource));
				$this->setData('message', __('Role saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('users', json_encode(array_map("tao_helpers_Uri::encode", $this->service->getUsers($role) )));
		$this->setData('uri', tao_helpers_Uri::encode($role->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', 'Edit Role');
		$this->setData('myForm', $myForm->render());
		$this->setView('role/form.tpl');
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

			$role = $this->getCurrentInstance();
		
			if(!in_array($role->uriResource, $this->forbidden)){
					//check if no user is using this role:
					$roleClass = new core_kernel_classes_Class($role->uriResource);
					$users = $roleClass->getInstances();
					if(empty($users)){
						//delete role here:
						$deleted = $this->service->deleteRole($role);
					}else{
						//set message error
						// $this->setData('message', 'nope');
						throw new Exception(__('The role is using by one or several users. Please remove the role to these users first.'));
					}
			}else{
				throw new Exception($role->getLabel().' cannot be deleted');
			}
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	public function getUsers(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		echo json_encode($userService->toTree());
	}
	
	/**
	 * save from the checkbox tree the users to link with 
	 * @return void
	 */
	public function saveUsers(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		$users = array();
		foreach($this->getRequestParameters() as $key => $value){
			if(preg_match("/^instance_/", $key)){
				array_push($users, tao_helpers_Uri::decode($value));
			}
		}
		
		$role = $this->getCurrentInstance();
		
		if($this->service->setRoleToUsers($role, $users)){
			$saved = true;
		}

		
		echo json_encode(array('saved'	=> $saved));
	}
	
	public function addInstance(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$instance = $this->service->createInstance($this->service->getRoleClass());
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $instance->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
			));
		}
	}
	
	public function editRoleClass(){
		$clazz = $this->getCurrentClass();
		//display it but do not allow it to be saved
		$myForm = $this->editClass($clazz, $this->service->getRoleClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Role Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Role class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_process.tpl');
	}
	
}
?>