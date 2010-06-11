<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

/**
 * Test the service wfEngine_models_classes_ActivityExecutionService
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class ActivityExecutionServiceTestCase extends UnitTestCase {
	
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
	 * Test the service implementation
	 */
	public function testService(){
		
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		$this->assertIsA($tokenService, 'tao_models_classes_Service');
		$this->assertIsA($tokenService, 'wfEngine_models_classes_TokenService');

		$this->service = $tokenService;
	}
	
	/**
	 * 
	 */
	public function testVirtualProcess(){
		try{
			
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			
			
			//create a new process def
			$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
			$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
			
			//define activities and connectors
			$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
			$authoringService->setFirstActivity($processDefinition, $activity1);
			
			$connector1 = $authoringService->createConnector($activity1);
			$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(CONNECTOR_SEQ));
			$this->assertNotNull($connector1);
			
			$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
			$connector2  = null; 
			$connector2s = $authoringService->getConnectorsByActivity($activity2, array('next'));
			foreach($connector2s['next'] as $connector){
				$connector2 = $connector;
				break;
			}
			$this->assertNotNull($connector2);
			
			$activity3 = $authoringService->createSequenceActivity($connector2, null, 'activity3');
			$connector3  = null; 
			$connector3s = $authoringService->getConnectorsByActivity($activity3, array('next'));
			foreach($connector3s['next'] as $connector){
				$connector3 = $connector;
				break;
			}
			$this->assertNotNull($connector3);
			
			$activity4 = $authoringService->createSequenceActivity($connector3, null, 'activity4');
			$connector4  = null; 
			$connector4s = $authoringService->getConnectorsByActivity($activity4, array('next'));
			foreach($connector4s['next'] as $connector){
				$connector4 = $connector;
				break;
			}
			$this->assertNotNull($connector4);
		
			
			$activity5 = $authoringService->createSequenceActivity($connector4, null, 'activity5');
			$connector5s = $authoringService->getConnectorsByActivity($activity5, array('next'));
			foreach($connector5s['next'] as $connector){
				if(!is_null($connector)){
					$connector->delete();
				}
			}
			
			//run the process
			$factory = new ProcessExecutionFactory();
			$factory->name = 'Test Process Execution';
			$factory->execution = $processDefinition->uriResource;
			$factory->ownerUri = SYS_USER_LOGIN;
			
			
		
			//init 1st activity
			$proc = $factory->create();
			
			$i = 1;
			while($i <= 5 ){
				$activity = $proc->currentActivity[0];
				$this->assertTrue($activity->label == 'activity'.$i);

				echo "<br><strong>".$activity->label."</strong><br>";
				
				$currentTokens = $this->service->getCurrents($proc->resource);
				$this->assertIsA($currentTokens, 'array');
				foreach($currentTokens as $currentToken){
					echo "Current : ". $currentToken->getLabel()."<br>";
				}
				
				//init execution
				$this->assertTrue($processExecutionService->initCurrentExecution($proc->resource, $activity->resource, $this->currentUser));
				
				$activityExecuction = $activityExecutionService->getExecution($activity->resource, $this->currentUser, $proc->resource);
				$this->assertNotNull($activityExecuction);
				echo "Execution: ".$activityExecuction->getLabel()."<br>";
				
				$token = $this->service->getCurrent($activityExecuction);
				$this->assertNotNull($token);
				echo "Token: ".$token->getLabel()." ".$token->uriResource."<br>";
				
				$tokenActivity = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITY));
				$this->assertNotNull($tokenActivity);
				echo "Token Activity: ".$tokenActivity->getLabel()."<br>";
				
				$tokenActivityExe = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION));
				$this->assertNotNull($tokenActivityExe);
				echo "Token ActivityExecution: ".$tokenActivityExe->getLabel()."<br>";
				
				$tokenUser = $token->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TOKEN_CURRENTUSER));
				$this->assertNotNull($tokenUser);
				echo "Token User: ".$tokenUser->getLabel()."<br>";
				
				//transition to 2nd activity
				$proc->performTransition($activityExecuction->uriResource);
				
				$currentTokens = $this->service->getCurrents($proc->resource);
				$this->assertIsA($currentTokens, 'array');
				
				
				$this->assertFalse($proc->isPaused());
				
				$i++;
			}
			$this->assertTrue($proc->isFinished());
			
			$currentTokens = $this->service->getCurrents($proc->resource);
			foreach($currentTokens as $currentToken){
				$this->assertTrue($this->service->delete($currentToken));
			}
			
			$this->assertTrue($activity1->delete());
			$this->assertTrue($activity2->delete());
			$this->assertTrue($activity3->delete());
			$this->assertTrue($activity4->delete());
			$this->assertTrue($activity5->delete());
			
			$this->assertTrue($connector1->delete());
			$this->assertTrue($connector2->delete());
			$this->assertTrue($connector3->delete());
			$this->assertTrue($connector4->delete());
			
			$this->assertTrue($proc->resource->delete());
			$this->assertTrue($processDefinition->delete());
			
			if(!is_null($this->currentUser)){
				core_kernel_users_Service::logout();
				$this->assertTrue($this->userService->removeUser($this->currentUser));
			}
		}
		catch(common_Exception $ce){
			
			print "<pre>";
			print_r($ce);
			print "</pre>";
			
			$this->fail($ce);
		}
	}
	
}
?>