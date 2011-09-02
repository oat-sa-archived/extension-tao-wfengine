<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessDefinitionServiceTestCase extends UnitTestCase {
	
	
	protected $service = null;
	protected $authoringService = null;
	protected $processDefinition = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process definition service');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->processDefinition = $processDefinition;
		}else{
			$this->fail('fail to create a process definition resource');
		}
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
	}
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		
		$service = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessDefinitionService');
		
		$this->assertIsA($service, 'tao_models_classes_Service');
		$this->assertIsA($service, 'wfEngine_models_classes_ProcessDefinitionService');

		$this->service = $service;
		
	}
	
	public function testGetRootActivities(){
		$activity1 = $this->authoringService->createActivity($this->processDefinition);
		$activity2 = $this->authoringService->createActivity($this->processDefinition);
		
		$rootActivities = $this->service->getRootActivities($this->processDefinition);
		$this->assertEqual(count($rootActivities), 1);
		$this->assertEqual($rootActivities[0]->uriResource, $activity1->uriResource);
	}
	
	public function testGetAllActivities(){
		$activity1 = $this->authoringService->createActivity($this->processDefinition);
		$activity2 = $this->authoringService->createActivity($this->processDefinition);
		$activity3 = $this->authoringService->createActivity($this->processDefinition);
		
		$allActivities = $this->service->getAllActivities($this->processDefinition);
		$this->assertEqual(count($allActivities), 3);
		
		foreach($allActivities as $activity){
			$this->assertTrue(in_array($activity->uriResource, array($activity1->uriResource, $activity2->uriResource, $activity3->uriResource)));
		}
	}
	
	public function testGetProcessVars(){
		
		$processVars = $this->service->getProcessVars($this->processDefinition);
		$this->assertEqual(count($processVars), 1);
		
		$myProcessVarName1 = 'myProcDefVarName1';
		$myProcessVar1 = $this->authoringService->getProcessVariable($myProcessVarName1, true);
		$this->service->setProcessVariable($this->processDefinition, $myProcessVarName1);
		//this works too: $this->service->setProcessVariable($this->processDefinition, $myProcessVar1);
		
		$processVars = $this->service->getProcessVars($this->processDefinition);
		$this->assertEqual(count($processVars), 2);
		$this->assertTrue(isset($processVars[$myProcessVar1->uriResource]));
		$secondProcessVar = $processVars[$myProcessVar1->uriResource];
		
		$this->assertEqual($secondProcessVar['name'], $myProcessVarName1);
		
		$myProcessVar1->delete();
	}
	
	public function tearDown() {
		$this->assertTrue($this->authoringService->deleteProcess($this->processDefinition));
    }

}
?>