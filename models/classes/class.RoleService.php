<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - wfEngine/models/classes/class.RoleService.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.06.2010, 14:02:53 with ArgoUML PHP module 
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
 * include tao_models_classes_RoleService
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
 * Short description of class wfEngine_models_classes_RoleService
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
     * Short description of method initRole
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initRole()
    {
        // section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8D begin
        
    	$this->roleClass = new core_kernel_classes_Class(INSTANCE_ROLE_WORKFLOWUSER);
    	
        // section 127-0-1-1-7f226444:12902c0ab92:-8000:0000000000001F8D end
    }

} /* end of class wfEngine_models_classes_RoleService */

?>