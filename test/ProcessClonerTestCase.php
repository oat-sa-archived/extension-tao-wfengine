<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessClonerTestCase extends UnitTestCase {
	
	
	protected $processCloner = null;
	protected $authoringService = null;
	protected $activityService = null;
	protected $connectorService = null;	
	protected $proc = null;
	protected $apiModel = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('process of Cloning UnitTest','created for the unit test of process cloner');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		$this->authoringService = new wfEngine_models_classes_ProcessAuthoringService();
		$this->activityService = new wfEngine_models_classes_ActivityService();
		$this->connectorService = new wfEngine_models_classes_ConnectorService();
	}
	
	
	public function testService(){
		
		$processCloner = new wfEngine_models_classes_ProcessCloner();
		$this->assertIsA($processCloner, 'tao_models_classes_GenerisService');
		$this->assertIsA($processCloner, 'wfEngine_models_classes_ProcessCloner');

		$this->processCloner = $processCloner;
	}
	
	
	public function testCloneActivity(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$service1 = $this->authoringService->createInteractiveService($activity1);
		$activity1Clone = $this->processCloner->cloneActivity($activity1);
		
		$propInitial = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
		$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
		$propService = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES);
		
		$this->assertEqual($activity1->getUniquePropertyValue($propInitial)->uriResource, $activity1Clone->getUniquePropertyValue($propInitial)->uriResource);
		$this->assertEqual($activity1->getLabel(), $activity1Clone->getLabel());
		
		$activity1services = $activity1->getPropertyValuesCollection($propService);
		$activity1clonedServices = $activity1Clone->getPropertyValuesCollection($propService);
		$this->assertEqual($activity1services->count(), 1);
		$this->assertEqual($activity1clonedServices->count(), 1);
		
		$clonedService = $activity1clonedServices->get(0);
		
		$this->assertIsA($clonedService, 'core_kernel_classes_Resource');
		$this->assertNotEqual($clonedService->uriResource, $activity1services->get(0)->uriResource);
		
		$this->authoringService->deleteActivity($activity1);
		$this->authoringService->deleteActivity($activity1Clone);
		$this->assertFalse($clonedService->exists());
		$this->assertFalse($service1->exists());
	}
	
	public function testCloneConnector(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		
		$activity1Clone = $this->processCloner->cloneActivity($activity1);
		$activity2Clone = $this->processCloner->cloneActivity($activity2);
		$this->processCloner->addClonedActivity($activity1Clone, $activity1);
		$this->processCloner->addClonedActivity($activity2Clone, $activity2);
		
		//clone it!
		$connector1Clone = $this->processCloner->cloneConnector($connector1);
		
		$this->assertTrue($this->connectorService->isConnector($connector1Clone));
		
		$this->assertIsA($this->processCloner->getClonedConnector($connector1), 'core_kernel_classes_Resource');
		$this->assertEqual($connector1Clone->uriResource, $this->processCloner->getClonedConnector($connector1)->uriResource);
		
		$this->authoringService->deleteActivity($activity1Clone);
		$this->authoringService->deleteActivity($activity2Clone);
		// $this->authoringService->deleteConnector($connector1Clone);
	}

	public function testCloneSequentialProcess(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$connector2 = $this->authoringService->createConnector($activity2);
		$activity3 = $this->authoringService->createSequenceActivity($connector2);
		
		$processClone = $this->processCloner->cloneProcess($this->proc);
		
		$this->assertIsA($processClone, 'core_kernel_classes_Resource');
		$activities = $this->authoringService->getActivitiesByProcess($processClone);
		$this->assertEqual(count($activities), 3);
		foreach($activities as $activity){
			$this->assertTrue($this->activityService->isActivity($activity));
		}
		
		$this->authoringService->deleteProcess($processClone);
	}
	
	
	public function testCloneProcessSegment(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$connector2 = $this->authoringService->createConnector($activity2);
		$activity3 = $this->authoringService->createSequenceActivity($connector2);
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc);
		$this->assertEqual($segmentInterface['in']->getLabel(), $activity1->getLabel());
		$this->assertEqual($segmentInterface['out'][0]->getLabel(), $activity3->getLabel());
		$this->assertEqual(count($this->processCloner->getClonedActivities()), 3);
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc, true);
		
		$this->assertEqual(count($this->processCloner->getClonedActivities()), 5);
		$this->assertEqual($segmentInterface['in']->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->uriResource, GENERIS_TRUE);
		
		// var_dump($this->processCloner);
		
		$this->processCloner->revertCloning();
	}
	
	
	public function testCloneConditionnalProcess(){
		$this->processCloner->initCloningVariables();
		
		$id = "P_condProc7_";//for var_dump identification
		$this->processCloner->setCloneLabel("__Clone7");
		
		$activity1 = $this->authoringService->createActivity($this->proc, "{$id}Activity_1");
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then1 = $this->authoringService->createSplitActivity($connector1, 'then', null, "{$id}Activity_2");//create "Activity_2"
		$else1 = $this->authoringService->createSplitActivity($connector1, 'else', null, '', true);//create another connector
		// $else1 = $this->authoringService->createSplitActivity($connector1, 'else');
		
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->uriResource, INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$this->assertTrue($this->activityService->isActivity($then1));
		$this->assertTrue($this->connectorService->isConnector($else1));
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEqual($then1->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->uriResource);
		$this->assertEqual($else1->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->uriResource);
		
		//create a sequential a
		$connector2 = $this->authoringService->createConnector($then1);
		$lastActivity = $this->authoringService->createSequenceActivity($connector2, null, "{$id}Activity_3");
		
		//connector "else1": connect the "then" to the activity "then1" and the "else" to 
		$then2 = $this->authoringService->createSplitActivity($else1, 'then', $connector2);//connect to the activity $then1
		$else2 = $this->authoringService->createSplitActivity($else1, 'else', $lastActivity);//connect to the connector of the activity $then1
		$this->assertEqual($then2->uriResource, $connector2->uriResource);
		$this->assertEqual($else2->uriResource, $lastActivity->uriResource);
		
		
		//clone the process now!
		$processClone = $this->processCloner->cloneProcess($this->proc);
		
		
		$this->assertIsA($processClone, 'core_kernel_classes_Resource');
		$this->assertEqual(count($this->processCloner->getClonedActivities()), 3);
		$this->assertEqual(count($this->processCloner->getClonedConnectors()), 3);
		
		//count the number of activities in the cloned process
		$activities = $this->authoringService->getActivitiesByProcess($processClone);
		$this->assertEqual(count($activities), 3);
		foreach($activities as $activity){
			$this->assertTrue($this->activityService->isActivity($activity));
		}
		
		$this->authoringService->deleteProcess($processClone);
	}
	
	
	public function testCloneConditionnalProcessSegment(){
		$this->processCloner->initCloningVariables();
		
		$id = "P_condSeg_";//for var_dump identification
		$this->processCloner->setCloneLabel("__Clone3");
		
		$activity1 = $this->authoringService->createActivity($this->proc, "{$id}Activity_1");
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then1 = $this->authoringService->createSplitActivity($connector1, 'then', null, "{$id}Activity_2");//create "Activity_2"
		$else1 = $this->authoringService->createSplitActivity($connector1, 'else', null, '', true);//create another connector
		// $else1 = $this->authoringService->createSplitActivity($connector1, 'else');
		
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->uriResource, INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$this->assertTrue($this->activityService->isActivity($then1));
		$this->assertTrue($this->connectorService->isConnector($else1));
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEqual($then1->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->uriResource);
		$this->assertEqual($else1->uriResource, $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->uriResource);
		
		//create a sequential a
		$connector2 = $this->authoringService->createConnector($then1);
		$lastActivity = $this->authoringService->createSequenceActivity($connector2, null, "{$id}Activity_3");
		
		//connector "else1": connect the "then" to the activity "then1" and the "else" to 
		$then2 = $this->authoringService->createSplitActivity($else1, 'then', $connector2);//connect to the activity $then1
		$else2 = $this->authoringService->createSplitActivity($else1, 'else', $lastActivity);//connect to the connector of the activity $then1
		$this->assertEqual($then2->uriResource, $connector2->uriResource);
		$this->assertEqual($else2->uriResource, $lastActivity->uriResource);
		
		// var_dump($this->processCloner);
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc);
		$this->assertEqual($segmentInterface['in']->getLabel(), $activity1->getLabel().$this->processCloner->getCloneLabel());
		// $this->assertEqual($segmentInterface['out'][0]->getLabel(), $activity3->getLabel());
		$this->assertEqual(count($this->processCloner->getClonedActivities()), 3);
//		var_dump($segmentInterface, $segmentInterface['in']->getLabel(), $segmentInterface['out'][0]->getLabel());
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc, true);
		$this->assertEqual(count($this->processCloner->getClonedActivities()), 5);
		$this->assertEqual($segmentInterface['in']->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->uriResource, GENERIS_TRUE);
		// var_dump($segmentInterface);
		
		$this->processCloner->revertCloning();
	}
	/**/
	
	public function tearDown() {
       $this->authoringService->deleteProcess($this->proc);
    }

}
?>