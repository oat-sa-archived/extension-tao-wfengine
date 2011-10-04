<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test ProcessExecution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class ProcessExecutionServiceTestCase extends UnitTestCase{

	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	const OUTPUT = true;
	
	/**
	 * CHANGE IT MANNUALLY to use service cache
	 * @var boolean
	 */
	const SERVICE_CACHE = false;
	
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
		
		list($usec, $sec) = explode(" ", microtime());
		$login = 'wfTester-0';
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
			$this->currentUser0 = $this->currentUser;
		}
	}
	
	public function tearDown() {
		$this->currentUser0->delete();
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
		$processExecutionService->cache = (bool) self::SERVICE_CACHE;
	}
	
	/**
	 * Test the sequential process execution:
	 */
	public function testVirtualSequencialProcess(){
		
		error_reporting(E_ALL);
		
		try{
			$t_start = microtime(true);
			
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			$activityExecutionService->cache = (bool) self::SERVICE_CACHE;
			$this->service = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			
			//create a new process def
			$processDefinition = $authoringService->createProcess('ProcessForUnitTest', 'Unit test');
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
			$processExecName = 'Test Process Execution';
			$processExecComment = 'created for processExecustionService test case by '.__METHOD__;
			$processInstance = $this->service->createProcessExecution($processDefinition, $processExecName, $processExecComment);
			$this->assertEqual($processDefinition->uriResource, $this->service->getExecutionOf($processInstance)->uriResource);
			$this->assertEqual($processDefinition->uriResource, $this->service->getExecutionOf($processInstance)->uriResource);
			
			$this->assertTrue($this->service->checkStatus($processInstance, 'started'));
			
			$this->out(__METHOD__, true);
			
			$currentActivityExecutions = $this->service->getCurrentActivityExecutions($processInstance);
			$this->assertEqual(count($currentActivityExecutions), 1);
			$this->assertEqual(strpos(array_pop($currentActivityExecutions)->getLabel(), 'Execution of activity1'), 0);
			
			$this->out("<strong>Forward transitions:</strong>", true);
			
			$iterationNumber = 5;
			$i = 1;
			while($i <= $iterationNumber){
				if($i<$iterationNumber){
					//try deleting a process that is not finished
					$this->assertFalse($this->service->deleteProcessExecution($processInstance, true));
				}
				
				$activities = $this->service->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
				$this->assertEqual(count($activities), 1);
				$activity = array_shift($activities);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$this->assertTrue($activity->getLabel() == 'activity'.$i);
				
				//init execution
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_STARTED);
				
				//transition to next activity
				$transitionResult = $this->service->performTransition($processInstance, $activityExecution);
				if($i < $iterationNumber){
					$this->assertTrue(count($transitionResult));
				}else{
					$this->assertFalse(count($transitionResult));
				}
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
				$this->assertFalse($this->service->isPaused($processInstance));
				
				$i++;
			}
			$this->assertTrue($this->service->isFinished($processInstance));
			
			$this->out("<strong>Backward transitions:</strong>", true);
			$j = 0;
			while($j < $iterationNumber){
				
				$activities = $this->service->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
				$this->assertEqual(count($activities), 1);
				$activity = array_shift($activities);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$index = $iterationNumber - $j;
				$this->assertEqual($activity->getLabel(), "activity$index");
				
				//init execution
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
				
				//transition to next activity
				$transitionResult = $this->service->performBackwardTransition($processInstance, $activityExecution);
				$processStatus = $this->service->getStatus($processInstance);
				$this->assertNotNull($processStatus);
				$this->assertEqual($processStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
				if($j < $iterationNumber-1){
					$this->assertTrue(count($transitionResult));
				}else{
					$this->assertFalse(count($transitionResult));
				}
				
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
				$j++;
			}
			
			$this->out("<strong>Forward transitions again:</strong>", true);
			
			$i = 1;
			while($i <= $iterationNumber){
				if($i<$iterationNumber){
					//try deleting a process that is not finished
					$this->assertFalse($this->service->deleteProcessExecution($processInstance, true));
				}
				
				$activities = $this->service->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
				
				$this->assertEqual(count($activities), 1);
				$activity = array_shift($activities);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$this->assertTrue($activity->getLabel() == 'activity'.$i);
				
				//init execution
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				if($i == 1){
					$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
				}else{
					$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_STARTED);
				}
				
				//check process content:
//				var_dump($this->service->getAllActivityExecutions($processInstance));
				
				//transition to next activity
				$transitionResult = $this->service->performTransition($processInstance, $activityExecution);
				if($i<$iterationNumber){
					$this->assertTrue(count($transitionResult));
				}else{
					$this->assertFalse(count($transitionResult));
				}
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
				
				$this->assertFalse($this->service->isPaused($processInstance));
				
				$i++;
			}
			$this->assertTrue($this->service->isFinished($processInstance));
			
			$t_end = microtime(true);
			$duration = $t_end - $t_start;
			$this->out('Elapsed time: '.$duration.'s', true);
		
			//delete processdef:
			$this->assertTrue($authoringService->deleteProcess($processDefinition));
			
			//delete process execution:
			$this->assertTrue($processInstance->exists());
			$this->assertTrue($this->service->deleteProcessExecution($processInstance));
			$this->assertFalse($processInstance->exists());
			
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
		
		$t_start = microtime(true);
		
		//init services
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$activityExecutionService->cache = (bool) self::SERVICE_CACHE;
		
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
		
		//define parallel activities, first branch with constant cardinality value, while the second listens to a process variable:
		$parallelCount1 = 3;
		$parallelCount2 = 5;
		$parallelCount2_processVar_key = 'unit_var_'.time();
		$parallelCount2_processVar = $processVariableService->createProcessVariable('Proc Var for unit test', $parallelCount2_processVar_key);
		$prallelActivitiesArray = array(
			$parallelActivity1->uriResource => $parallelCount1,
			$parallelActivity2->uriResource => $parallelCount2_processVar
		);
		
		$result = $authoringService->setParallelActivities($connector0, $prallelActivitiesArray);
		$this->assertTrue($result);
		
		$prallelActivitiesArray[$parallelActivity2->uriResource] = $parallelCount2;
		
		

		$joinActivity = $authoringService->createActivity($processDefinition, 'activity3');

		//join parallel Activity 1 and 2 to "joinActivity"
		$authoringService->createJoinActivity($connector1, $joinActivity, '', $parallelActivity1);
		$authoringService->createJoinActivity($connector2, $joinActivity, '', $parallelActivity2);

		//run the process
		$processExecName = 'Test Parallel Process Execution';
		$processExecComment = 'created for processExecustionService test case by '.__METHOD__;
		$processInstance = $this->service->createProcessExecution($processDefinition, $processExecName, $processExecComment);
		$this->assertTrue($this->service->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);
		$this->out("<strong>Forward transitions:</strong>", true);
		
		$numberActivities = 2 + $parallelCount1 + $parallelCount2;
		$createdUsers = array();
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		for($i=1; $i <= $numberActivities; $i++){

			$activities = $this->service->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
			$countActivities = count($activities);
			$activity = null;
			if($countActivities > 1){
				//select one of the available activities in the parallel branch:
				foreach($activities as $activityUri => $act){
					if(isset($prallelActivitiesArray[$activityUri])){
						if($prallelActivitiesArray[$activityUri] > 0){
							$prallelActivitiesArray[$activityUri]--;
							$activity = $act;
							break;
						}
					}
				}
			}else if($countActivities == 1){
				$activity = reset($activities);
			}else{
				$this->fail('no current activity definition found for the iteration '.$i);
			}

			$this->out("<strong> Iteration {$i} :</strong>", true);
			$this->out("<strong>{$activity->getLabel()}</strong> (among {$countActivities})");
			
			//init execution
			$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
			$this->assertNotNull($activityExecution);
			if($i == 1){
				//set value of the parallel thread:
				$processVariableService->push($parallelCount2_processVar_key, $parallelCount2);
			}
			
			$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
			$this->assertNotNull($activityExecStatus);
			$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_STARTED);

			//transition to next activity
			$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"');
			$this->out("performing transition ...");

			//transition to next activity
			$this->service->performTransition($processInstance, $activityExecution);
			$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
			$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());

			if($this->service->isPaused($processInstance)){

				//Login another user to execute parallel branch
				core_kernel_users_Service::logout();
				$this->out("logout ". $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->uriResource . '"', true);
				
				list($usec, $sec) = explode(" ", microtime());
				$login = 'wfTester-'.$i.'-'.$usec;
				$pass = 'test123';
				$userData = array(
					PROPERTY_USER_LOGIN		=> 	$login,
					PROPERTY_USER_PASSWORD	=>	md5($pass),
					PROPERTY_USER_DEFLG		=>	'EN'
				);

				$otherUser = $this->userService->getOneUser($login);
				if(is_null($otherUser)){
					$this->assertTrue($this->userService->saveUser(null, $userData, new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE)));
					$otherUser = $this->userService->getOneUser($login);
				}
				$createdUsers[$otherUser->uriResource] = $otherUser; 

				if($this->userService->loginUser($login, md5($pass))){
					$this->userService->connectCurrentUser();
					$this->currentUser = $this->userService->getCurrentUser();
					$this->out("new user logged in: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"');
				}else{
					$this->fail("unable to login user $login<br>");
				}
			}
		}
		$this->assertTrue($this->service->isFinished($processInstance));
		
		$this->out("<strong>Backward transitions:</strong>", true);
		
		$j = 0;
		$iterationNumber = 2;
		while($j < $iterationNumber){

			$activities = $this->service->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
			$this->assertEqual(count($activities), 1);
			$activity = array_shift($activities);

			$this->out("<strong>".$activity->getLabel()."</strong>", true);

			//init execution
			$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
			$this->assertNotNull($activityExecution);
			$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
			$this->assertNotNull($activityExecStatus);
			$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
			
			$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"');
			$this->out("performing transition ...");

			//transition to next activity
			$transitionResult = $this->service->performBackwardTransition($processInstance, $activityExecution);
			if($j == 0){
				$this->assertTrue($transitionResult);
			}else if($j == $iterationNumber - 1){
				$this->assertFalse($transitionResult);
			}
			
			$processStatus = $this->service->getStatus($processInstance);
			$this->assertNotNull($processStatus);
			$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
			$this->out("process status: ".$processStatus->getLabel());
			$this->assertEqual($processStatus->uriResource, INSTANCE_PROCESSSTATUS_PAUSED);
			
			$j++;
		}
		
		$this->out("<strong>Forward transitions again:</strong>", true);
//		var_dump($this->service->getAllActivityExecutions($processInstance));
		$currentActivityExecutions = $this->service->getCurrentActivityExecutions($processInstance);
		
		$currentActivityExecutionsCount = count($currentActivityExecutions);
		$this->assertEqual($currentActivityExecutionsCount, $parallelCount1 + $parallelCount2);
		
		for($i=0; $i<$currentActivityExecutionsCount; $i++){
			
			$currentActivityExecution = array_pop($currentActivityExecutions);
			$user = $activityExecutionService->getActivityExecutionUser($currentActivityExecution);
			$activityDefinition = $activityExecutionService->getExecutionOf($currentActivityExecution);
			$this->assertNotNull($user);
			$this->assertNotNull($activityDefinition);
			
			if(!is_null($user) && !is_null($activityDefinition)){
				
				core_kernel_users_Service::logout();
				$this->out("logout ". $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->uriResource . '"', true);

				$login = (string) $user->getUniquePropertyValue($loginProperty);
				$pass = 'test123';
				if ($this->userService->loginUser($login, md5($pass))) {
					$this->userService->connectCurrentUser();
					$this->currentUser = $this->userService->getCurrentUser();
					$this->out("new user logged in: " . $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->uriResource . '"');
				} else {
					$this->fail("unable to login user $login<br>");
				}
				
				//check if the activity definition is among the currently available ones:
				$activityAvailable = false;
				$activities = $this->service->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
				foreach($activities as $activity){
					if($activity->uriResource == $activityDefinition->uriResource){
						$activityAvailable = true;
						break;
					}
				}
				$this->assertTrue($activityAvailable);
				
				$iterationNo = $i+1;
				$this->out("<strong>Iteration $iterationNo: ".$activityDefinition->getLabel()."</strong>", true);
				
				//execute activity:
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activityDefinition, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
				
				//transition to next activity
				$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"');
				$this->out("performing transition ...");

				//transition to next activity
				$this->service->performTransition($processInstance, $activityExecution);
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
			}
		}
		
		$activities = $this->service->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
		$this->assertEqual(count($activities), 1);
		$activity = array_shift($activities);
		$this->assertEqual($activity->uriResource, $joinActivity->uriResource);
		
		$this->out("<strong>Executing last activity: ".$activity->getLabel()."</strong>", true);
			
		//init execution
		$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
		$this->assertNotNull($activityExecution);

		$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
		$this->assertNotNull($activityExecStatus);
		$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_STARTED);

		//transition to next activity
		$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"');
		$this->out("performing transition ...");

		//transition to next activity
		$this->service->performTransition($processInstance, $activityExecution);
		$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
		$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
		$this->assertTrue($this->service->isFinished($processInstance));
		
		$t_end = microtime(true);
		$duration = $t_end - $t_start;
		$this->out('Elapsed time: '.$duration.'s', true);
		
		$this->out('deleting created resources:', true);
		//delete process exec:
		$this->assertTrue($this->service->deleteProcessExecution($processInstance));

		//delete processdef:
		$this->assertTrue($authoringService->deleteProcess($processDefinition));
		$parallelCount2_processVar->delete();
		
		//delete created users:
		foreach($createdUsers as $createdUser){
			$this->out('deleting '.$createdUser->getLabel().' "'.$createdUser->uriResource.'"');
			$this->assertTrue($this->userService->removeUser($createdUser));
		}

		if(!is_null($this->currentUser)){
			core_kernel_users_Service::logout();
			$this->userService->removeUser($this->currentUser);
		}
		
	}
}

?>