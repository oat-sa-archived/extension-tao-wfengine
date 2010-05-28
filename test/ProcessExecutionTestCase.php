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
	

	protected $apiModel = null;
	
	public function setUp(){
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		$this->apiModel->logIn(LOGIN,md5(PASS),DATABASE_NAME,true);
		

	}
	
	/*
	public function testCreateSplitProcess(){
	
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

		$this->assertTrue($proc->currentActivity[0]->label == 'activity1');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity2');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity3');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity5');
		$proc->resource->delete();
		
		$proc = $factory->create();
		$this->assertNotNull($procVar);
		$proc->resource->setPropertyValue(new core_kernel_classes_Property($procVar->uriResource), '12');
		$this->assertTrue($proc->currentActivity[0]->label == 'activity1');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity2');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity4');
		$proc->performTransition();
		$this->assertTrue($proc->currentActivity[0]->label == 'activity5');
		
		$proc->resource->delete();
			
		//delete processdef:
		$authoringService->deleteProcess($processDefinition);
		
	}*/
	
	public function testCreateJoinProcess(){
		
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest_' . date(DATE_ISO8601),'created for the unit test of process execution');
		
		$activity0 = $authoringService->createActivity($processDefinition, 'activity0');
		$authoringService->setFirstActivity($processDefinition,$activity0);
		$connector0 = $authoringService->createConnector($activity0);
		
		$connectorParallele = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL);
		$authoringService->setConnectorType($connector0,$connectorParallele);
		
		
		$parallelActivity1 = $authoringService->createActivity($processDefinition, 'myActivity1');
		$connector1 = $authoringService->createConnector($parallelActivity1);

		$parallelActivity2 = $authoringService->createActivity($processDefinition, 'myActivity2');
		$connector2 = $authoringService->createConnector($parallelActivity2);
		
		$nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		
		$connector0->setPropertyValue($nextActivitiesProp,$parallelActivity1->uriResource);
		$connector0->setPropertyValue($nextActivitiesProp,$parallelActivity2->uriResource);
		
		$joinActivity = $authoringService->createActivity($processDefinition, 'joinActivity');
		
		//join parallel Activity 1 and 2 to "joinActivity"
		$this->assertIsA($authoringService->createJoinActivity($connector1, $joinActivity, '', $parallelActivity1), 'core_kernel_classes_Resource');
		$authoringService->createJoinActivity($connector2, $joinActivity, '', $parallelActivity2);
		
		//both connectors joined to the same activity have the same transition rule?
		$propTransitionRule = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE);
		$transitionRule1 = $connector1->getUniquePropertyValue($propTransitionRule);
		$this->assertIsA($transitionRule1, 'core_kernel_classes_Resource');
		$transitionRule2 = $connector2->getUniquePropertyValue($propTransitionRule);
		$this->assertEqual($transitionRule1->uriResource, $transitionRule2->uriResource);

		//same activity in 'then' property?
		$propThen = new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN);
		$this->assertEqual($transitionRule1->getUniquePropertyValue($propThen)->uriResource, $joinActivity->uriResource);
		
		//test update of the joined activity after a connector has been disonnected from it:
		$oldConditonIf = $transitionRule1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
		$connector2->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
		$authoringService->updateJoinedActivity($joinActivity);
		
		//the condition of transition rule of the connector 1 has been modified?
		$newConditionIf = $transitionRule1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
		$this->assertNotEqual($oldConditonIf->uriResource, $newConditionIf->uriResource);
		// var_dump($transitionRule1, $transitionRule1bis);
		
		
		$factory = new ProcessExecutionFactory();
		$factory->name = 'Test Process Execution Parallele';
		$factory->execution = $processDefinition->uriResource;
		$factory->ownerUri = LOGIN;

		$proc = $factory->create();
		$proc->performTransition();
		var_dump($proc->currentActivity);

		$parallelActivity1->delete();
		$connector1->delete();
		$parallelActivity2->delete();
		$connector2->delete();
		$transitionRule1->delete();//TODO test all delete methods:
		
		$proc->resource->delete();
			
		//delete processdef:
		$authoringService->deleteProcess($processDefinition);
	}
	
}

?>