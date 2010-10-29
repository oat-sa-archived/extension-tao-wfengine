<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

class ProcessAuthoringServiceTestCase extends UnitTestCase {
	
	
	protected $processCloner = null;
	protected $authoringService = null;
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
	}
	
	
	public function testService(){
		
		$processCloner = new wfEngine_models_classes_ProcessCloner();
		$this->assertIsA($processCloner, 'tao_models_classes_GenerisService');
		$this->assertIsA($processCloner, 'wfEngine_models_classes_ProcessCloner');

		$this->processCloner = $processCloner;
	}
	/*
	public function testCloneActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
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
	}
	
	public function testCloneConnector(){
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		
		$activity1Clone = $this->processCloner->cloneActivity($activity1);
		$activity2Clone = $this->processCloner->cloneActivity($activity2);
		$this->processCloner->addClonedActivity($activity1, $activity1Clone);
		$this->processCloner->addClonedActivity($activity2, $activity2Clone);
		
		//clone it!
		$connector1Clone = $this->processCloner->cloneConnector($connector1);
		
		// var_dump($this->processCloner);
		// var_dump($connector1Clone);
		
		$this->assertTrue(wfEngine_helpers_ProcessUtil::isConnector($connector1Clone));
		
		$this->assertIsA($this->processCloner->getClonedConnector($connector1), 'core_kernel_classes_Resource');
		$this->assertEqual($connector1Clone->uriResource, $this->processCloner->getClonedConnector($connector1)->uriResource);
		
		$this->authoringService->deleteActivity($activity1Clone);
		$this->authoringService->deleteActivity($activity2Clone);
		// $this->authoringService->deleteConnector($connector1Clone);
	}
	
	public function testCloneSequentialProcess(){
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$connector2 = $this->authoringService->createConnector($activity2);
		$activity3 = $this->authoringService->createSequenceActivity($connector2);
		
		$processClone = $this->processCloner->cloneProcess($this->proc);
		
		// var_dump($this->processCloner);
		
		$this->assertIsA($processClone, 'core_kernel_classes_Resource');
		$activities = $this->authoringService->getActivitiesByProcess($processClone);
		$this->assertEqual(count($activities), 3);
		foreach($activities as $activity){
			$this->assertTrue(wfEngine_helpers_ProcessUtil::isActivity($activity));
		}
		
		$this->authoringService->deleteProcess($processClone);
	}*/
	
	public function testCloneProcessSegment(){
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
		var_dump($this->processCloner, $segmentInterface);
		$this->assertEqual(count($this->processCloner->getClonedActivities()), 5);
		$this->assertEqual($segmentInterface['in']->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->uriResource, GENERIS_TRUE);
		
		$this->processCloner->revertCloning();
	}
	
	public function testCloneConditionnnalProcess(){
	
	}
	
	public function tearDown() {
       $this->authoringService->deleteProcess($this->proc);
    }

}
?>