<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test the service wfEngine_models_classes_RecoveryService
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class RecoveryServiceTestCase extends UnitTestCase {
	
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
	protected $activityExecution = null;
	
	
	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		TestRunner::initTest();
		
		$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$this->activityExecution = $activityExecutionClass->createInstance('test');
		
	}
	
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		
		$recoveryService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RecoveryService');
		$this->assertIsA($recoveryService, 'tao_models_classes_Service');
		$this->assertIsA($recoveryService, 'wfEngine_models_classes_RecoveryService');

		$this->service = $recoveryService;
	}
	
	/**
	 * Test the context recovery saving, retrieving and remving
	 */
	public function testContext(){
		
		$context = array(
			'data' => array(
				'boolean'	=> true,
				'integer'	=> 12,
				'array'		=> array(1, 2)
			),
			'other'	=> 12
		);
		
		$this->assertTrue($this->service->saveContext($this->activityExecution, $context));
		
		$recoveredContext = $this->service->getContext($this->activityExecution);
		$this->assertTrue(is_array($recoveredContext));
		$this->assertTrue(isset($recoveredContext['data']['array']));
		
		$this->service->removeContext($this->activityExecution);
		
		$this->assertTrue(count($this->service->getContext($this->activityExecution)) == 0);
		
		$this->assertTrue($this->activityExecution->delete());
	}
	
}
?>