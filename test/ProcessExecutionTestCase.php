<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Test ProcessExecution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class ProcessExecutionTestCase extends UnitTestCase{

	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	const OUTPUT = false;
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
	/**
	 * @var wfEngine_models_classes_UserService
	 */
	protected $userService = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $currentUser = null;
	
	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		TestRunner::initTest();
		
		error_reporting(E_ALL);
		
		if(is_null($this->userService)){
			$this->userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		}
		
		$login = 'wfTester';
		$pass = 'test123';
		$userData = array(
			PROPERTY_USER_LOGIN		=> 	$login,
			PROPERTY_USER_PASSWORD	=>	md5($pass),
			PROPERTY_USER_DEFLG		=>	'EN'
		);
		
		$this->currentUser = $this->userService->getOneUser($login);
		if(is_null($this->currentUser)){
			$this->userService->saveUser($this->currentUser, $userData, new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE));
		}
		
		core_kernel_users_Service::logout();
		if($this->userService->loginUser($login, md5($pass))){
			$this->userService->connectCurrentUser();
			$this->currentUser = $this->userService->getCurrentUser();
		}
	}
	
	/**
	 * output messages
	 * @param string $message
	 * @param boolean $ln
	 * @return void
	 */
	private function out($message, $ln = false){
		if(self::OUTPUT){
			if(PHP_SAPI == 'cli'){
				if($ln){
					echo "\n";
				}
				echo "$message\n";
			}
			else{
				if($ln){
					echo "<br />";
				}
				echo "$message<br />";
			}
		}
	}
	
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$this->assertIsA($processExecutionService, 'tao_models_classes_Service');
		$this->service = $processExecutionService;
	}
	
	/**
	 * Test the tokens into a sequancial process
	 */
	public function testVirtualSequencialProcess(){
		
		error_reporting(E_ALL);
		
		try{
			
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			
			//create a new process def
			$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
			$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
			
			//define activities and connectors
			$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
			$this->assertNotNull($activity1);
			$authoringService->setFirstActivity($processDefinition, $activity1);
			
			$connector1 = null;
			$connector1 = $authoringService->createConnector($activity1);
			$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector1);
			
			$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
			$this->assertNotNull($activity2);
			
			$connector2  = null; 
			$connector2 = $authoringService->createConnector($activity2);
			$authoringService->setConnectorType($connector2, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector2);
			
			$activity3 = $authoringService->createSequenceActivity($connector2, null, 'activity3');
			$this->assertNotNull($activity3);
			
			$connector3  = null; 
			$connector3 = $authoringService->createConnector($activity3);
			$authoringService->setConnectorType($connector3, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector3);
			
			$activity4 = $authoringService->createSequenceActivity($connector3, null, 'activity4');
			$this->assertNotNull($activity4);
			
			$connector4  = null; 
			$connector4 = $authoringService->createConnector($activity4);
			$authoringService->setConnectorType($connector4, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector4);
		
			$activity5 = $authoringService->createSequenceActivity($connector4, null, 'activity5');
			$this->assertNotNull($activity5);
			
			//run the process
			$factory = new wfEngine_models_classes_ProcessExecutionFactory();
			$factory->name = 'Test Process Execution';
			$factory->execution = $processDefinition->uriResource;
			$factory->ownerUri = SYS_USER_LOGIN;
		
			//init 1st activity
			$proc = $factory->create();
			
			$this->out(__METHOD__, true);
			
			$i = 1;
			while($i <= 5 ){
				$activity = $proc->currentActivity[0];
				
				$this->out("<strong>".$activity->label."</strong>", true);
				$this->assertTrue($activity->label == 'activity'.$i);
				
				//init execution
				$this->assertTrue($this->service->initCurrentExecution($proc->resource, $activity->resource, $this->currentUser));
				
				$activityExecuction = $activityExecutionService->getExecution($activity->resource, $this->currentUser, $proc->resource);
				$this->assertNotNull($activityExecuction);
				
				
				//transition to 2nd activity
				$proc->performTransition($activityExecuction->uriResource);
				
				$this->assertFalse($proc->isPaused());
				
				$i++;
			}
			$this->assertTrue($proc->isFinished());
			
			$activity1->delete();
			$activity2->delete();
			$activity3->delete();
			$activity4->delete();
			$activity5->delete();
			
			$connector1->delete();
			$connector2->delete();
			$connector3->delete();
			$connector4->delete();
			
			$proc->resource->delete();
			$processDefinition->delete();
			
			if(!is_null($this->currentUser)){
				core_kernel_users_Service::logout();
				$this->userService->removeUser($this->currentUser);
			}
		}
		catch(common_Exception $ce){
			$this->fail($ce);
		}
	}
	

	/**
	 * Test the tokens into a parallel process
	 */
	public function testVirtualParallelJoinProcess(){
		
		error_reporting(E_ALL);
		
		try{
			//init services
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
				
			//process definition
			$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$processDefinition = $processDefinitionClass->createInstance('PJ processForUnitTest_' . date(DATE_ISO8601),'created for the unit test of process execution');
			$this->assertNotNull($processDefinition);
			
			//activities definitions
			$activity0 = $authoringService->createActivity($processDefinition, 'activity0');
			$this->assertNotNull($activity0);
			
			$connector0 = null;
			$authoringService->setFirstActivity($processDefinition,$activity0);
			$connector0 = $authoringService->createConnector($activity0);
			$connectorParallele = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL);
			$authoringService->setConnectorType($connector0, $connectorParallele);
			$this->assertNotNull($connector0);
			
			$parallelActivity1 = $authoringService->createActivity($processDefinition, 'activity1');
			$this->assertNotNull($parallelActivity1);
			
			$connector1 = null;
			$connector1 = $authoringService->createConnector($parallelActivity1);
			$this->assertNotNull($connector1);
			
			$parallelActivity2 = $authoringService->createActivity($processDefinition, 'activity2');
			$this->assertNotNull($parallelActivity2);
			
			$connector2 = null;
			$connector2 = $authoringService->createConnector($parallelActivity2);
			$this->assertNotNull($connector2);
			
			$nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
			
			$connector0->setPropertyValue($nextActivitiesProp, $parallelActivity1->uriResource);
			$connector0->setPropertyValue($nextActivitiesProp, $parallelActivity2->uriResource);
			
			$joinActivity = $authoringService->createActivity($processDefinition, 'activity3');
			
			//join parallel Activity 1 and 2 to "joinActivity"
			$authoringService->createJoinActivity($connector1, $joinActivity, '', $parallelActivity1);
			$authoringService->createJoinActivity($connector2, $joinActivity, '', $parallelActivity2);
			
			
			//run process
			$factory = new wfEngine_models_classes_ProcessExecutionFactory();
			$factory->name = 'Test Process Execution Parallel';
			$factory->execution = $processDefinition->uriResource;
			$factory->ownerUri = SYS_USER_LOGIN;
	
			
			$proc = $factory->create();
			
			$this->out(__METHOD__, true);
			
			$i = 0;
			$current = 0;
			while($i < 4){
				$activity = $proc->currentActivity[$current];
				//$this->assertTrue($activity->label == 'activity'.$i);
		
				$this->out("<strong>".$activity->label."</strong>", true);
						
				//init execution
				$this->assertTrue($this->service->initCurrentExecution($proc->resource, $activity->resource, $this->currentUser));
				
				$activityExecuction = $activityExecutionService->getExecution($activity->resource, $this->currentUser, $proc->resource);
				$this->assertNotNull($activityExecuction);
				
				//transition to 2nd activity
				$proc->performTransition($activityExecuction->uriResource);
				
				if($proc->isPaused()){
					/*
					 * Login an other user 
					 */
					
					core_kernel_users_Service::logout();
					$this->assertTrue($this->userService->removeUser($this->currentUser));
					
					$login = 'wfTester2';
					$pass = 'test456';
					$userData = array(
						PROPERTY_USER_LOGIN		=> 	$login,
						PROPERTY_USER_PASSWORD	=>	md5($pass),
						PROPERTY_USER_DEFLG		=>	'EN'
					);
		
					$othertUser = $this->userService->getOneUser($login);
					if(is_null($othertUser)){
						$this->userService->saveUser($othertUser, $userData, new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE));
					}
					if($this->userService->loginUser($login, md5($pass))){
						$this->userService->connectCurrentUser();
						$this->currentUser = $this->userService->getCurrentUser();
						if($current == 0){
							$current = 1;
						}
						else{
							$current = 0;
						}
						$this->out("new user logged in: ".$this->currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN)), true);
					}
					else{
						$this->fail("unable to login user $login<br>");
					}
				}
			
				$i++;
			}
			
			$this->assertTrue($proc->isFinished());
			
			$activity0->delete();
			$connector0->delete();
			
			$parallelActivity1->delete();
			$connector1->delete();
			
			$parallelActivity2->delete();
			$connector2->delete();
			
			$joinActivity->delete();
			
			$proc->resource->delete();
				
			//delete processdef:
			$authoringService->deleteProcess($processDefinition);
			
			if(!is_null($this->currentUser)){
				core_kernel_users_Service::logout();
				$this->userService->removeUser($this->currentUser);
			}
		}
		catch(common_Exception $ce){
			$this->fail($ce);
		}
	}
}

?>