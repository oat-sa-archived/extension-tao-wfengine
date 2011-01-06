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
 * include Service
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Service.php');

/**
 * include WfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.WfResource.php');

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
    extends WfResource
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
     * @param ActivityExecution
     * @return void
     */
    public function __construct( ProcessExecution $processExecution,  core_kernel_classes_Resource $activityExecutionResource)
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087B begin
    	parent::__construct($activityExecutionResource->uriResource);
		$this->processExecution = $processExecution;
		$activityResource = $activityExecutionResource->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY));
		$this->activity = new Activity($activityResource->uriResource);
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
		$tokensProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_TOKEN);
		$tokens = $this->processExecution->resource->getPropertyValues($tokensProp);
		
		
		if (in_array($this->activity->uri,$tokens))
			{
				return true;
			}
		else trigger_error("<div style=\"background-color:white;padding:2px;\">This activity may not be  run for the moment</div>");
		return false;
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:000000000000087D end
    }
    
    public function isFinished(){
    	$isFinished = $this->resource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED));
    	if(!is_null($isFinished)){
    		return ($isFinished->uriResource == GENERIS_TRUE);
    	}
    	return false;
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
     * Short description of method getInteractiveServices
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getInteractiveServices()
    {
        $returnValue = array();

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000881 begin
		
		$returnValue = $this->activity->getServices($this);
		
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000881 end

        return (array) $returnValue;
    }
	
	public function getToken(){
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		$token = $tokenService->getCurrent($this->resource);
		return $token;
	}

} /* end of class ActivityExecution */

?>