<?php
require_once dirname(__FILE__) . '/wfEngineServiceTest.php';

/**
 * Test the execution of a complex translation process
 * 
 * @author Somsack Sipasseuth, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class MonitoringTestCase extends wfEngineServiceTest {
	
	public function testCreateProcessMonitoringGrid(){
		
		//wfEngine_helpers_Monitoring_ProcessMonitoringGrid
		//wfEngine_helpers_Monitoring_TranslationProcessMonitoringGrid
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$processExecutions = $processInstancesClass->getInstances();
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_ProcessMonitoringGrid(array_keys($processExecutions));
		var_dump($processExecutions);
		var_dump($processMonitoringGrid->toArray());
		
	}
	
}
?>
