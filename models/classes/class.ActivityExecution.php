<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.ActivityExecution.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 21.08.2008, 15:33:42
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include Activity
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Activity.php');

/**
 * include ProcessExecution
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ProcessExecution.php');

/**
 * include Tool
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Tool.php');

/**
 * include wfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.wfResource.php');

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000860-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000860-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000860-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000860-constants end

/**
 * Short description of class ActivityExecution
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class ActivityExecution
    extends wfResource
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processExecution
     *
     * @access public
     * @var ProcessExecution
     */
    public $processExecution = null;

    /**
     * Short description of attribute activity
     *
     * @access public
     * @var Activity
     */
    public $activity = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param ProcessExecution
     * @param Activity
     * @return void
     */
    public function __construct( ProcessExecution $processExecution,  Activity $activity)
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087B begin
    	$this->processExecution = $processExecution;
		$this->activity = $activity;
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087B end
    }

    /**
     * Short description of method isExecutable
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function isExecutable()
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087D begin
		$tokens = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,array($this->processExecution->uri),array(PROPERTY_PINSTANCES_TOKEN),array(""));
		
		if (in_array($this->activity->uri,$tokens))
			{
				$rolesAllowed = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,array($this->activity->uri),array(ACTIVITY_ROLE),array(""));
				
				$rolesUser = getInstancePropertyValues(Wfengine::singleton()->sessionGeneris,array(Wfengine::singleton()->user->userUri),array(USER_ROLE),array(""));


				foreach ($rolesAllowed as $roleAllowed)

					{
						if (in_array($roleAllowed,$rolesUser)) return true;
					}
				
				trigger_error("<div style=\"background-color:white;padding:2px;\">Sorry, You are not allowed to perform this activity</div>");
				
			}
		else trigger_error("<div style=\"background-color:white;padding:2px;\">This activity may not be  run for the moment</div>");
		return false;
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087D end
    }

    /**
     * Short description of method getStatementsAssignations
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function getStatementsAssignations()
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087F begin
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087F end
    }

    /**
     * Short description of method getInteractiveTools
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getInteractiveTools()
    {
        $returnValue = array();

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000881 begin
		
		$returnValue = $this->activity->getTools($this);
		
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000881 end

        return (array) $returnValue;
    }

} /* end of class ActivityExecution */

?>