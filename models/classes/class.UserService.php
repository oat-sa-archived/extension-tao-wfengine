<?php

error_reporting(E_ALL);

/**
 * Manage the user in the workflow engine
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
 * Manage the user in the workflow engine
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
     * initialize the roles
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
     * login a user
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
							
					$_SESSION['taoqual.authenticated'] 		= true;
					$_SESSION['taoqual.lang']				= $dataLang;
					$_SESSION['taoqual.serviceContentLang'] = $dataLang;
					$_SESSION['taoqual.userId']				= $login;
					
					$returnValue = true;
	        	}
        	}
        }
        
        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F59 end

        return (bool) $returnValue;
    }

    /**
     * get all the users
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
        
    	$roleService = wfEngine_models_classes_RoleService::singleton();
    	
        $userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$users = array();
        foreach($userClass->getInstances(true) as $user){
           	if($roleService->checkUserRole($user, new core_kernel_classes_Class(CLASS_ROLE_BACKOFFICE))){
           		$users[$user->uriResource] = $user;
           	}
        }
        
		$keyProp = null;
       	if(isset($options['order'])){
        	switch($options['order']){
        		case 'login'		: $prop = PROPERTY_USER_LOGIN; break;
        		case 'password'		: $prop = PROPERTY_USER_PASSWORD; break;
        		case 'uilg'			: $prop = PROPERTY_USER_UILG; break;
        		case 'deflg'		: $prop = PROPERTY_USER_DEFLG; break;
        		case 'mail'			: $prop = PROPERTY_USER_MAIL; break;
        		case 'firstname'	: $prop = PROPERTY_USER_FIRTNAME; break;
        		case 'lastname'		: $prop = PROPERTY_USER_LASTNAME; break;
        		case 'name'			: $prop = PROPERTY_USER_FIRTNAME; break;
        	}
        	$keyProp = new core_kernel_classes_Property($prop);
        }
       
        $index = 0;
        foreach($users as $user){
        	$key = $index;
        	if(!is_null($keyProp)){
        		try{
        			$key = $user->getUniquePropertyValue($keyProp);
        			if(!is_null($key)){
        				if($key instanceof core_kernel_classes_Literal){
        					$returnValue[(string)$key] = $user;
        				}
        				if($key instanceof core_kernel_classes_Resource){
        					$returnValue[$key->getLabel()] = $user;
        				}
        				continue;
        			}
        		}
        		catch(common_Exception $ce){}
        	}
        	$returnValue[$key] = $user;
        	$index++;
        }
      	
    	if(isset($options['orderDir'])){
    		if(isset($options['order'])){
    			if(strtolower($options['orderDir']) == 'asc'){
   					ksort($returnValue, SORT_STRING);
    			}
    			else{
    				krsort($returnValue, SORT_STRING);
    			}
   			}
   			else{
   				if(strtolower($options['orderDir']) == 'asc'){
	   				sort($returnValue);
	   			}   
	   			else{
	   				rsort($returnValue);
	   			}  
   			}
        }
        (isset($options['start'])) 	? $start = $options['start'] 	: $start = 0;
        (isset($options['end']))	? $end	= $options['end']		: $end	= count($returnValue);
		
        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F88 end

        return (array) $returnValue;
    }

    /**
     * save a user (the role is managed in addition)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource user
     * @param  array properties
     * @param  Resource role
     * @return boolean
     */
    public function saveUser( core_kernel_classes_Resource $user = null, $properties = array(),  core_kernel_classes_Resource $role = null)
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
     * Short description of method feedAllowedRoles
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class roleClass
     * @return mixed
     */
    public function feedAllowedRoles( core_kernel_classes_Class $roleClass = null)
    {
        // section 127-0-1-1--2c34ff07:1291273bd7e:-8000:0000000000001F94 begin
        
    	if(empty($roleClass)){
			$roleClass = new core_kernel_classes_Class(CLASS_ROLE_BACKOFFICE);	
		}	
    	$this->allowedRoles = array_keys($roleClass->getInstances(true));
    	
        // section 127-0-1-1--2c34ff07:1291273bd7e:-8000:0000000000001F94 end
    }

    /**
     * method to format the data
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