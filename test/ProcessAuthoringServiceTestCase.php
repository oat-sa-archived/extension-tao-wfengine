<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

class ProcessAuthoringServiceTestCase extends UnitTestCase {
	
	
	protected $authoringService = null;
	protected $proc = null;
	protected $apiModel = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process authoring service');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
	}
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		
		$authoringService = new wfEngine_models_classes_ProcessAuthoringService();
		$this->assertIsA($authoringService, 'tao_models_classes_Service');
		$this->assertIsA($authoringService, 'wfEngine_models_classes_ProcessAuthoringService');

		$this->authoringService = $authoringService;
	}
	
	
	public function testDeleteProcess(){
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('myProcess','created for the unit test of process authoring service');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->assertTrue($this->authoringService->deleteProcess($processDefinition));
			$this->assertTrue($this->apiModel->getSubject(RDFS_LABEL, 'myProcess')->isEmpty());
		}
		
	}
	
	public function testCreateActivity(){
		
			$activity1 = $this->authoringService->createActivity($this->proc);
			$this->assertEqual($activity1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL))->uriResource, GENERIS_TRUE);
			$this->assertEqual($activity1->getLabel(), 'Activity_1');
			$this->assertEqual($activity1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->uriResource, GENERIS_FALSE);
			
			$activity2 = $this->authoringService->createActivity($this->proc, 'myActivity');
			$this->assertEqual($activity2->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL))->uriResource, GENERIS_FALSE);
			
			$activity1->delete();
			$activity2->delete();
		
	}
	
	public function testIsActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc);
		
		$this->assertTrue(wfEngine_models_classes_ProcessAuthoringService::isActivity($activity1));
		
		$activity1->delete();
	}
	
	public function testIsConnector(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$this->assertTrue(wfEngine_models_classes_ProcessAuthoringService::isConnector($connector1));
		
		$activity1->delete();
		$connector1->delete();
	}
	
	public function testCreateConnector(){
		
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES))->uriResource, $activity1->uriResource);
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource, $activity1->uriResource);
		
		//create a connector of a connector:
		$connector2 = $this->authoringService->createConnector($connector1);
		$this->assertEqual($connector2->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource, $activity1->uriResource);
		
		$activity1->delete();
		$connector1->delete();
		$connector2->delete();
	}
	
	public function testAnalyseExpression(){
		
		$conditionDom = $this->authoringService->analyseExpression('(3*(^var +  1) = 2 or ^var > 7)', true);
		$this->assertIsA($conditionDom, 'DOMDocument');
		$isCondition = false;
		foreach ($conditionDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "condition"){
					$isCondition = true;
					break 2;//once is enough...
				
				}
			}
		}
		$this->assertTrue($isCondition);
		
		$assignmentDom = $this->authoringService->analyseExpression('^var = ^var*32 + ^SCR');
		$isAssignment = false;
		foreach ($assignmentDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "then"){
					$isAssignment = true;
					break 2;//stop at the first occurence of course
				}
			}
		}
		$this->assertTrue($isAssignment);
		
	}

	public function testCreateSequenceActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		$followingActivity1 = $this->authoringService->createSequenceActivity($connector1);
		$this->assertIsA($followingActivity1, 'core_kernel_classes_Resource');
		$this->assertEqual($followingActivity1->getLabel(), 'Activity_2');
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->uriResource, INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
		
		// $followingConnector1 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $followingActivity1->uriResource)->get(0);
		$followingConnector1 = $this->authoringService->createConnector($followingActivity1);
		$this->assertIsA($followingConnector1, 'core_kernel_classes_Resource');

		$shouldBeActivity1 = null;
		$shouldBeActivity1 = $this->authoringService->createSequenceActivity($followingConnector1, $activity1);
		$this->assertEqual($activity1->uriResource, $shouldBeActivity1->uriResource);
		
		$shouldBeActivity1 = null;
		$shouldBeActivity1 = $followingConnector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
		$this->assertEqual($activity1->uriResource,$shouldBeActivity1->uriResource);
		
		$activity1->delete();
		$connector1->delete();
		$followingActivity1->delete();
		$followingConnector1->delete();
	}
		
	public function testCreateSplitActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then = $this->authoringService->createSplitActivity($connector1, 'then');//create "Activity_2"
		$else = $this->authoringService->createSplitActivity($connector1, 'else', null, '', true);//create another connector
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->uriResource, INSTANCE_TYPEOFCONNECTORS_SPLIT);
		$this->assertTrue(wfEngine_models_classes_ProcessAuthoringService::isActivity($then));
		$this->assertTrue(wfEngine_models_classes_ProcessAuthoringService::isConnector($else));
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEqual($then->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->uriResource);
		$this->assertEqual($else->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->uriResource);
		
		$activity1->delete();
		$connector1->delete();
		$transitionRule->delete();
		$then->delete();
		$else->delete();
	}
	
	
	public function testDeleteConnectorNextActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		$this->authoringService->createSequenceActivity($connector1, null, '2ndActivityForUnitTest');
		
		$nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		
		$activity2 = $connector1->getUniquePropertyValue($nextActivitiesProp);
		$this->assertIsA($activity2 , 'core_kernel_classes_Resource');
		
		$this->authoringService->deleteConnectorNextActivity($connector1, 'next');
		$followingActivity1 = $connector1->getOnePropertyValue($nextActivitiesProp);
		$this->assertNull($followingActivity1);
		
		// $connector2 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity2->uriResource)->get(0);
		$connector2 = $this->authoringService->createConnector($activity2);
		$then = $this->authoringService->createSplitActivity($connector2, 'then');//create "Activity_2"
		$else = $this->authoringService->createSplitActivity($connector2, 'else', null, '', true);//create another connector
		
		$this->authoringService->deleteConnectorNextActivity($connector2, 'then');
		$this->authoringService->deleteConnectorNextActivity($connector2, 'else');
		
		$transitionRule = $connector2->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		
		$this->assertNull($transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN)));
		$this->assertNull($transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE)));
		$this->assertTrue($this->apiModel->getSubject(RDFS_LABEL, '2ndActivityForUnitTest_c_c')->isEmpty());
		
		$activity1->delete();
		$connector1->delete();
		$activity2->delete();
		$connector2->delete();
		$transitionRule->delete();
		$then->delete();
		$else->delete();
	}
	
	public function testParallelJoinActivities(){
		$activityA = $this->authoringService->createActivity($this->proc, 'A');
		$connectorA = $this->authoringService->createConnector($activityA);
		
		$activityB = $this->authoringService->createSequenceActivity($connectorA, null, 'B');
		// $connectorB = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activityB->uriResource)->get(0);
		$connectorB = $this->authoringService->createConnector($activityB);
		
		//create the parallel branch 'C' acivities and connectors
		$activityC = $this->authoringService->createActivity($this->proc, 'C');
		$connectorC = $this->authoringService->createConnector($activityC);
				
		$activityC1 = $this->authoringService->createSequenceActivity($connectorC, null, 'C1');
		// $connectorC1 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activityC1->uriResource)->get(0);
		$connectorC1 = $this->authoringService->createConnector($activityC1);
		
		$activityC2 = $this->authoringService->createSequenceActivity($connectorC1, null, 'C2');
		// $connectorC2 = $this->apiModel->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activityC2->uriResource)->get(0);
		$connectorC2 = $this->authoringService->createConnector($activityC2);
		
		//create the parallel branch 'D' activities and connectors
		$activityD = $this->authoringService->createActivity($this->proc, 'D');
		$connectorD = $this->authoringService->createConnector($activityD);
		
		//create the merging actvity F
		$activityF = $this->authoringService->createActivity($this->proc, 'F');
		$connectorF = $this->authoringService->createConnector($activityF);
		
		$newActivitiesArray = array(
			$activityC->uriResource => 2,
			$activityD->uriResource => 3
		);
		
		$this->assertTrue($this->authoringService->setParallelActivities($connectorB, $newActivitiesArray));
		// $count = array();
		// foreach($connectorB->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES))->getIterator() as $activity){
		
		// }
		
		//merge all activity D instance to F:
		$this->authoringService->createJoinActivity($connectorD, $activityF, '', $activityD);
		$previousActivitiesCollection = $connectorD->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
		var_dump($connectorD);
		
		$this->assertEqual($previousActivitiesCollection->count(), 3);
		// foreach($previousActivitiesCollection->getIterator() as $previousAc
	}
	
	/*
	public function testCreateJoinActivity(){
		$parallelActivity1 = $this->authoringService->createActivity($this->proc, 'myActivity1');
		$connector1 = $this->authoringService->createConnector($parallelActivity1);
		
		$parallelActivity2 = $this->authoringService->createActivity($this->proc, 'myActivity2');
		$connector2 = $this->authoringService->createConnector($parallelActivity2);
		
		$joinActivity = $this->authoringService->createActivity($this->proc, 'joinActivity');
		
		//join parallel Activity 1 and 2 to "joinActivity"
		$this->assertIsA($this->authoringService->createJoinActivity($connector1, $joinActivity, '', $parallelActivity1), 'core_kernel_classes_Resource');
		$this->authoringService->createJoinActivity($connector2, $joinActivity, '', $parallelActivity2);
		
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
		$this->authoringService->updateJoinedActivity($joinActivity);
		
		//the condition of transition rule of the connector 1 has been modified?
		$newConditionIf = $transitionRule1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
		$this->assertNotEqual($oldConditonIf->uriResource, $newConditionIf->uriResource);
		
		
		$parallelActivity1->delete();
		$connector1->delete();
		$parallelActivity2->delete();
		$connector2->delete();
		$transitionRule1->delete();//TODO test all delete methods:
	}
	*/
	
	public function tearDown() {
        // $this->proc->delete();
    }

}
?>