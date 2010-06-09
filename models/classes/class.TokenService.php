<?php

error_reporting(E_ALL);

/**
 * Enable you to manage the token. 
 * A token is an abstract container embeding the data of a user 
 * for a particular execution of a process acitivity.
 * It helps you to define data scope and contexts,0
 *  to manage the process time line, to wait the other users and
 * to know the current state of a process execution.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
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

/* user defined includes */
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-includes begin
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-includes end

/* user defined constants */
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-constants begin
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-constants end

/**
 * Enable you to manage the token. 
 * A token is an abstract container embeding the data of a user 
 * for a particular execution of a process acitivity.
 * It helps you to define data scope and contexts,0
 *  to manage the process time line, to wait the other users and
 * to know the current state of a process execution.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_TokenService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute tokenClass
     *
     * @access protected
     * @var Class
     */
    protected $tokenClass = null;

    /**
     * Short description of attribute tokenActivityProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenActivityProp = null;

    /**
     * Short description of attribute tokenActivityExecutionProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenActivityExecutionProp = null;

    /**
     * Short description of attribute tokenCurrentUserProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenCurrentUserProp = null;

    /**
     * Short description of attribute tokenVariableProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenVariableProp = null;

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
        // section 127-0-1-1-24bd84b1:1291d596dba:-8000:0000000000001FB9 begin
        
    	$this->tokenClass = new core_kernel_classes_Class(CLASS_TOKEN);
    	
    	$this->tokenActivityProp 			= new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITY);
    	$this->tokenActivityExecutionProp 	= new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION);
    	$this->tokenCurrentUserProp 		= new core_kernel_classes_Property(PROPERTY_TOKEN_CURRENTUSER);
    	$this->tokenVariableProp 			= new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
    	
        // section 127-0-1-1-24bd84b1:1291d596dba:-8000:0000000000001FB9 end
    }

    /**
     * Short description of method getCurrents
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getCurrents( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F9A begin
        
        if(!is_null($activityExecution)){
        	
        	$activityUser = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER));
        	if(!is_null($activityUser)){
	        	
	        	$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
	        	$tokenCollection = $apiModel->getSubject(PROPERTY_TOKEN_ACTIVITYEXECUTION, $activityExecution->uriResource);
	        	foreach($tokenCollection->getIterator() as $token){
	        		$tokenUser = $token->getOnePropertyValue($this->tokenCurrentUserProp);
	        		if(!is_null($tokenUser)){
	        			if($tokenUser->uriResource == $activityUser->uriResource){
	        				$returnValue[$token->uriResource] = $token;
	        			}
	        		}
	        	}
        	}
        }
        
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F9A end

        return (array) $returnValue;
    }

    /**
     * Short description of method build
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource connector
     * @param  Resource user
     * @return array
     */
    public function build( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $user)
    {
        $returnValue = array();

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F9D begin
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F9D end

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource token
     * @return core_kernel_classes_Resource
     */
    public function duplicate( core_kernel_classes_Resource $token)
    {
        $returnValue = null;

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA2 begin
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA2 end

        return $returnValue;
    }

    /**
     * Short description of method merge
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array tokens
     * @return core_kernel_classes_Resource
     */
    public function merge($tokens)
    {
        $returnValue = null;

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA5 begin
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA5 end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource token
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $token)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA8 begin
        
        if(!is_null($token)){
        	$token->removePropertyValues($this->tokenActivityProp);
        	$token->removePropertyValues($this->tokenActivityExecutionProp);
        	$token->removePropertyValues($this->tokenCurrentUserProp);
        	$token->removePropertyValues($this->tokenVariableProp);
        	
        	$returnValue = $token->delete();
        }
        
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA8 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_TokenService */

?>