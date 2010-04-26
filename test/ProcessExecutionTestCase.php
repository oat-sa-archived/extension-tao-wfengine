<?php
require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
require_once dirname(__FILE__) . '/../includes/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

if(!defined("LOGIN")){
	define("LOGIN", "generis", true);
}
/**
* @constant password for the module you wish to connect to 
*/
if(!defined("PASS")){
	define("PASS", "g3n3r1s", true);
}
/**
* @constant module for the module you wish to connect to 
*/
if(!defined("MODULE")){
	define("MODULE", "tao", true);
}

error_reporting(E_ALL);

class ProcessExecutionTestCase extends UnitTestCase{
	
	protected $procDefinition = null;
	protected $apiModel = null;
	
	public function setUp(){
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		$this->apiModel->logIn(LOGIN,md5(PASS),DATABASE_NAME,true);
		
		/*
		
		$factory = new ProcessExecutionFactory();
		$factory->name = 'Test Process Execution';
		$factory->execution = 'http://www.tao.lu/middleware/Interview.rdf#i126537966613798';
		
		
		$factory->ownerUri = LOGIN;

		$this->proc = $factory->create();
*/

	}
	
	public function testCreateProcess(){
	
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process execution');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
		
		$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
		$authoringService->setFirstActivity($processDefinition,$activity1);
		$connector1 = $authoringService->createConnector($activity1);
		$connectorSeq = new core_kernel_classes_Resource(CONNECTOR_SEQ);
		$connectorSplit = new core_kernel_classes_Resource(CONNECTOR_SPLIT);
		
		$authoringService->setConnectorType($connector1,$connectorSeq);
		$this->assertNotNull($connector1);
		
		$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
		$connector2 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity2->uriResource)->get(0);//the spit connector
		$authoringService->setConnectorType($connector2,$connectorSplit);
		$authoringService->createRule($connector2, '^groupUri = 1');
		
		$activity3 = $authoringService->createSplitActivity($connector2, 'then', null, 'activity3');
		$connector3 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity3->uriResource)->get(0);
		$authoringService->setConnectorType($connector3,$connectorSeq);
		$this->assertNotNull($connector3);
		
		$activity4 = $authoringService->createSplitActivity($connector2, 'else', null, 'activity4');
		$connector4 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity4->uriResource)->get(0);
		$authoringService->setConnectorType($connector4,$connectorSeq);
		
		$activity5 = $authoringService->createActivity($processDefinition, 'activity5');
		//connect activity 3 and 4 to the 5th:
		$act5bis = $authoringService->createSequenceActivity($connector3, $activity5);
		$act5ter = $authoringService->createSequenceActivity($connector4, $activity5);
		$this->assertEqual($connector3->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES))->uriResource, $activity5->uriResource);
		$this->assertEqual($connector3->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES))->uriResource, $connector4->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES))->uriResource);
		
		
		$factory = new ProcessExecutionFactory();
		$factory->name = 'Test Process Execution';
		$factory->execution = $processDefinition->uriResource;
		$factory->ownerUri = LOGIN;
		$proc = $factory->create();
		$procVar = $authoringService->getProcessVariable('groupUri');
		$this->assertNotNull($procVar);
		$proc->resource->setPropertyValue(new core_kernel_classes_Property($procVar->uriResource), '1');

		 var_dump($proc->currentActivity[0]);
		 $activity = $proc->currentActivity[0];
//		 var_dump($activity->getServices());

		 $proc->performTransition();
		
		 var_dump($proc->currentActivity[0]);
		 $activity = $proc->currentActivity[0];

		 $proc->performTransition();
	
		 var_dump($proc->currentActivity[0]);
		 $activity = $proc->currentActivity[0];
//		 var_dump($activity->getServices());

		$proc->performTransition();
	
		 var_dump($proc->currentActivity[0]);
		 $activity = $proc->currentActivity[0];

		 $this->fail('not imp yet');
		
		//delete processdef:
		$authoringService->deleteProcess($processDefinition);
		
	}
	/*
	public function testPerformTransition(){
		
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$process = new core_kernel_classes_Class(CLASS_PROCESS);
		$this->procDefinition = core_kernel_classes_ResourceFactory::create($process,'WfEngine unit test', 'test');
		$activity1 = $authoringService->createActivity($this->procDefinition, 'testPerformTransition Activity 1');
		$authoringService->setFirstActivity($this->procDefinition,$activity1);
		$connectorSeq = new core_kernel_classes_Resource(CONNECTOR_SEQ);
		$connectorSplit = new core_kernel_classes_Resource(CONNECTOR_SPLIT);
		
		$seaConnector1 = $authoringService->createConnector($activity1);
		$authoringService->setConnectorType($seaConnector1,$connectorSeq);

		
		$activity2 = $authoringService->createSequenceActivity($seaConnector1);
		
		$seaConnector2 = $authoringService->createConnector($activity2);

		echo __FILE__.__LINE__;var_dump($seaConnector2,$connectorSplit);
		$authoringService->setConnectorType($seaConnector2,$connectorSplit);
//
//				
//		$rule = $authoringService->createRule($seaConnector2,'^var = 1');
//		$condition = $authoringService->createCondition($conditionDom);
//
//		$activity3 = $authoringService->createSplitActivity($seaConnector2, 'then');
//		$seaConnector3 = $authoringService->createConnector($activity3);
//		$authoringService->setConnectorType($seaConnector3,$connectorSeq);
//
//		
//		$activity4 = $authoringService->createSplitActivity($seaConnector2, 'else');
//		$seaConnector4 = $authoringService->createConnector($activity4);
//		$authoringService->setConnectorType($seaConnector4,$connectorSeq);
//		
//		$activity5 = $authoringService->createSequenceActivity($seaConnector3);
//		$activity5 = $authoringService->createSequenceActivity($seaConnector4,$activity5);

		

		$factory = new ProcessExecutionFactory();
		$factory->name = 'Test Process Execution';
		$factory->execution = urldecode('http%3A%2F%2Flocalhost%2Fmiddleware%2Fzzz.rdf%23i1272033936038083200');
//		$factory->execution =$this->procDefinition->uriResource;
		$factory->ownerUri = LOGIN;

		$proc = $factory->create();

//		var_dump($proc);
		var_dump($proc->currentActivity[0]);
		$activity = $proc->currentActivity[0];
//		var_dump($activity->getServices());

		$proc->performTransition();
		
		var_dump($proc->currentActivity[0]);
		$activity = $proc->currentActivity[0];
		$proc->performTransition();
		
		var_dump($proc->currentActivity[0]);
		$activity = $proc->currentActivity[0];
//		var_dump($activity->getServices());

		
		$this->fail('not imp yet');
		
	}
	*/

	
}

?>