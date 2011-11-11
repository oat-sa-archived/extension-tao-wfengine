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
	 * 
	 */
	public function index()
	{
		$clazz = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$properties = array();
		
		//get properties to filter on
		/*
		if($this->hasRequestParameter('properties')){
			$properties = $this->getRequestParameter('properties');
		}else{
			$properties = tao_helpers_form_GenerisFormFactory::getClassProperties($clazz);
		}
		*/

		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$properties[] = new core_kernel_classes_Property(LOCAL_NAMESPACE."#countryCode");
		$properties[] = new core_kernel_classes_Property(LOCAL_NAMESPACE."#languageCode");
		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		//$processExecutions = $processInstancesClass->getInstances(array('limit'=>'1'));
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_TranslationProcessMonitoringGrid(/*array_keys($processExecutions)*/);
		//$processMonitoringGrid->toArray();
		$grid = $processMonitoringGrid->getGrid();
		$columns = $grid->getColumns();
		$model = $grid->getColumnsModel();
		//echo'<pre>';print_r($processMonitoringGrid->toArray());echo'</pre>';
		
		$processHistoryGrid = new wfEngine_helpers_Monitoring_TranslationExecutionHistoryGrid(new core_kernel_classes_Resource('yeah'));
		$historyProcessModel = $processHistoryGrid->getGrid()->getColumnsModel();
		
		$this->setData('clazz', $clazz);
		$this->setData('properties', $properties);	
		//$this->setData('processExecutions', $processExecutions);
		$this->setData('columns', $columns);
		foreach($model as $key=>$elt){
			$model[$key]['weight'] = 1;
			$model[$key]['widget'] = "Label";
		}
		
		$model['http://www.w3.org/2000/01/rdf-schema#label']['weight'] = 3;
		$model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesExecutionOf']['weight'] = 2;
		$model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']['weight'] = 4;
		$model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']['widget'] = "CurrentActivities";
		
		$this->setData('model', json_encode($model));
		$this->setData('historyProcessModel', json_encode($historyProcessModel));
		$this->setData('data', $processMonitoringGrid->toArray());
		$this->setView('monitor/index.tpl');
	}
	
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