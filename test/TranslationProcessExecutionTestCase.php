<?php
require_once dirname(__FILE__) . '/wfEngineServiceTest.php';

/**
 * Test the execution of the PISA translation process
 * 
 * @author Somsack Sipasseuth, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class TranslationProcessExecutionTestCase extends wfEngineServiceTest {
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $currentUser = null;
	
	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		parent::setUp();
		$this->userPassword = 'pisa2015';
	}
	
	public function tearDown() {
		
    }
	
	/**
	 * Recursive create users from their logins:
	 */
	private function createUsers($usersLogin){
		foreach($usersLogin as $logins){
			if(is_string($logins)){
				 $createdUser = $this->createUser($logins);
				 $this->assertIsA($createdUser, 'core_kernel_classes_Resource');
				 $createdUser->setLabel($logins);
				 $this->users[$logins] = $createdUser;
			}else{
				$this->createUsers($logins);
			}
		}
	}
	
	/**
	 * Test generation of users:
	 */
	public function testCreateUsers(){
		
		error_reporting(E_ALL);
		
		try{
			
			$roleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RoleService');
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
			$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
			
			$usec = time();
			
			$this->userLogins = array();
			$this->users = array();	
			$this->roles = array();
			
			//create roles and users:
			$roleClass = new core_kernel_classes_Class(CLASS_ROLE_WORKFLOWUSERROLE);
			$this->roles['NPM'] = $roleService->createInstance($roleClass, 'NPMs - '.$usec);
			$this->roles['translator'] = $roleService->createInstance($roleClass, 'translators - '.$usec);
			$this->roles['reconciler'] = $roleService->createInstance($roleClass, 'reconcilers - '.$usec);
			$this->roles['verifier'] = $roleService->createInstance($roleClass, 'verifiers - '.$usec);
			$this->roles['developer'] = $roleService->createInstance($roleClass, 'developers - '.$usec);
			$this->roles['testDeveloper'] = $roleService->createInstance($roleClass, 'test developers - '.$usec);
			
			$langCountries = array(
				'LU' => array('fr', 'de', 'lb'),
				'DE' => array('de')
			);
			
			//generate users' logins:
			$this->userLogins = array();
			$this->roleLogins = array();
			
			$this->userLogins['developer'] = array();
			$nbDevelopers = 5;
			for($i = 1; $i <= $nbDevelopers; $i++){
				$this->userLogins['developer'][$i] = 'developer_'.$i.'_'.$usec;
				$this->roleLogins[$this->roles['developer']->uriResource][] = $this->userLogins['developer'][$i];
			}
			
			$nbTranslatorsByCountryLang = 3;
			foreach($langCountries as $countryCode => $languageCodes){
				$this->userLogins[$countryCode] = array();
				
				//one NPM by country
				$this->userLogins[$countryCode]['NPM'] = 'NPM_'.$countryCode.'_'.$usec;
				$this->roleLogins[$this->roles['NPM']->uriResource][] = $this->userLogins[$countryCode]['NPM'];
				
				foreach($languageCodes as $languageCode){
					
					//one reconciler and verifier by country-language
					$this->userLogins[$countryCode][$languageCode] = array(
						'translator' => array(),
						'reconciler' => 'reconciler_'.$countryCode.'_'.$languageCode.'_'.$usec,
						'verifier' => 'verifier_'.$countryCode.'_'.$languageCode.'_'.$usec
					);
					$this->roleLogins[$this->roles['reconciler']->uriResource][] = $this->userLogins[$countryCode][$languageCode]['reconciler'];
					$this->roleLogins[$this->roles['verifier']->uriResource][] = $this->userLogins[$countryCode][$languageCode]['verifier'];
					
					//as many translators as wanted:
					for($i = 1; $i <= $nbTranslatorsByCountryLang; $i++){
						$this->userLogins[$countryCode][$languageCode]['translator'][$i] = 'translator_'.$countryCode.'_'.$languageCode.'_'.$usec;
						$this->roleLogins[$this->roles['translator']->uriResource][] = $this->userLogins[$countryCode][$languageCode]['translator'][$i];
					}
				}
			}
			
			$this->createUsers($this->userLogins);
			
			foreach($this->roleLogins as $roleUri => $usrs){
				$role = new core_kernel_classes_Resource($roleUri);
				$userUris = array();
				foreach($usrs as $login){
					if(isset($this->users[$login])){
						$userUris[] = $this->users[$login]->uriResource;
					}
				}
				$this->assertTrue($roleService->setRoleToUsers($role, $userUris));
			}
			
//			var_dump($this->userLogins, $this->roleLogins);
			
			return;
			
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
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$processVariableService->push($role_processVar_key, $roleB->uriResource);
						break;
					}
					case 2:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER, $roleB:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$processVariableService->push($user_processVar_key, $user2->uriResource);
						break;
					}
					case 3:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 4:{
						//INSTANCE_ACL_USER, $user2:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 5:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB:
						//only user5 can access it normally:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 6:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY, $roleA:
						//only the user of $roleA that executed (the initial acivity belongs to user2:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
				}
				
				
				//init execution
				$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
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
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 2:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER, $roleB:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 3:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 4:{
						//INSTANCE_ACL_USER, $user2:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 5:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB:
						//only user5 can access it normally:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 6:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY, $roleA:
						//only the user of $roleA that executed (the initial acivity belongs to user2:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					
				}
				
				//init execution
				$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
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
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						//TODO:to be modified after "back"
						$processVariableService->push($role_processVar_key, $roleB->uriResource);
						
						break;
					}
					case 2:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER, $roleB:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						//TODO:to be modified after "back"
						$processVariableService->push($user_processVar_key, $user2->uriResource);
						
						break;
					}
					case 3:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
				
						$this->assertTrue($this->changeUser($users[4]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 4:{
						//INSTANCE_ACL_USER, $user2:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 5:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB:
						//only user5 can access it normally:
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[5]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					case 6:{
						//INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY, $roleA:
						//only the user of $roleA that executed (the initial acivity belongs to user2:
						
						$this->assertTrue($this->changeUser($users[1]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[3]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[6]));
						$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						$this->assertTrue($this->changeUser($users[2]));
						$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
						$this->assertNotNull($processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser));
						
						break;
					}
					
				}
				
				//init execution
				$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
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
			$user_processVar->delete();
			$role_processVar->delete();
		}
		catch(common_Exception $ce){
			$this->fail($ce);
		}
	}
	
	public function testCreateTranslationProcess(){
		
		$roleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RoleService');
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		
		//TEST PLAN :
		//INSTANCE_ACL_ROLE, $roleA
		//INSTANCE_ACL_ROLE_RESTRICTED_USER, $roleB
		//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB (assigned dynamically via process var $role_processVar in activity1)
		//INSTANCE_ACL_USER, $user2	(assigned dynamically via process var $user_processVar in activity2)
		//INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED, $roleB (assigned dynamically via process var $role_processVar in activity1)
		//INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY, $roleA
		
		//create some process variables:
		$vars = array();
		$varCodes = array(
			'npm', //define the *unique* NPM that can access the activity
			'translator',//seerialized array (the system variable) that will be split during parallel branch creation
			'reconciler',//define the *unique* reconciler that can access the activity
			'verifier'
		);
		foreach($varCodes as $varCode){
			$vars[$varCode] = $processVariableService->getProcessVariable($varCode, true);
		}
		
		return;
		
		//create a new process def
		$typeSequential = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
		$typeConditional = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$typeSplit = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL);
		$typeJoin = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_JOIN);
		
		$aclUser = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
		$aclRole = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
		$aclRoleRestricted = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER);
		
		$processDefinition = $authoringService->createProcess('TranslationProcess', 'For Unit test');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');

		//define activities and connectors

		//Select translator:
		$activitySelectTranslators = $authoringService->createActivity($processDefinition, 'Select Translator');
		$this->assertNotNull($activitySelectTranslators);
		$authoringService->setFirstActivity($processDefinition, $activitySelectTranslators);

		$activityService->setAcl($activitySelectTranslators, $aclUser, $vars['npm']);

		$connectorSelectTranslators = $authoringService->createConnector($activitySelectTranslators);
		$this->assertNotNull($connectorSelectTranslators);
		
		//select translators:
		$activityTranslate = $authoringService->createSequenceActivity($connectorSelectTranslators, null, 'Translate Item');
		$this->assertNotNull($activityTranslate);
		$activityService->setAcl($activityTranslate, $aclUser, $vars['translator']);
		
		//reconciliation:
		$activityReconciliation = $authoringService->createActivity($processDefinition, 'Reconciliation');
		$this->assertNotNull($activityReconciliation);
		
		$connectorTranslate = $authoringService->createConnector($activityTranslate);
		$result = $authoringService->setParallelActivities($connectorTranslate, array($activityReconciliation->uriResource => $vars['reconciler']));
		$this->assertTrue($result);

		//verify translations
		$connectorReconciliation = $authoringService->createConnector($activityReconciliation);
		$this->assertNotNull($connectorReconciliation);
		
		$activityVerifyTranslations = $authoringService->createSequenceActivity($connectorReconciliation, null, 'Verify Translations');
		$this->assertNotNull($activityVerifyTranslations);
		$activityService->setAcl($activityVerifyTranslations, $aclUser, $vars['verifier']);

		$connectorVerifyTranslations = $authoringService->createConnector($activityVerifyTranslations);
		$this->assertNotNull($connectorVerifyTranslations);

		//correct verification
		$activityCorrectVerification = $authoringService->createSequenceActivity($connectorVerifyTranslations, null, 'Correct Verification');
		$this->assertNotNull($activityCorrectVerification);
		$activityService->setAcl($activityCorrectVerification, $aclUser, $vars['reconciler']);

		$connectorCorrectVerification = $authoringService->createConnector($activityCorrectVerification);
		$this->assertNotNull($connectorCorrectVerification);

		//correct layout :
		$activityCorrectLayout = $authoringService->createSequenceActivity($connectorCorrectVerification, null, 'Correct Layout');
		$this->assertNotNull($activityCorrectLayout);
		$activityService->setAcl($activityCorrectLayout, $aclRole, $vars['developer']);

		$connectorCorrectLayout = $authoringService->createConnector($activityCorrectLayout);
		$this->assertNotNull($connectorCorrectLayout);
		
		return;
		
		//final check :
		$activityFinalCheck = $this->authoringService->createConditionalActivity($connectorCorrectLayout, 'then', null, 'Final Check');
		$activityService->setAcl($activityFinalCheck, $aclRole, $vars['developer']);
		
		$this->authoringService->createConditionalActivity($connectorCorrectLayout, 'else', $activityCorrectVerification);
			
		$connectorFinalCheck = $authoringService->createConnector($activityFinalCheck);
		$this->assertNotNull($connectorFinalCheck);
		
		//check:
		
		//scoring definition :
		$activityScoringDefinition = $this->authoringService->createConditionalActivity($connectorCorrectLayout, 'then', null, 'Scoring definition');
	
		$activityService->setAcl($activityScoringDefinition, $aclUser, $vars['reconciler']);
		
		$connectorScoringDefinition = $authoringService->createConnector($activityScoringDefinition);
		$this->assertNotNull($connectorScoringDefinition);
		
		//scoring verification:
		
		//final sign off :
		$activityFinalSignOff = $authoringService->createSequenceActivity($connectorScoringDefinition, null, 'Final Sign Off');
		$this->assertNotNull($activityFinalSignOff);
		$activityService->setAcl($activityFinalSignOff, $aclRole, $vars['developer']);

		$connectorFinalSignOff = $authoringService->createConnector($activityFinalSignOff);
		$this->assertNotNull($connectorFinalSignOff);
		
		//sign off :
		$activitySignOff = $this->authoringService->createConditionalActivity($connectorFinalSignOff, 'then', null, 'Sign Off');
		$activityService->setAcl($activitySignOff, $aclUser, $vars['reconciler']);
		
		$this->authoringService->createConditionalActivity($connectorFinalSignOff, 'else', $activityFinalCheck);
	}
	
	public function testExecuteTranslationProcess(){
		
	}
	
	public function testDeleteCreatedResources(){
		
		if(!empty($this->users)){
			foreach($this->users as $user){
				$this->assertTrue($user->delete());
			}
		}
		
		if(!empty($this->roles)){
			foreach($this->roles as $role){
				$this->assertTrue($role->delete());
			}
		}
		
	}
}
?>