<?php

error_reporting(E_ALL);

/**
 * This class provides a service on user management. It extends the common user
 * to add the data related to the workflow engine user managament
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
// section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023AE-includes begin
// section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023AE-includes end

/* user defined constants */
// section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023AE-constants begin
// section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023AE-constants end

/**
 * This class provides a service on user management. It extends the common user
 * to add the data related to the workflow engine user managament
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

    /**
     * Short description of attribute wfUserClass
     *
     * @access protected
     * @var Class
     */
    protected $wfUserClass = null;

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
        // section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023B5 begin
        
    	if(!defined('CLASS_WORKFLOW_USER')){
    		include_once('wfEngine/includes/constants.php');
    	}
    	
    	parent::__construct();
    	
    	$this->wfUserClass = new core_kernel_classes_Class(CLASS_WORKFLOW_USER);
    	
        // section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023B5 end
    }

    /**
     * Short description of method saveUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array user
     * @return boolean
     */
    public function saveUser($user)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023B0 begin
        
        $returnValue = parent::saveUser($user);
        
        if(isset($user['acl'])){
        	if(in_array('wf', $user['acl'])){
        		
        		try{
	        		//check if user exist in ontology
	        		$wfUser = $this->getOneWfUser($user['login']);
	        		
	        		if(is_null($wfUser)){
	        			//add a user in the wf ontology
	        			$wfUser = $this->createInstance($this->wfUserClass);
	        			$this->bindProperties($wfUser, array(PROPERTY_USER_LOGIN =>  $user['login']));
	        		}
	        		
	        		$this->bindProperties($wfUser, array(
	        			PROPERTY_USER_PASSWORD 	=> $user['password'],
	        			USER_ROLE				=> $user['wfRole']
	        		));
        		}
        		catch(common_Exception $ce){
        			$returnValue = false;
        		}
        	}
        }
        
        // section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023B0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllRoles
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getAllRoles()
    {
        $returnValue = array();

        // section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023B3 begin
        
        $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
        foreach($roleClass->getInstances(false) as $role){
        	$returnValue[] = $role;
        }
        
        // section 127-0-1-1-e87e5e4:127f196b89d:-8000:00000000000023B3 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getOneWfUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string login
     * @return core_kernel_classes_Resource
     */
    public function getOneWfUser($login)
    {
        $returnValue = null;

        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023B3 begin
        
        $loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
        foreach($this->wfUserClass->getInstances(true) as $wfUser){
        	try{
	        	if(trim($wfUser->getUniquePropertyValue($loginProperty)) == trim($login)){
	        		$returnValue = $wfUser;
	        		break;
	        	}
        	}
        	catch(common_Exception $ce){}
        }
        
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023B3 end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_UserService */

?>