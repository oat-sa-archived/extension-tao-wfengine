<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - wfEngine/models/classes/class.UserService.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.06.2010, 14:40:44 with ArgoUML PHP module 
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
						
						// $this->feedAllowedRoles();
					}
	        	}
        	}
        }
        
        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F59 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllUsers
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return array
     */
    public function getAllUsers($options)
    {
        $returnValue = array();

        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F88 begin
        
    	$roleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RoleService');
     	
    	
        $userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        foreach($userClass->getInstances(true) as $user){
           	if($roleService->checkUserRole($user, new core_kernel_classes_Class(CLASS_ROLE_BACKOFFICE))){
           		$returnValue[$user->uriResource] = $user;
           	}
        }
        
        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F88 end

        return (array) $returnValue;
    }

    /**
     * Short description of method saveUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource user
     * @param  array properties
     * @param  Resource role
     * @return boolean
     */
    public function saveUser( core_kernel_classes_Resource $user, $properties = array(),  core_kernel_classes_Resource $role = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F8B begin
        
        if(is_null($user)){		
			//Create user here:
			if(is_null($role)){
				$role = new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE);
			}
			$user = $this->createInstance(new core_kernel_classes_Class($role->uriResource));
		}
		
    	if(!is_null($user)){
			$returnValue = $this->bindProperties($user, $properties);
		}
        
        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F8B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method toTree
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function toTree()
    {
        $returnValue = array();

        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F92 begin
        
        $users = $this->getAllUsers(array('order'=>'login'));
		foreach($users as $user){
			$login = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LABEL));
			$returnValue[] = array(
					'data' 	=> tao_helpers_Display::textCutter($user->getLabel(), 16),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($user->uriResource),
						'class' => 'node-instance',
						'title' => __('login: ').$login
					)
				);
			
		}
        
        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F92 end

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_UserService */

?>