<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - wfEngine/models/classes/class.UserService.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.05.2010, 17:00:26 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user management
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.UserService.php');

/* user defined includes */
// section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F53-includes begin
// section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F53-includes end

/* user defined constants */
// section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F53-constants begin
// section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F53-constants end

/**
 * Short description of class wfEngine_models_classes_UserService
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_UserService
    extends tao_models_classes_UserService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initRoles
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initRoles()
    {
        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F55 begin
        
		$this->allowedRoles = array(CLASS_ROLE_BACKOFFICE);
        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F55 end
    }

    /**
     * Short description of method loginUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function loginUser($login, $password)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F59 begin
        
        if(parent::loginUser($login, $password)){
        	
        	if($this->connectCurrentUser()){
	        	$currentUser = $this->getCurrentUser();
	        	if(!is_null($currentUser)){
	        		
	        		$login 			= (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
	        		$password 		= (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
					try{
	        			$dataLang 	= (string)$currentUser->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_DEFLG));
					}
					catch(common_Exception $ce){
						$dataLang 	= 'EN';
					}
					
	        		//log in the wf engines
					$_SESSION["WfEngine"] 		= WfEngine::singleton($login, $password);
					$user = WfEngine::singleton()->getUser();
					if($user == null) {
						$returnValue=  false;
					}
					else{
						$_SESSION["userObject"] 	= $user;
							
						// Taoqual authentication and language markers.
						$_SESSION['taoqual.authenticated'] 		= true;
						$_SESSION['taoqual.lang']				= $dataLang;
						$_SESSION['taoqual.serviceContentLang'] = $dataLang;
						$_SESSION['taoqual.userId']				= $login;
						
						$returnValue = true;
						
						$this->feedAllowedRoles(); die();
					}
	        	}
        	}
        }
        
        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F59 end

        return (bool) $returnValue;
    }
	
	public function feedAllowedRoles(core_kernel_classes_Class $roleClass=null){
		if(empty($roleClass)){
			$roleClass = new core_kernel_classes_Class(CLASS_ROLE_BACKOFFICE);	
		}	
		
		// $acceptedRole =  array_merge(array($roleClass->uriResource) , array_keys($roleClass->getInstances(true))); 
    	$this->allowedRoles = array_keys($roleClass->getInstances(true));
	}
	
	public function toTree(){
		$this->feedAllowedRoles();
		$users = $this->getAllUsers(array('order'=>'login'));
		$instancesData = array();
		foreach($users as $user){
			$login = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LABEL));
			$instancesData[] = array(
					'data' 	=> tao_helpers_Display::textCutter($user->getLabel(), 16),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($user->uriResource),
						'class' => 'node-instance',
						'title' => __('login: ').$login
					)
				);
			
		}
		return $instancesData;
	}
	
	public function saveUser( core_kernel_classes_Resource $user = null, $properties = array(), core_kernel_classes_Resource $role=null)
    {
        $returnValue = (bool) false;
		// var_dump($user);
		if(is_null($user)){		
			//Create user here:
			if(is_null($role)){
				$role = new core_kernel_classes_Resource(INSTANCE_ROLE_WORKFLOWUSER);
			}
			$user = $this->createInstance(new core_kernel_classes_Class($role->uriResource));
			var_dump($user->getRdfTriples());
		}
		
		if(!is_null($user)){
			$returnValue = $this->bindProperties($user, $properties);
		}
		
        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_UserService */

?>