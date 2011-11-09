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
		$processExecutions = $processInstancesClass->getInstances();
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_TranslationProcessMonitoringGrid();
		$grid = $processMonitoringGrid->getGrid();
		$columns = $grid->getColumns();
		
		$this->setData('clazz', $clazz);
		$this->setData('properties', $properties);	
		$this->setData('processExecutions', $processExecutions);
		$this->setData('columns', $columns);
		$this->setData('data', $processMonitoringGrid->toArray());
		$this->setView('monitor/index.tpl');
	}
	
	public function monitorProcess()
	{
		$returnValue = array();
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$processExecutions = $processInstancesClass->getInstances();
		
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_TranslationProcessMonitoringGrid(array_keys($processExecutions));
		
		$grid = $processMonitoringGrid->getGrid();
		$columns = $grid->getColumns();
		$toArray = $processMonitoringGrid->toArray();
		
		echo json_encode($toArray);
		return;
		
		//var_dump($toArray);
		/*foreach($toArray as $elt){
			$returnValue[] = $elt;
		}
		var_dump($returnValue);*/
		//echo json_encode($returnValue);
		$test = array(
			"page"		=> 1
			, "total"	=> 1
			, "records"	=> 10
			, "rows" 	=> array()
		);
		$i=0;
		foreach($toArray as $process){
			foreach($process as $propUri=>$propValue){
				$test['rows'][$i][$propUri] = $propValue;
			}
			$i++;
		} 
		echo json_encode ((Object)$test);
	}
	
}
/*{
   "page":"1",
   "total":2,
   "records":"13", 
   "rows":[ 
      {"id":"12345","name":"Desktop Computers","email":"josh@josh.com","item":{"price":"1000.72", "weight": "1.22" }, "note": "note", "stock": "0","ship": "1"}, 
      {"id":"23456","name":"<var>laptop</var>","note":"Long text ","stock":"yes","item":{"price":"56.72", "weight":"1.22"},"ship": "2"},
      {"id":"34567","name":"LCD Monitor","note":"note3","stock":"true","item":{"price":"99999.72", "weight":"1.22"},"ship":"3"},
      {"id":"45678","name":"Speakers","note":"note","stock":"false","ship":"4"} 
    ] 
}*/