<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the service wfEngine_models_classes_InteractiveServiceService
 *
 * @author Lionel Lecaque, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */

class InteractiveServiceServiceTestCase extends UnitTestCase {

    protected $service = null;
	protected $authoringService = null;
	protected $processDefinition = null;
	protected $activity = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $this->processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');

        //define activities and connectors
        $activity = $this->authoringService->createActivity($this->processDefinition, 'activity for interactive service unit test');
		if($activity instanceof core_kernel_classes_Resource){
			$this->activity = $activity;
		}else{
			$this->fail('fail to create a process definition resource');
		}
	}
	
	public function tearDown() {
		$this->assertTrue($this->authoringService->deleteProcess($this->processDefinition));
    }

    /**
     * Test the service implementation
     */
    public function testService(){

        $aService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_InteractiveServiceService');
        $this->assertIsA($aService, 'tao_models_classes_Service');
        $this->assertIsA($aService, 'wfEngine_models_classes_InteractiveServiceService');

        $this->service = $aService;
    }

    public function testIsInteractiveService(){
        $service1 = $this->authoringService->createInteractiveService($this->activity);
        $this->assertTrue($this->service->isInteractiveService($service1));
    }
	
	public function testGetCallUrl(){
		$myProcessVar1 = $this->authoringService->getProcessVariable('myProcessVarCode1', true);
		$myProcessVar2 = $this->authoringService->getProcessVariable('myProcessVarCode2', true);
		
		$inputParameters = array(
			'param1' => $myProcessVar1,
			'param2' => '^myProcessVarCode2',
			'param3' => 'myConstantValue',
			'param4' => null
		);
		
		$serviceUrl = 'http://www.myWebSite.com/myServiceScript.php';
		
		$serviceDefinition1 = $this->authoringService->createServiceDefinition('myServiceDefinition', $serviceUrl, $inputParameters);
		$this->assertNotNull($serviceDefinition1);
		
		$service1 = $this->authoringService->createInteractiveService($this->activity);
		$this->assertTrue($this->service->isInteractiveService($service1));
		$this->assertTrue($this->authoringService->setCallOfServiceDefinition($service1, $serviceDefinition1));
		
		//check call url
		$callUrl = $this->service->getCallUrl($service1);
		$this->assertEqual($callUrl, 'http://www.myWebSite.com/myServiceScript.php?');
		
		//assign actual params:
		for($i=1;$i<=4;$i++){
			$formalParam = $this->authoringService->getFormalParameter('param'.$i);
			$this->assertNotNull($formalParam);
			if(!is_null($formalParam) && $formalParam instanceof core_kernel_classes_Resource){
				$this->assertTrue($this->authoringService->setActualParameter($service1, $formalParam, 'value'.$i));
			}
		}
		
		//a no-orthodox way to create a valid token-activity execution pair:
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$currentUser = new core_kernel_classes_Resource(LOCAL_NAMESPACE.'#unitTestUser');
		$this->assertNotNull($currentUser);
		
		$classActivityExecution = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$activityExec1 = $classActivityExecution->createInstance('activity exec for interactive service test case');
		$activityExec1->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER), $currentUser->uriResource);
		
		$classToken = new core_kernel_classes_Class(CLASS_TOKEN);
		$token1 = $classToken->createInstance('token for interactive service test case');
		$token1->setPropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION), $activityExec1->uriResource);
		$token1->setPropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_CURRENTUSER), $currentUser->uriResource);
		$token1->setPropertyValue(new core_kernel_classes_Property($myProcessVar1->uriResource), 'val1');
		$token1->setPropertyValue(new core_kernel_classes_Property($myProcessVar2->uriResource), 'val2');
		
		//check call url again
		$callUrl = $this->service->getCallUrl($service1);
		$this->assertEqual(strlen($callUrl), strlen('http://www.myWebSite.com/myServiceScript.php?param2=&param1=&param3=&param4=&'));
		
		$callUrl = $this->service->getCallUrl($service1, $activityExec1);
		$this->assertEqual(strlen($callUrl), strlen('http://www.myWebSite.com/myServiceScript.php?param2=value2&param1=value1&param3=value3&param4=value4&'));
		
		$myProcessVar1->delete();
		$myProcessVar2->delete();
		$serviceDefinition1->delete();
		for($i=1;$i<=4;$i++){
			$formalParam = $this->authoringService->getFormalParameter('param'.$i);
			$this->assertNotNull($formalParam);
			if(!is_null($formalParam) && $formalParam instanceof core_kernel_classes_Resource){
				$this->assertTrue($formalParam->delete());
			}
		}
	}
	
	public function testGetStyle(){
		
		$service1 = $this->authoringService->createInteractiveService($this->activity);
		$this->assertTrue($this->service->isInteractiveService($service1));
		
		$style = $this->service->getStyle($service1);
		$this->assertEqual($style, 'position:absolute;left:0%;top:0%;width:100%;height:100%;');
		
		//set default position and size value:
		$service1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH), 5);
		$service1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT), 10);
		$service1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP), 30);
		$service1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT), 50);
		
		$style = $this->service->getStyle($service1);
		$this->assertEqual($style, 'position:absolute;left:50%;top:30%;width:5%;height:10%;');
		
	}

}
