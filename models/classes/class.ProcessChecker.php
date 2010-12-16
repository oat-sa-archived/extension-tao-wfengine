<?php

error_reporting(E_ALL);

/**
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every service instances.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('tao/models/classes/class.Service.php');

/**
 * The wfEngine_models_classes_ProcessAuthoringService class provides methods to access and edit the process ontology
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_models_classes_ProcessChecker
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---
	
	protected $process = null;
	protected $authoringService = null;
	protected $initialActivities = array();
	protected $isolatedActivities = array();
	protected $isolatedConnectors = array();
	
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct($process)
    {
		$this->process = $process;
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		
		parent::__construct();
		
    }
	
	public function getInitialActivities(){
		return $this->initialActivities;
	}
	
	public function getIsolatedActivities(){
		return $this->isolatedActivities;
	}
	
	public function getIsolatedConnectors(){
		return $this->isolatedConnectors;
	}
	
	public function checkProcess($checkList = array()){
	
		$returnValue = false;
		
		$checkFunctions = array(
			'hasInitialActivity',
			'hasNoIsolatedActivity', 
			'hasNoIsolatedConnector'
		);
		if(!empty($checkList)){
			$checkFunctions = array_intersect($checkFunctions, $checkList);
		}
		
		foreach($checkFunctions as $function){
			if(method_exists($this, $function)){
				$returnValue = $this->$function();
				if(!$returnValue) break;
			}
		}
		
		return $returnValue;
	}
	
	public function hasInitialActivity($number = 0){
			
		$returnValue = false;
		
		$number = intval($number);
		$this->initialActivities = array();
		
		$process = $this->process;
		$count = 0;
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			
			if(wfEngine_helpers_ProcessUtil::isActivityInitial($activity)){
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
		
		return $returnValue;
	}
	
	public function hasNoIsolatedActivity(){
		
		$returnValue = true;
		
		$this->isolatedActivities = array();
		
		$process = $this->process;
		$apiModel = core_kernel_impl_ApiModelOO::singleton();
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			if(!wfEngine_helpers_ProcessUtil::isActivityInitial($activity)){
				//should have a previous activity:
				$connectorsCollection = $apiModel->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES, $activity->uriResource);
				if($connectorsCollection->isEmpty()){
					$returnValue = false;
					$this->isolatedActivities[$activity->uriResource] = $activity;
				}
			}
		}
		
		return $returnValue;
	}
	
	public function hasNoIsolatedConnector(){
		
		$returnValue = true;
		
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
		
		return $returnValue;
	}
	
	private function isIsolatedConnector($connector){
		
		$returnValue = true;
		
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
		
		return $returnValue;
	}
	
} /* end of class wfEngine_models_classes_ProcessAuthoringService */

?>
