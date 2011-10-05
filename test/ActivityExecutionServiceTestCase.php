<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the service wfEngine_models_classes_ActivityExecutionService
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class ActivityExecutionServiceTestCase extends UnitTestCase {
	
	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	const OUTPUT = true;
	
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
		$this->userPassword = $pass;
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
		
		$this->service = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
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
		
		$aeService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$this->assertIsA($aeService, 'tao_models_classes_Service');
		$this->assertIsA($aeService, 'wfEngine_models_classes_ActivityExecutionService');

	}
	
	private function createUser($login){
		
		$returnValue = null;
		
		$userData = array(
			PROPERTY_USER_LOGIN		=> 	$login,
			PROPERTY_USER_PASSWORD	=>	md5($this->userPassword),
			PROPERTY_USER_DEFLG		=>	'EN'
		);
		
		$user = $this->userService->getOneUser($login);
		if(is_null($user)){
			$this->userService->saveUser(null, $userData, new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE));
			$returnValue = $this->userService->getOneUser($login);
		}else{
			$returnValue = $user;
		}
		
		if(is_null($returnValue)){
			throw new Exception('cannot get the user with login '.$login);
		}
		
		return $returnValue;
	}
	
	private function changeUser($login){
		
		$returnValue = false;
		
		//Login another user to execute parallel branch
		core_kernel_users_Service::logout();
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		$this->out("logout ". $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->uriResource . '"', true);
		if($this->userService->loginUser($login, md5($this->userPassword))){
			$this->userService->connectCurrentUser();
			$this->currentUser = $this->userService->getCurrentUser();
			$returnValue = true;
			$this->out("new user logged in: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"');
		}else{
			$this->fail("unable to login user $login<br>");
		}
		
		$this->service->clearCache('wfEngine_models_classes_ActivityExecutionService::checkAcl');
		
		return $returnValue;
	}
	
	private function checkAccessControl($activityExecution){
		
		$aclMode = $this->service->getAclMode($activityExecution);
		$restricedRole = $this->service->getRestrictedRole($activityExecution);
		$restrictedTo = !is_null($restricedRole) ? $restricedRole : $this->service->getRestrictedUser($activityExecution);
		$this->assertNotNull($aclMode);
		$this->assertNotNull($restrictedTo);
		$this->out("ACL mode: {$aclMode->getLabel()}; restricted to {$restrictedTo->getLabel()}", true);
		
	}
	
	/**
	 * Test the sequential process execution:
	 */
	public function testVirtualSequencialProcess(){
		
		error_reporting(E_ALL);
		
		try{
			
			$roleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RoleService');
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			
			//create roles and users:
			$roleClass = new core_kernel_classes_Class(CLASS_ROLE_WORKFLOWUSERROLE);
			$roleA = $roleService->createInstance($roleClass, 'ACLTestCaseRoleA');
			$roleB = $roleService->createInstance($roleClass, 'ACLTestCaseRoleB');
			$roleC = $roleService->createInstance($roleClass, 'ACLTestCaseRoleC');
			
			list($usec, $sec) = explode(" ", microtime());
			$users = array();
			$users[0] = $usec;
			for($i=1; $i<=6; $i++){
				$users[] = 'ACLTestCaseUser'.$i.'-'.$usec;
			}
			$user1 = $this->createUser($users[1]);$user1->setLabel($users[1]);
			$user2 = $this->createUser($users[2]);$user2->setLabel($users[2]);
			$user3 = $this->createUser($users[3]);$user3->setLabel($users[3]);
			$user4 = $this->createUser($users[4]);$user4->setLabel($users[4]);
			$user5 = $this->createUser($users[5]);$user5->setLabel($users[5]);
			$user6 = $this->createUser($users[6]);$user6->setLabel($users[6]);
			
			$roleService->setRoleToUsers($roleA, array(
				$user1->uriResource,
				$user2->uriResource,
				$user3->uriResource
			));
			$roleService->setRoleToUsers($roleB, array(
				$user4->uriResource,
				$user5->uriResource
			));
			$roleService->setRoleToUsers($roleC, array(
				$user6->uriResource
			));
			
			//create a new process def
			$processDefinition = $authoringService->createProcess('ProcessForUnitTest', 'Unit test');
			$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
			
			//define activities and connectors
			
			//activity 1:
			$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
			$this->assertNotNull($activity1);
			$authoringService->setFirstActivity($processDefinition, $activity1);
			$activityService->setAcl($activity1, new core_kernel_classes_Resource(INSTANCE_ACL_ROLE), $roleA);
			
			$connector1 = $authoringService->createConnector($activity1);
			$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector1);
			
			//activity 2:
			$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
			$this->assertNotNull($activity2);
			$activityService->setAcl($activity2, new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER), $roleB);
			
			$connector2 = $authoringService->createConnector($activity2);
			$authoringService->setConnectorType($connector2, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector2);
			
			//activity 3:
			$activity3 = $authoringService->createSequenceActivity($connector2, null, 'activity3');
			$this->assertNotNull($activity3);
			$activityService->setAcl($activity3, new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED), $roleB);
			
			$connector3 = $authoringService->createConnector($activity3);
			$authoringService->setConnectorType($connector3, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector3);
			
			//activity 4:
			$activity4 = $authoringService->createSequenceActivity($connector3, null, 'activity4');
			$this->assertNotNull($activity4);
			$activityService->setAcl($activity4, new core_kernel_classes_Resource(INSTANCE_ACL_USER), $user2);
			
			$connector4 = $authoringService->createConnector($activity4);
			$authoringService->setConnectorType($connector4, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector4);
			
			//activity 5:
			$activity5 = $authoringService->createSequenceActivity($connector4, null, 'activity5');
			$this->assertNotNull($activity5);
			$activityService->setAcl($activity5, new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED), $roleB);
			
			$connector5 = $authoringService->createConnector($activity5);
			$authoringService->setConnectorType($connector5, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector5);
			
			//activity 6:
			$activity6 = $authoringService->createSequenceActivity($connector5, null, 'activity6');
			$this->assertNotNull($activity6);
			$activityService->setAcl($activity6, new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY), $roleA);
			
			
			//run the process
			$processExecName = 'Test Process Execution';
			$processExecComment = 'created for processExecustionService test case by '.__METHOD__;
			$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment);
			$this->assertEqual($processDefinition->uriResource, $processExecutionService->getExecutionOf($processInstance)->uriResource);
			$this->assertEqual($processDefinition->uriResource, $processExecutionService->getExecutionOf($processInstance)->uriResource);
			
			$this->assertTrue($processExecutionService->checkStatus($processInstance, 'started'));
			
			$this->out(__METHOD__, true);
			
			$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$this->assertEqual(count($currentActivityExecutions), 1);
			$this->assertEqual(strpos(array_pop($currentActivityExecutions)->getLabel(), 'Execution of activity1'), 0);
			
			$this->out("<strong>Forward transitions:</strong>", true);
			
			$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
			
			$iterationNumber = 6;
			$i = 1;
			while($i <= $iterationNumber){
				if($i<$iterationNumber){
					//try deleting a process that is not finished
					$this->assertFalse($processExecutionService->deleteProcessExecution($processInstance, true));
				}
				
				$activities = $processExecutionService->getAvailableCurrentActivityDefinitions($processInstance, $this->currentUser);
				$this->assertEqual(count($activities), 1);
				$activity = array_shift($activities);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$this->assertTrue($activity->getLabel() == 'activity'.$i);
				$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"', true);
				
				$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
				$activityExecution = reset($activityExecutions);
				
				$this->checkAccessControl($activityExecution);
				
				//check ACL:
				switch($i){
					case 1:{
						//INSTANCE_ACL_ROLE, $roleA:
						
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 2:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER, $roleB:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 3:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 4:{
						//INSTANCE_ACL_USER, $user2:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 5:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB:
						//only user5 can access it normally:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 6:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY, $roleA:
						//only the user of $roleA that executed (the initial acivity belongs to user2:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
				}
				
				
				//init execution
				$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
				
				//transition to next activity
				$transitionResult = $processExecutionService->performTransition($processInstance, $activityExecution);
				switch($i){
					case 1:
					case 3:
					case 4:
					case 5:{
						$this->assertFalse(count($transitionResult));
						$this->assertTrue($processExecutionService->isPaused($processInstance));
						break;
					}
					case 2:{
						$this->assertTrue(count($transitionResult));
						$this->assertFalse($processExecutionService->isPaused($processInstance));
						break;
					}
					case 6:{
						$this->assertFalse(count($transitionResult));
						$this->assertTrue($processExecutionService->isFinished($processInstance));
						break;
					}
				}
				
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$processExecutionService->getStatus($processInstance)->getLabel());
				
				
				$i++;
			}
			$this->assertTrue($processExecutionService->isFinished($processInstance));
			
			$this->out("<strong>Backward transitions:</strong>", true);
			$j = 0;
			while($j < $iterationNumber){
				
				$activitieExecs = $processExecutionService->getCurrentActivityExecutions($processInstance);
				$this->assertEqual(count($activitieExecs), 1);
				$activityExecution = reset($activitieExecs);
				$activity = $activityExecutionService->getExecutionOf($activityExecution);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$index = $iterationNumber - $j;
				$this->assertEqual($activity->getLabel(), "activity$index");
				$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"', true);
				
				$this->checkAccessControl($activityExecution);
				
				//check ACL:
				switch($index){
					case 1:{
						//INSTANCE_ACL_ROLE, $roleA:
						
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 2:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER, $roleB:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 3:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 4:{
						//INSTANCE_ACL_USER, $user2:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 5:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB:
						//only user5 can access it normally:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
//						var_dump($processExecutionService->getAllActivityExecutions($processInstance));
						break;
					}
					case 6:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY, $roleA:
						//only the user of $roleA that executed (the initial acivity belongs to user2:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					
				}
				
				//init execution
				$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
				
				//transition to next activity
				$transitionResult = $processExecutionService->performBackwardTransition($processInstance, $activityExecution);
				$processStatus = $processExecutionService->getStatus($processInstance);
				$this->assertNotNull($processStatus);
				$this->assertEqual($processStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
				if($j < $iterationNumber-1){
					$this->assertTrue(count($transitionResult));
				}else{
					$this->assertFalse(count($transitionResult));
				}
				
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$processExecutionService->getStatus($processInstance)->getLabel());
				$j++;
			}
			
			$this->out("<strong>Forward transitions again:</strong>", true);
			
			$i = 1;
			while($i <= $iterationNumber){
				if($i<$iterationNumber){
					//try deleting a process that is not finished
					$this->assertFalse($processExecutionService->deleteProcessExecution($processInstance, true));
				}
				
				$activitieExecs = $processExecutionService->getCurrentActivityExecutions($processInstance);
				$this->assertEqual(count($activitieExecs), 1);
				$activityExecution = reset($activitieExecs);
				$activity = $activityExecutionService->getExecutionOf($activityExecution);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$this->assertTrue($activity->getLabel() == 'activity'.$i);
				
				$this->checkAccessControl($activityExecution);
				
				
				//check ACL:
				switch($i){
					case 1:{
						//INSTANCE_ACL_ROLE, $roleA:
						
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 2:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER, $roleB:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 3:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 4:{
						//INSTANCE_ACL_USER, $user2:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 5:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB:
						//only user5 can access it normally:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					case 6:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY, $roleA:
						//only the user of $roleA that executed (the initial acivity belongs to user2:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser));
						
						break;
					}
					
				}
				
				//init execution
				$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activity, $this->currentUser);
				$this->assertNotNull($activityExecution);
				
				//transition to next activity
				$transitionResult = $processExecutionService->performTransition($processInstance, $activityExecution);
				switch($i){
					case 1:
					case 3:
					case 4:
					case 5:{
						$this->assertFalse(count($transitionResult));
						$this->assertTrue($processExecutionService->isPaused($processInstance));
						break;
					}
					case 2:{
						$this->assertTrue(count($transitionResult));
						$this->assertFalse($processExecutionService->isPaused($processInstance));
						break;
					}
					case 6:{
						$this->assertFalse(count($transitionResult));
						$this->assertTrue($processExecutionService->isFinished($processInstance));
						break;
					}
				}
				
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$processExecutionService->getStatus($processInstance)->getLabel());
				
				$i++;
			}
			
			$this->assertTrue($processExecutionService->isFinished($processInstance));
			
			//delete processdef:
			$this->assertTrue($authoringService->deleteProcess($processDefinition));
			
			//delete process execution:
			$this->assertTrue($processInstance->exists());
			$this->assertTrue($processExecutionService->deleteProcessExecution($processInstance));
			$this->assertFalse($processInstance->exists());
			
			if(!is_null($this->currentUser)){
				core_kernel_users_Service::logout();
				$this->userService->removeUser($this->currentUser);
			}
			
			$roleA->delete();
			$roleB->delete();
			$roleC->delete();
			$user1->delete();
			$user2->delete();
			$user3->delete();
			$user4->delete();
			$user5->delete();
			$user6->delete();
		}
		catch(common_Exception $ce){
			$this->fail($ce);
		}
	}
}
?>