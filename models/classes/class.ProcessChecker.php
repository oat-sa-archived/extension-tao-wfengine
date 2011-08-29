<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.ProcessChecker.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.08.2011, 17:04:59 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
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
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004F8F-includes begin
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004F8F-includes end

/* user defined constants */
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004F8F-constants begin
// section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004F8F-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessChecker
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessChecker
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute process
     *
     * @access protected
     * @var Resource
     */
    protected $process = null;

    /**
     * Short description of attribute authoringService
     *
     * @access protected
     * @var Resource
     */
    protected $authoringService = null;

    /**
     * Short description of attribute initialActivities
     *
     * @access protected
     * @var array
     */
    protected $initialActivities = array();

    /**
     * Short description of attribute isolatedActivities
     *
     * @access protected
     * @var array
     */
    protected $isolatedActivities = array();

    /**
     * Short description of attribute isolatedConnectors
     *
     * @access protected
     * @var array
     */
    protected $isolatedConnectors = array();

    /**
     * Short description of attribute activityService
     *
     * @access protected
     * @var ActivityService
     */
    protected $activityService = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource process
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $process)
    {
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004F9F begin
		$this->process = $process;
		$this->activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		
		parent::__construct();
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004F9F end
    }

    /**
     * Short description of method check
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  array checkList
     * @return boolean
     */
    public function check($checkList = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FA2 begin
		$classMethods = get_class_methods(get_class($this));
		$checkFunctions = array();
		foreach($classMethods as $functionName){
			if(preg_match('/^check(.)+/', $functionName)){
				$checkFunctions[] = $functionName;
			}
		}
		
		if(!empty($checkList)){
			$checkFunctions = array_intersect($checkFunctions, $checkList);
		}
		
		foreach($checkFunctions as $function){
			if(method_exists($this, $function)){
				$returnValue = $this->$function();
				if(!$returnValue) break;
			}
		}
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FA2 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkInitialActivity
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  int number
     * @return boolean
     */
    public function checkInitialActivity($number = 0)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FAF begin
		$number = intval($number);
		$this->initialActivities = array();
		
		$process = $this->process;
		$count = 0;
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			
			if($this->activityService->isInitial($activity)){
				$this->initialActivities[$activity->uriResource] = $activity;
				$count++;
				if($number && ($count>$number)){
					// throw new wfEngine_models_classes_QTI_ProcessDefinitionException('too many initial activity');
					$returnValue = false;
					break;
				}
			}
		}
		
		if($number){
			$returnValue = ($count==$number)?true:false;
		}else{
			//number == 0 means at least one
			$returnValue = ($count>0)?true:false;
		}
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FAF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkNoIsolatedActivity
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return boolean
     */
    public function checkNoIsolatedActivity()
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB3 begin
		$returnValue = true;//need to be initiated as true
		
		$this->isolatedActivities = array();
		
		$connectorsClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$process = $this->process;
		$apiModel = core_kernel_impl_ApiModelOO::singleton();
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			if(!$this->activityService->isInitial($activity)){
				//should have a previous activity:
				$connectors = $connectorsClass->searchInstances(array(PROPERTY_CONNECTORS_NEXTACTIVITIES => $activity->uriResource), array('like'=>false, 'recursive' => 0));
				if(empty($connectors)){
					$returnValue = false;
					$this->isolatedActivities[$activity->uriResource] = $activity;
				}
			}
		}
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkNoIsolatedConnector
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return boolean
     */
    public function checkNoIsolatedConnector()
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB5 begin
		$returnValue = true;//need to be initiated as true
		
		$this->isolatedConnectors = array();
		
		$process = $this->process;
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			$nextConnectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
			foreach($nextConnectors['next'] as $connector){
				
				$returnValue = false;
				if(!$this->isIsolatedConnector($connector)){
					$returnValue = true;
				}
				
			}
		}
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getInitialActivities
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return array
     */
    public function getInitialActivities()
    {
        $returnValue = array();

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB7 begin
		$returnValue = $this->initialActivities;
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB7 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getIsolatedActivities
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return array
     */
    public function getIsolatedActivities()
    {
        $returnValue = array();

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB9 begin
		$returnValue = $this->isolatedActivities;
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FB9 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getIsolatedConnectors
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return array
     */
    public function getIsolatedConnectors()
    {
        $returnValue = array();

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FBB begin
		$returnValue = $this->isolatedConnectors;
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FBB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isIsolatedConnector
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource connector
     * @return boolean
     */
    public function isIsolatedConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FBD begin
		$returnValue = true;//need to be initiated as true
		
		$propNextActivities = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		foreach($connector->getPropertyValuesCollection($propNextActivities)->getIterator() as $nextActivityOrConnector){
			
			if(wfEngine_helpers_ProcessUtil::isActivity($nextActivityOrConnector)){
				$returnValue = false;
			}else if(wfEngine_helpers_ProcessUtil::isConnector($nextActivityOrConnector)){
				$isolated = $this->isIsolatedConnector($nextActivityOrConnector);
				if($returnValue){
					$returnValue = $isolated;
				}
			}else{
				throw new Exception('the next acitivty is neither an activity nor a connector');
			}
		}
		if($returnValue){
			$this->isolatedConnectors[$connector->uriResource] = $connector; 
		}
        // section 10-13-1-39--7378788e:12e4d9bbe63:-8000:0000000000004FBD end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessChecker */

?>