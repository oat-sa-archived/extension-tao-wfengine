<?php

/**
 *  Montitor Controler provide actions to manage processes
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_actions_Monitor extends tao_actions_TaoModule {
	
	/**
	 * 
	 */
	public function getRootClass()
	{
		return null;
	}
	
	/**
	 * The monitoring front page
	 * -> Display current process status
	 * -> Display current activities status
	 * -> Display activities history
	 */
	public function index()
	{
		//Class to filter on
		$clazz = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$properties = array();

		//Properties to filter on
		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$properties[] = new core_kernel_classes_Property(LOCAL_NAMESPACE."#countryCode");
		$properties[] = new core_kernel_classes_Property(LOCAL_NAMESPACE."#languageCode");
		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		
		//Monitoring data
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_TranslationProcessMonitoringGrid(array(), array(
			'columns' => array(
				'http://www.w3.org/2000/01/rdf-schema#label' 													=> array('weight'=>3)
				, 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesExecutionOf' 				=> array('weight'=>2)
				, 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions' => array('weight'=>4, 'widget'=>'CurrentActivities')
			)
		));
		$grid = $processMonitoringGrid->getGrid();
		$model = $grid->getColumnsModel();
		
		$processHistoryGrid = new wfEngine_helpers_Monitoring_TranslationExecutionHistoryGrid(new core_kernel_classes_Resource('yeah'));
		$historyProcessModel = $processHistoryGrid->getGrid()->getColumnsModel();
		
		//Filtering data
		$this->setData('clazz', $clazz);
		$this->setData('properties', $properties);
		
		//Monitoring data
		$this->setData('model', json_encode($model));
		$this->setData('historyProcessModel', json_encode($historyProcessModel));
		$this->setData('data', $processMonitoringGrid->toArray());
		
		$this->setView('monitor/index.tpl');
	}
	
	/**
	 * Get JSON monitoring data
	 */
	public function monitorProcess()
	{
		$returnValue = array();
		$filter = null;
		
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
			$filter = $filter == 'null' || empty($filter) ? null : $filter;
		}
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		if(!is_null($filter)){
			$processExecutions = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessService')
				->searchInstances($filter, $processInstancesClass, array ('recursive'=>true));
		}else{
			$processExecutions = $processInstancesClass->getInstances();
		}
		
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_TranslationProcessMonitoringGrid(array_keys($processExecutions));
		$data = $processMonitoringGrid->toArray();
		
		echo json_encode($data);
	}

	/**
	 * Get JSON activity history
	 */
	public function processHistory()
	{
		if($this->hasRequestParameter('uri')){
			$uri = $this->getRequestParameter('uri');
		}
		
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_TranslationExecutionHistoryGrid(new core_kernel_classes_Resource($uri));
		$data = $processMonitoringGrid->toArray();
		
		echo json_encode($data);
	}
}