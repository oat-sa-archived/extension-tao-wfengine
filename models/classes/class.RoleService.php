<?php

error_reporting(E_ALL);

/**
 * 
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoGroups
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');


/**
 * 
 *
 * @access public
 * @author taoTeam
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_RoleService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level group class
     *
     * @access protected
     * @var Class
     */
    protected $roleClass = null;

    /**
     * The ontologies to load
     *
     * @access protected
     * @var array
     */
    protected $processOntologies = array(NS_TAOQUAL);
	
	 /**
     * Short description of attribute generisUserService
     *
     * @access protected
     * @var Service
     */
    protected $generisUserService = null;
	
    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        		
		parent::__construct();
		$this->generisUserService = core_kernel_users_Service::singleton();
		$this->roleClass = new core_kernel_classes_Class(INSTANCE_ROLE_BACKOFFICE);
		$this->loadOntologies($this->processOntologies);
		
    }

    /**
     * get a role subclass by uri. 
     * If the uri is not set, it returns the group class (the top level class.
     * If the uri don't reference a group subclass, it returns null
     *
     * @access public
     * @author TAO team
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getRoleClass($uri = '')
    {
        $returnValue = null;
		
		if(empty($uri) && !is_null($this->roleClass)){
			$returnValue = $this->roleClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isRoleClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }

    /**
     * Short description of method getGroup
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier usually the test label or the ressource URI
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getRole($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		
		if(is_null($clazz) && $mode == 'uri'){
			try{
				$resource = new core_kernel_classes_Resource($identifier);
				$type = $resource->getUniquePropertyValue(new core_kernel_classes_Property( RDF_TYPE ));
				$clazz = new core_kernel_classes_Class($type->uriResource);
			}
			catch(Exception $e){}
		}
		if(is_null($clazz)){
			$clazz = $this->roleClass;
		}
		if($this->isRoleClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		

        return $returnValue;
    }

    /**
     * Short description of method createGroup
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string label
     * @param  ContainerCollection members
     * @param  ContainerCollection tests
     * @return core_kernel_classes_Resource
     */
    public function createProcess($label,  core_kernel_classes_ContainerCollection $members,  core_kernel_classes_ContainerCollection $tests)
    {
        $returnValue = null;


        return $returnValue;
    }
	

    /**
     * Short description of method isRoleClass
     *
     * @access public
     * @author TAO Team
     * @param  Class clazz
     * @return boolean
     */
    public function isRoleClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($clazz->uriResource == $this->roleClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->roleClass->getSubClasses() as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}

        return (bool) $returnValue;
    }
 
	public function setRoleToUsers(core_kernel_classes_Resource $role, $users=array()){
		
		$returnValue = false;
		
		//get all users who have the following role:
    	$allUsers = $this->getUsers($role);
		
		$forbidden = array(
			INSTANCE_ROLE_TAOMANAGER,
			INSTANCE_ROLE_WORKFLOWUSER
		);
		
		if(!in_array($role->uriResource, $forbidden)){
		
			foreach($allUsers as $user){
				//delete the current role
				$returnValue = core_kernel_impl_ApiModelOO::singleton()->removeStatement($user, RDF_TYPE, $role->uriResource, '');
				
			}
			
			foreach($users as $userUri){
				$userInstance = new core_kernel_classes_Resource($userUri);
				$returnValue = $userInstance->setPropertyValue(new core_kernel_classes_Property(RDF_TYPE), $role->uriResource);
			}
			
		}
		
		return $returnValue;
		
	}	
	
	public function getUsers(core_kernel_classes_Resource $role){
		$allUsers = array();
		
		$userClass = new core_kernel_classes_Class($role->uriResource);	
    	$allUsers = array_keys($userClass->getInstances(true));
		
		return $allUsers;
	}
	
	public function createInstance($label='', core_kernel_classes_Class $clazz = null){
		if(is_null($clazz)){
			$clazz = $this->getRoleClass();
		}
		
		if( empty($label) ){
			$label = 'Role_' . (count($clazz->getInstances()) + 1);
		}
		
		$returnValue = $this->generisUserService->addRole($label, 'created by RoleService', new core_kernel_classes_Class(CLASS_ROLE_BACKOFFICE));
		
		return $returnValue;
	}
	
} /* end of class wfEngine_models_classes_RoleService */

?>