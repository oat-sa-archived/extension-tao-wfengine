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
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process authoring service');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}
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
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$process = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process authoring service');
		if($process instanceof core_kernel_classes_Resource){
			$activity1 = $this->authoringService->createActivity($process);
			$this->assertEqual($activity1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL))->uriResource, GENERIS_TRUE);
			$this->assertEqual($activity1->getLabel(), 'Activity_1');
			$this->assertEqual($activity1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->uriResource, GENERIS_FALSE);
			
			$activity2 = $this->authoringService->createActivity($process, 'myActivity');
			$this->assertEqual($activity2->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL))->uriResource, GENERIS_FALSE);
			
			$activity1->delete();
			$activity2->delete();
			$process->delete();
		}
		
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
	
	 function tearDown() {
        $this->proc->delete();
    }

}
?>