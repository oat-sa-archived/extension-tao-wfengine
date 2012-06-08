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

		$this->allowedRoles = array(CLASS_ROLE_WORKFLOWUSER);

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
     * get all the users
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array options
     * @return array
     */
    public function getAllUsers($options)
    {
        $returnValue = array();

        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F88 begin

    	$roleService = wfEngine_models_classes_RoleService::singleton();
    	$fields = array('login' => PROPERTY_USER_LOGIN,
						'password' => PROPERTY_USER_PASSWORD,
						'uilg' => PROPERTY_USER_UILG,
						'deflg' => PROPERTY_USER_DEFLG,
						'mail' => PROPERTY_USER_MAIL,
						'firstname' => PROPERTY_USER_FIRTNAME,
						'lastname' => PROPERTY_USER_LASTNAME,
						'name' => PROPERTY_USER_FIRTNAME);
		$ops = array('eq' => "%s",
					 'bw' => "%s*",
					 'ew' => "*%s",
					 'cn' => "*%s*");
        $userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$users = array();

		$backoffice = new core_kernel_classes_Class(CLASS_ROLE_BACKOFFICE);
		$types = array();
		$bos = $backoffice->getInstances(true, array());
		foreach ($bos as $i => $e) {
			$types[] = $i;
		}

		$opts = array('recursive' => 0, 'like' => false, 'additionalClasses' => $types);
		if (isset($options['start'])) $opts['offset'] = $options['start'];
		if (isset($options['end'])) $opts['limit'] = $options['end'];

		$crits = array(PROPERTY_USER_LOGIN => '*');
		if (isset($options['search']) && !is_null($options['search']) && isset($options['search']['string']) && isset($ops[$options['search']['op']])) {
			$crits[$fields[$options['search']['field']]] = sprintf($ops[$options['search']['op']], $options['search']['string']);
		}
		foreach ($userClass->searchInstances($crits, $opts) as $user) {
			if ($user->uriResource != 'http://www.tao.lu/Ontologies/TAO.rdf#installator') {
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
        //(isset($options['start'])) 	? $start = $options['start'] 	: $start = 0;
        //(isset($options['end']))	? $end	= $options['end']		: $end	= count($returnValue);

        // section 127-0-1-1-718243b3:12912642ee4:-8000:0000000000001F88 end

        return (array) $returnValue;
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
				$roleClass = new core_kernel_classes_Class(CLASS_ROLE_BACKOFFICE);
			}
			$this->allowedRoles = array_keys($roleClass->getInstances(true));

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