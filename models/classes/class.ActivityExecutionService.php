<?php

error_reporting(E_ALL);

/**
 * This service enables you to manage, control, restrict the process activities
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
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-includes begin
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-includes end

/* user defined constants */
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-constants begin
// section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5B-constants end

/**
 * This service enables you to manage, control, restrict the process activities
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ActivityExecutionService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Get the list of available ACL modes
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getAclModes()
    {
        $returnValue = array();

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6B begin
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6B end

        return (array) $returnValue;
    }

    /**
     * Define the ACL mode of an activity
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @param  Resource mode
     * @param  Resource target
     * @return core_kernel_classes_Resource
     */
    public function setAcl( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $mode,  core_kernel_classes_Resource $target = null)
    {
        $returnValue = null;

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5D begin
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F5D end

        return $returnValue;
    }

    /**
     * Link an activity execution to the current user (it will enables you to
     * the execution for that user or restrict it afterwhile)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource currentUser
     * @return boolean
     */
    public function bindExecution( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F78 begin
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F78 end

        return (bool) $returnValue;
    }

    /**
     * Check the ACL of a user for a given activity.
     * It returns false if the user cannot access the activity.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource currentUser
     * @return boolean
     */
    public function checkAcl( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F62 begin
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F62 end

        return (bool) $returnValue;
    }

    /**
     * Get the list of available process execution for a user
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource process
     * @param  Resource currentUser
     * @return array
     */
    public function getProcessActivities( core_kernel_classes_Resource $process,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = array();

        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6F begin
        // section 127-0-1-1--10e47d9e:128d54bbb0d:-8000:0000000000001F6F end

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityExecutionService */

?>