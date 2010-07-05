<?php

error_reporting(E_ALL);

/**
 * Manage the roles for the user workflow
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide service on user roles management
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.RoleService.php');

/* user defined includes */
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-includes begin
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-includes end

/* user defined constants */
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-constants begin
// section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8B-constants end

/**
 * Manage the roles for the user workflow
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_RoleService
    extends tao_models_classes_RoleService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * initialize the roles of the service
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initRole()
    {
        // section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8D begin
        
    	$this->roleClass = new core_kernel_classes_Class(CLASS_ROLE_WORKFLOWUSER);
    	
        // section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8D end
    }

} /* end of class wfEngine_models_classes_RoleService */

?>