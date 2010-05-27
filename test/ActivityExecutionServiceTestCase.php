<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 * Test the service wfEngine_models_classes_ActivityExecutionService
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class ActivityExecutionServiceTestCase extends UnitTestCase {
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
	/**
	 * initialize the test 
	 */
	public function setUp(){
		TestRunner::initTest();
	}
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		
		$aeService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$this->assertIsA($aeService, 'tao_models_classes_Service');
		$this->assertIsA($aeService, 'wfEngine_models_classes_ActivityExecutionService');

		$this->service = service;
	}
	
	public function testVirtualProcess(){
		
		$userService = tao_models_classes_ServiceFactory('wfEngine_models_classes_UserService');
		//$testRole = $userService
		
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest', 'unit test');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
		
		$aclModeRole = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
		
		$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
		$authoringService->setFirstActivity($processDefinition, $activity1);
		$this->service->setAcl($activity1, $aclModeRole);
		
		$connectorSeq = new core_kernel_classes_Resource(CONNECTOR_SEQ);
		
		$connector1 = $authoringService->createConnector($activity1);
		$authoringService->setConnectorType($connector1, $connectorSeq);
		$this->assertNotNull($connector1);
		
		$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
		$connector2 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity2->uriResource)->get(0);//the spit connector
		$authoringService->setConnectorType($connector2,$connectorSeq);
		
		$activity3 = $authoringService->createSplitActivity($connector2, null, 'activity3');
		$connector3 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity3->uriResource)->get(0);
		$authoringService->setConnectorType($connector3,$connectorSeq);
		$this->assertNotNull($connector3);
		
		
		$factory = new ProcessExecutionFactory();
		$factory->name = 'Test Process Execution';
		$factory->execution = $processDefinition->uriResource;
		$factory->ownerUri = SYS_USER_LOGIN;
		$proc = $factory->create();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity1');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity2');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity3');
		
		$proc->resource->delete();
		
		//delete processdef:
		$authoringService->deleteProcess($processDefinition);
		
	}
	
}
?>