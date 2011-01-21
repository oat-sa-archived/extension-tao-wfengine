<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 * Test the service wfEngine_models_classes_NotificationService
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class NotificationServiceTestCase extends UnitTestCase {
	
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
			PROPERTY_USER_DEFLG		=>	'EN',
			PROPERTY_USER_MAIL		=> 'bertrand.chevrier@tudor.lu'
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
		
		$aeService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_NotificationService');
		$this->assertIsA($aeService, 'tao_models_classes_Service');
		$this->assertIsA($aeService, 'wfEngine_models_classes_NotificationService');

		$this->service = $aeService;
	}
	
	/**
	 * Test the notifications in a sequencial process
	 */
	public function testNotificationsInProcess(){
		try{
			
			$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			
			//create a new process def
			$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
			$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
			
			$aclModeRole		 = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
			$aclModeUser		 = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
			
			$role1		 = new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE);
			
			$roleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RoleService');
			$role2		 = $roleService->createInstance($roleService->getRoleClass(), 'test role 2');
			$roleService->setRoleToUsers($role2, array($this->currentUser->uriResource));
			
			
			//define activities and connectors
			$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
			$this->assertNotNull($activity1);
			
			$authoringService->setFirstActivity($processDefinition, $activity1);
			
			//activity is allowed to the created role
			$activityExecutionService->setAcl($activity1, $aclModeRole, $role2);
			
			$connector1 = null;
			$connector1 = $authoringService->createConnector($activity1);
			$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector1);
			
			
			$this->service->bindProperties($connector1, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_USER,
				PROPERTY_CONNECTORS_USER_NOTIFIED => $this->currentUser->uriResource,
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 1 notification to user '.$this->currentUser->getLabel()
			));
			
			
			$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
			$this->assertNotNull($activity2);
			
			//2nd activity is allowed to create role
			$activityExecutionService->setAcl($activity2, $aclModeRole, $role2);
			
			$connector2  = null;
			$connector2 = $authoringService->createConnector($activity2);
			$authoringService->setConnectorType($connector2, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector2);
			
			$this->service->bindProperties($connector2, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_PREVIOUS,
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 2 notification to previous activity user '.$this->currentUser->getLabel()
			));
			
			
			$activity3 = $authoringService->createSequenceActivity($connector2, null, 'activity3');
			$this->assertNotNull($activity3);
			
			$connector3  = null;
			$connector3 = $authoringService->createConnector($activity3);
			$authoringService->setConnectorType($connector3, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector3);
			
			$this->service->bindProperties($connector3, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_NEXT,
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 3 notification to next activity user '.$this->currentUser->getLabel()
			));
			
			$activity4 = $authoringService->createSequenceActivity($connector3, null, 'activity4');
			$this->assertNotNull($activity4);
			
			//allowed to the currentUser only
			$activityExecutionService->setAcl($activity4, $aclModeUser, $this->currentUser);
		
		
			$connector4  = null;
			$connector4 = $authoringService->createConnector($activity4);
			$authoringService->setConnectorType($connector4, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector4);
			
			$this->service->bindProperties($connector4, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_ROLE,
				PROPERTY_CONNECTORS_ROLE_NOTIFIED => $role2->uriResource,
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 4 notification to role user '.$role2->getLabel()
			));
		
			$activity5 = $authoringService->createSequenceActivity($connector4, null, 'activity5');
			$this->assertNotNull($activity5);
			
			//run the process
			$factory = new wfEngine_models_classes_ProcessExecutionFactory();
			$factory->name = 'Test Process Execution';
			$factory->execution = $processDefinition->uriResource;
			$factory->ownerUri = SYS_USER_LOGIN;
			
			//init 1st activity
			$proc = $factory->create();
			
			$i = 1;
			while($i <= 5 ){
				
				$activity = $proc->currentActivity[0];
				
				$this->out("Activity: ".$activity->label, true);
				$this->assertTrue($activity->label == 'activity'.$i);
				
				//init execution
				$processExecutionService->initCurrentExecution($proc->resource, $activity->resource, $this->currentUser);
				$activityExecuction = $activityExecutionService->getExecution($activity->resource, $this->currentUser, $proc->resource);
				$this->assertIsA($activityExecuction, "core_kernel_classes_Resource");
				
				//transition to nextactivity
				$proc->performTransition($activityExecuction->uriResource);
				
				$this->assertFalse($proc->isPaused());
				
				$i++;
			}
			$this->assertTrue($proc->isFinished());
			
			//check the created notifications
			$notificationCount = 0;
			
			$notificationProcessExecProp 	= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_PROCESS_EXECUTION);
			$notificationToProp 			= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_TO);
			
			$notificationsToSend = $this->service->getNotificationsToSend();
			$this->assertTrue(count($notificationsToSend) > 0);
			foreach($this->service->getNotificationsToSend() as $notification){
				$notificationProcess = $notification->getOnePropertyValue($notificationProcessExecProp);
				if(!is_null($notificationProcess)){
					if($notificationProcess->uriResource == $proc->resource->uriResource){
						
						$notifiedUser = $notification->getOnePropertyValue($notificationToProp);
						$this->assertNotNull($notifiedUser);
						$this->assertEqual($notifiedUser->uriResource, $this->currentUser->uriResource);
						
						$notificationCount++;
						
						$this->assertTrue($notification->delete());
					}
				}
			} 
			
			$this->assertEqual($notificationCount, 4);
			
//			$this->out("$notificationCount notification to send");
//			$this->service->sendNotifications(new tao_helpers_transfert_MailAdapter());
//			$this->out("All notifications sent");
			
			
			$this->assertTrue($activity1->delete());
			$this->assertTrue($activity2->delete());
			$this->assertTrue($activity3->delete());
			$this->assertTrue($activity4->delete());
			$this->assertTrue($activity5->delete());
			
			$this->assertTrue($connector1->delete());
			$this->assertTrue($connector2->delete());
			$this->assertTrue($connector3->delete());
			$this->assertTrue($connector4->delete());
			
			$this->assertTrue($role2->delete());
			$this->assertTrue($proc->resource->delete());
			$this->assertTrue($processDefinition->delete());
			
			if(!is_null($this->currentUser)){
				core_kernel_users_Service::logout();
				$this->assertTrue($this->userService->removeUser($this->currentUser));
			}
		}
		catch(common_Exception $ce){
			$this->fail($ce);
		}
	}
	
}
?>