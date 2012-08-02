<?php

error_reporting(E_ALL);

/**
 * Manage the user in the workflow engine
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user management
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
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
 * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function loginUser($login, $password)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F59 begin

        if(parent::loginUser($login, $password)){

        	$currentUser = $this->getCurrentUser();
        	if(!is_null($currentUser)){
        		
				$_SESSION['taoqual.authenticated'] 		= true;
				$_SESSION['taoqual.lang']				= core_kernel_classes_Session::singleton()->getInterfaceLanguage();
				$_SESSION['taoqual.serviceContentLang'] = core_kernel_classes_Session::singleton()->getInterfaceLanguage();
				$_SESSION['taoqual.userId']				= $login;
				
				$returnValue = true;
        	}
        }

        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F59 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method feedAllowedRoles
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class roleClass
     * @return mixed
     */
    public function feedAllowedRoles( core_kernel_classes_Class $roleClass = null)
    {
        // section 127-0-1-1--2c34ff07:1291273bd7e:-8000:0000000000001F94 begin

			if (empty($roleClass)) {
				$roleClass = new core_kernel_classes_Class(CLASS_ROLE_WORKFLOWUSER);
			}
			$this->allowedRoles = array($roleClass->getUri());

        // section 127-0-1-1--2c34ff07:1291273bd7e:-8000:0000000000001F94 end
    }

    /**
     * method to format the data
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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