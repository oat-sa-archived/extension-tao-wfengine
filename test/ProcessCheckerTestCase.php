<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessCheckerTestCase extends UnitTestCase {
	
	protected $authoringService = null;
	protected $proc = null;
	
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('process of Checker UnitTest','created for the unit test of process cloner');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}
		$this->authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
	}
	
	public function testInitialActivity(){
	
		$activity1 = $this->authoringService->createActivity($this->proc);
		
		$processChecker = new wfEngine_models_classes_ProcessChecker($this->proc);
		$this->assertTrue($processChecker->checkInitialActivity());
		
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
		$this->assertFalse($processChecker->checkInitialActivity());
		
	}
	
	public function testIsolatedConnector(){
		
		$processChecker = new wfEngine_models_classes_ProcessChecker($this->proc);
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->assertTrue($processChecker->checkNoIsolatedConnector());
		
		$connector1 = $this->authoringService->createConnector($activity1);
		$this->assertFalse($processChecker->checkNoIsolatedConnector());
		
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$this->assertTrue($processChecker->checkNoIsolatedConnector());
	}
	
	public function testIsolatedActivity(){
		
		$processChecker = new wfEngine_models_classes_ProcessChecker($this->proc);
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$this->assertTrue($processChecker->checkNoIsolatedActivity());
		
		$this->authoringService->deleteConnector($connector1);
		$this->assertFalse($processChecker->checkNoIsolatedActivity());
	}
	
	public function testCheckProcess(){
		$id= '_unit_pr_check_';
		$processChecker = new wfEngine_models_classes_ProcessChecker($this->proc);
		
		$activity1 = $this->authoringService->createActivity($this->proc, "{$id}Activity_1");
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then1 = $this->authoringService->createConditionalActivity($connector1, 'then', null, "{$id}Activity_2");//create "Activity_2"
		$else1 = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
		
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->uriResource, INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$activityService = new wfEngine_models_classes_ActivityService();
		$this->assertTrue($activityService->isActivity($then1));
		$connectorService =  new wfEngine_models_classes_ConnectorService();
		$this->assertTrue($connectorService->isConnector($else1));
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEqual($then1->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->uriResource);
		$this->assertEqual($else1->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->uriResource);
		
		//create a sequential a
		$connector2 = $this->authoringService->createConnector($then1);
		$lastActivity = $this->authoringService->createSequenceActivity($connector2, null, "{$id}Activity_3");
		
		//connector "else1": connect the "then" to the activity "then1" and the "else" to 
		$then2 = $this->authoringService->createConditionalActivity($else1, 'then', $connector2);//connect to the activity $then1
		$else2 = $this->authoringService->createConditionalActivity($else1, 'else', $lastActivity);//connect to the connector of the activity $then1
		$this->assertEqual($then2->uriResource, $connector2->uriResource);
		$this->assertEqual($else2->uriResource, $lastActivity->uriResource);
		
		$this->assertTrue($processChecker->check());
	}
		
	public function tearDown() {
       $this->authoringService->deleteProcess($this->proc);
    }

}
?>