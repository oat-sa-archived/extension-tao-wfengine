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
		
		$this->authoringService = wfEngine_models_classes_ProcessAuthoringService::singleton();
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

        $aService = wfEngine_models_classes_InteractiveServiceService::singleton();
        $this->assertIsA($aService, 'tao_models_classes_Service');
        $this->assertIsA($aService, 'wfEngine_models_classes_InteractiveServiceService');

        $this->service = $aService;
    }

    public function testIsInteractiveService(){
		
        $service1 = $this->authoringService->createInteractiveService($this->activity);
        $this->assertTrue($this->service->isInteractiveService($service1));
        $this->assertFalse($this->service->isInteractiveService($this->processDefinition));	
        $service1->delete(); 
		
    }
	
	public function testGetCallUrl(){
		
		//create unique process variables for this unit test only:
		$variableService = wfEngine_models_classes_VariableService::singleton();
		$myProcessVarCode1 = 'myProcessVarCode1'.time();
		$myProcessVarCode2 = 'myProcessVarCode2'.time();
		$myProcessVar1 = $variableService->getProcessVariable($myProcessVarCode1, true);
		$myProcessVar2 = $variableService->getProcessVariable($myProcessVarCode2, true);
		
		
		$parameterNames = array(
			'param1'.time(),
			'param2'.time(),
			'param3'.time(),
			'param4'.time()
		);
		$inputParameters = array(
			$parameterNames[0] => $myProcessVar1,
			$parameterNames[1] => '^'.$myProcessVarCode2,
			$parameterNames[2] => 'myConstantValue',
			$parameterNames[3] => null
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
			$formalParam = $this->authoringService->getFormalParameter($parameterNames[$i-1]);
			$this->assertNotNull($formalParam);
			if(!is_null($formalParam) && $formalParam instanceof core_kernel_classes_Resource){
				$defaultProcessVar = $formalParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE));
				if(!is_null($defaultProcessVar)){
					$this->assertTrue($this->authoringService->setActualParameter($service1, $formalParam, $defaultProcessVar->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE));
				}else{
					$this->assertTrue($this->authoringService->setActualParameter($service1, $formalParam, 'value'.$i));
				}
			}
		}
		
		//a no-orthodox way to create a valid activity execution :
		$userService = wfEngine_models_classes_UserService::singleton();
		$currentUser = new core_kernel_classes_Resource(LOCAL_NAMESPACE.'#unitTestUser');
		$this->assertNotNull($currentUser);
		
		$classActivityExecution = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$activityExec1 = $classActivityExecution->createInstance('activity exec for interactive service test case');
		$activityExec1->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER), $currentUser->uriResource);
		
		$procVarValue1 = 'procVarValue1';
		$procVarValue2 = 'procVarValue2';
		$activityExec1->setPropertyValue(new core_kernel_classes_Property($myProcessVar1->uriResource), $procVarValue1);
		$activityExec1->setPropertyValue(new core_kernel_classes_Property($myProcessVar2->uriResource), $procVarValue2);
		
		//check call url again
		$callUrl = $this->service->getCallUrl($service1);
		$this->assertEqual(strlen($callUrl), strlen('http://www.myWebSite.com/myServiceScript.php?'.$parameterNames[0].'=&'.$parameterNames[1].'=&'.$parameterNames[2].'=&'.$parameterNames[3].'=&'));
		
		//and again:
		$callUrl = $this->service->getCallUrl($service1, $activityExec1);
		$this->assertEqual(strlen($callUrl), strlen('http://www.myWebSite.com/myServiceScript.php?'.$parameterNames[0].'=procVarValue1&'.$parameterNames[1].'=procVarValue2&'.$parameterNames[2].'=value3&'.$parameterNames[3].'=value4&'));
		$this->assertTrue(strpos($callUrl, $procVarValue1));
		$this->assertTrue(strpos($callUrl, $procVarValue2));
		$this->assertTrue(strpos($callUrl, $parameterNames[2].'=value3'));
		$this->assertTrue(strpos($callUrl, $parameterNames[3].'=value4'));
		
		//delete all created resources:
		$myProcessVar1->delete();
		$myProcessVar2->delete();
		$serviceDefinition1->delete();
		$activityExec1->delete();
		$service1->delete();
		
		for($i=0;$i<4;$i++){
			$formalParam = $this->authoringService->getFormalParameter($parameterNames[$i]);
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
		
		$service1->delete();
	}

}
