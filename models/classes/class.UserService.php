<?php

error_reporting(E_ALL);

/**
 * Manage the user in the workflow engine
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user management
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function initRoles()
    {
        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F55 begin

		$this->allowedRoles = array(INSTANCE_ROLE_WORKFLOW => new core_kernel_classes_Resource(INSTANCE_ROLE_WORKFLOW));

        // section 127-0-1-1-951b66:128b0d3ece8:-8000:0000000000001F55 end
    }

    /**
     * login a user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * method to format the data
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  array options
     * @return array
     */
    public function toTree( core_kernel_classes_Class $clazz, $options)
    {
        $returnValue = array();

        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F92 begin

        $users = $this->getAllUsers(array('order' => 'login'));
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