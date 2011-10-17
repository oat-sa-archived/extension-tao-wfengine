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
	 * @var core_kernel_classes_Resource
	 */
	protected $processDefinition = null;
	
	protected $processLabel = 'TranslationProcess';
	
	/**
	 * @var array()
	 */
	protected $userLogins = array();
	protected $users = array();
	protected $roles = array();
	protected $vars = array();
	
	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		parent::setUp();
		$this->userPassword = '123456';
		$this->processLabel = 'TranslationProcess';
		$this->createUsers = true;
		$this->createProcess = true;
		
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
	
	private function getAuthorizedUsersByCountryLanguage($countryCode, $languageCode){
		
		$returnValue = array();
		
		if(!empty($this->userLogins)){
			if(isset($this->userLogins[$countryCode])){
				if(isset($this->userLogins[$countryCode][$languageCode])){
					
					$npmLogin = $this->userLogins[$countryCode]['NPM'];
					$reconcilerLogin =  $this->userLogins[$countryCode][$languageCode]['reconciler'];
					$verifierLogin =  $this->userLogins[$countryCode][$languageCode]['verifier'];
					
					if(isset($this->users[$npmLogin]) && isset($this->users[$reconcilerLogin]) && isset($this->users[$verifierLogin])){
						$returnValue = array(
							'npm' => $this->users[$npmLogin]->uriResource,
							'reconciler' => $this->users[$reconcilerLogin]->uriResource,
							'verifier' => $this->users[$verifierLogin]->uriResource
						);
					}
					
				}
			}
		}
		
		return $returnValue;
	}

	/**
	 * Test generation of users:
	 */
	public function testCreateUsers(){
		
		error_reporting(E_ALL);
		
		if($this->createUsers){
			
			$roleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RoleService');
			
			$usec = time();
			
			$this->userLogins = array();
			$this->users = array();	
			$this->roles = array();
			
			//create roles and users:
			$roleClass = new core_kernel_classes_Class(CLASS_ROLE_WORKFLOWUSERROLE);
			$this->roles['consortium'] = $roleService->createInstance($roleClass, 'consortium - '.$usec);
			$this->roles['NPM'] = $roleService->createInstance($roleClass, 'NPMs - '.$usec);
			$this->roles['translator'] = $roleService->createInstance($roleClass, 'translators - '.$usec);
			$this->roles['reconciler'] = $roleService->createInstance($roleClass, 'reconcilers - '.$usec);
			$this->roles['verifier'] = $roleService->createInstance($roleClass, 'verifiers - '.$usec);
			$this->roles['developer'] = $roleService->createInstance($roleClass, 'developers - '.$usec);
			
			$langCountries = array(
				'LU' => array('fr', 'de', 'lb'),
				'DE' => array('de')
			);
			
			//generate users' logins:
			$this->userLogins = array();
			$this->roleLogins = array();
			
			$this->userLogins['developer'] = array();
			$nbDevelopers = 6;
			for($i = 1; $i <= $nbDevelopers; $i++){
				$this->userLogins['developer'][$i] = 'developer_'.$i.'_'.$usec;//ETS_01, ETS_02, etc.
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
			
			
		}
			
	}
	
	public function testCreateTranslationProcess(){
		
		if(!$this->createProcess){
			return;
		}
		
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		
		//create some process variables:
		$vars = array();
		$varCodes = array(
			'unitUri', //to be initialized
			'countryCode', //to be initialized
			'languageCode', //to be initialized
			'npm', //define the *unique* NPM that can access the activity
			'translatorsCount',//the number of translator, used in split connector
			'translator',//serialized array (the system variable) that will be split during parallel branch creation
			'reconciler',//define the *unique* reconciler that can access the activity
			'verifier',
			'translatorSelected',
			'translationFinished',
			'layoutCheck',
			'finalCheck'
		);
		
		foreach($varCodes as $varCode){
			$vars[$varCode] = $processVariableService->getProcessVariable($varCode, true);
		}
		$this->vars = $vars;
		
		$aclUser = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
		$aclRole = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
		
		$processDefinition = $authoringService->createProcess($this->processLabel, 'For Unit test');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');

		//define activities and connectors

		//Select translators:
		$activitySelectTranslators = $authoringService->createActivity($processDefinition, 'Select Translator');
		$this->assertNotNull($activitySelectTranslators);
		$authoringService->setFirstActivity($processDefinition, $activitySelectTranslators);
		$activityService->setAcl($activitySelectTranslators, $aclUser, $vars['npm']);

		$connectorSelectTranslators = $authoringService->createConnector($activitySelectTranslators);
		$this->assertNotNull($connectorSelectTranslators);
		
		//translate:
		$activityTranslate = $authoringService->createActivity($processDefinition, 'Translate');
		$this->assertNotNull($activityTranslate);
		$activityService->setAcl($activityTranslate, $aclUser, $vars['translator']);
		
		$result = $authoringService->setParallelActivities($connectorSelectTranslators, array($activityTranslate->uriResource => $vars['translatorsCount']));
		$this->assertTrue($result);
		
		$connectorTranslate = $authoringService->createConnector($activityTranslate);
		$this->assertNotNull($connectorTranslate);
		
		//if ok, go to "end translation"
		$transitionRule = $authoringService->createTransitionRule($connectorTranslate, '^translationFinished == 1');
		$this->assertNotNull($transitionRule);
		$activityEndTranslation = $authoringService->createConditionalActivity($connectorTranslate, 'then', null, 'end translation');
		$this->assertNotNull($activityEndTranslation);
		
		$connectorEndTranslation = $authoringService->createConnector($activityEndTranslation);
		$this->assertNotNull($connectorEndTranslation);
		
		//if not ok, go to replace translator:
		$activityReplaceTranslator = $authoringService->createConditionalActivity($connectorTranslate, 'else', null, 'replace translator');
		$this->assertNotNull($activityReplaceTranslator);
		
		$connectorReplaceTranslator = $authoringService->createConnector($activityReplaceTranslator);
		$this->assertNotNull($connectorReplaceTranslator);
		
		//if the translator has been replaced, go back to translate:
		$transitionRule = $authoringService->createTransitionRule($connectorReplaceTranslator, '^translatorsCount == 1');
		$this->assertNotNull($transitionRule);
		$activityTranslateBis = $authoringService->createConditionalActivity($connectorReplaceTranslator, 'then', $activityTranslate);
		$this->assertNotNull($activityTranslateBis);
		$this->assertEqual($activityTranslateBis->uriResource, $activityTranslate->uriResource);
		
		//if no translator has been selected, shortcut to "end translation":
		$activityEndTranslationBis = $authoringService->createConditionalActivity($connectorReplaceTranslator, 'else', $activityEndTranslation);
		$this->assertNotNull($activityEndTranslationBis);
		$this->assertEqual($activityEndTranslationBis->uriResource, $activityEndTranslation->uriResource);
		
		//reconciliation:
//		$activityReconciliation = $authoringService->createActivity($processDefinition, 'Reconciliation');
//		$this->assertNotNull($activityReconciliation);
		$activityReconciliation = $authoringService->createJoinActivity($connectorEndTranslation, null, 'Reconciliation', $activityEndTranslation);
		$this->assertNotNull($activityReconciliation);
		$activityService->setAcl($activityReconciliation, $aclUser, $vars['reconciler']);
		
		$connectorReconciliation = $authoringService->createConnector($activityReconciliation);
		$this->assertNotNull($connectorReconciliation);
		
		//verify translations
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
		$activityService->setAcl($activityCorrectLayout, $aclRole, $this->roles['developer']);

		$connectorCorrectLayout = $authoringService->createConnector($activityCorrectLayout);
		$this->assertNotNull($connectorCorrectLayout);
		
		//final check :
		$activityFinalCheck = $authoringService->createSequenceActivity($connectorCorrectLayout, null, 'Final check');
		$this->assertNotNull($activityFinalCheck);
		$activityService->setAcl($activityFinalCheck, $aclRole, $this->roles['developer']);

		$connectorFinalCheck = $authoringService->createConnector($activityFinalCheck);
		$this->assertNotNull($connectorFinalCheck);
		
		//link it back to "correct verification"
		$activityVerifyLayoutCorrection = $authoringService->createConditionalActivity($connectorFinalCheck, 'else', $activityCorrectVerification);
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheck, '^layoutCheck == 1');
		$this->assertNotNull($transitionRule);
		
		//verify layout correction :
		$activityVerifyLayoutCorrection = $authoringService->createConditionalActivity($connectorFinalCheck, 'then', null, 'Correct Layout');
		$this->assertNotNull($activityVerifyLayoutCorrection);
		$activityService->setAcl($activityVerifyLayoutCorrection, $aclUser, $vars['verifier']);

		$connectorVerifyLayoutCorrection = $authoringService->createConnector($activityVerifyLayoutCorrection);
		$this->assertNotNull($connectorVerifyLayoutCorrection);
		
		//scoring definition :
		$activityScoringDefinition = $authoringService->createSequenceActivity($connectorVerifyLayoutCorrection, null, 'Scoring definition');
		$this->assertNotNull($activityScoringDefinition);
		$activityService->setAcl($activityScoringDefinition, $aclUser, $vars['reconciler']);
		
		$connectorScoringDefinition = $authoringService->createConnector($activityScoringDefinition);
		$this->assertNotNull($connectorScoringDefinition);
		
		//scoring verification:
		$activityScoringVerification = $authoringService->createSequenceActivity($connectorScoringDefinition, null, 'Scoring verification');
		$this->assertNotNull($activityScoringVerification);
		$activityService->setAcl($activityScoringVerification, $aclUser, $vars['verifier']);
		
		$connectorScoringVerification = $authoringService->createConnector($activityScoringVerification);
		$this->assertNotNull($connectorScoringVerification);
		
		//final sign off :
		$activityFinalSignOff = $authoringService->createSequenceActivity($connectorScoringVerification, null, 'Final Sign Off');
		$this->assertNotNull($activityFinalSignOff);
		$activityService->setAcl($activityFinalSignOff, $aclRole, $this->roles['developer']);

		$connectorFinalSignOff = $authoringService->createConnector($activityFinalSignOff);
		$this->assertNotNull($connectorFinalSignOff);
		
		//link back to final check:
		$authoringService->createConditionalActivity($connectorFinalSignOff, 'else', $activityFinalCheck);
		$transitionRule = $authoringService->createTransitionRule($connectorFinalSignOff, '^finalCheck == 1');
		$this->assertNotNull($transitionRule);
		
		//sign off :
		$activitySignOff = $authoringService->createConditionalActivity($connectorFinalSignOff, 'then', null, 'Sign Off');
		$activityService->setAcl($activitySignOff, $aclUser, $vars['reconciler']);
		
		//end of process definition
		
		$this->processDefinition = $processDefinition;
	}
	
	public function testExecuteTranslationProcess(){
		
		$itemUri = 'myItemUri';
		$countryCode = 'LU';
		$languageCode = 'de';
		
		$simulationOptions = array(
			'backRepeat' => 0,//O: do not back when possible
			'loopRepeat' => 1
		);
		
		if(!$this->processDefinition instanceof core_kernel_classes_Resource){
			//try to find it:
			$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$translationProcesses = $processClass->searchInstances(array(RDFS_LABEL => (string) $this->processLabel), array('like'=>false));
			if(!empty($translationProcesses)){
				$this->processDefinition = array_pop($translationProcesses);
			}
		}
			
		if(!$this->processDefinition instanceof core_kernel_classes_Resource){
			$this->fail('No process definition found to be executed');
		}
		
		$this->executeTranslationProcess($this->processDefinition, $itemUri, $countryCode, $languageCode, $simulationOptions);
	}
	
	private function executeTranslationProcess($processDefinition, $itemUri, $countryCode, $languageCode, $simulationOptions){
		
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		
		$processExecName = 'Test Translation Process Execution';
		$processExecComment = 'created by '.__CLASS__.'::'.__METHOD__;
		
		$users = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode);
		
		if(empty($users)){
			$this->fail("cannot find authorized the npm, verifier and reconciler for this country-language : {$countryCode}/{$languageCode}");
			return;
		}
		
		$initVariables = array(
			$this->vars['unitUri']->uriResource => $itemUri,
			$this->vars['countryCode']->uriResource => $countryCode,
			$this->vars['languageCode']->uriResource => $languageCode,
			$this->vars['npm']->uriResource => $users['npm'],
			$this->vars['reconciler']->uriResource => $users['reconciler'],
			$this->vars['verifier']->uriResource => $users['verifier']
		);
			
		$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $initVariables);
		$this->assertEqual($processDefinition->uriResource, $processExecutionService->getExecutionOf($processInstance)->uriResource);
		$this->assertEqual($processDefinition->uriResource, $processExecutionService->getExecutionOf($processInstance)->uriResource);

		$this->assertTrue($processExecutionService->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);

		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($currentActivityExecutions), 1);
		var_dump($activityExecutionService->getExecutionOf(reset($currentActivityExecutions))->getLabel());

		$this->out("<strong>Forward transitions:</strong>", true);
		
		/*
		 * select translators
		 * translator 1
		 * replace translator 1
		 * end translation 1
		 * translator 2
		 * end translation 2
		 * reconciliation
		 * verify translations
		 * correct verification
		 * correct layout
		 * final check
		 * correct verification
		 * correct layout 
		 * final check
		 * verify layout correction
		 * scoring definition
		 * scoring verification
		 * final sign off
		 * final check
		 * verify layout correction
		 * scoring definition
		 * scoring verification
		 * final sign off
		 * sign off
		 */
		
		$iterations = 24;
		$iterations = 3;
		$this->changeUser($this->userLogins[$countryCode]['NPM']);
		
		for($i = 0; $i<$iterations; $i++){
			
			$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$this->assertEqual(count($activityExecutions), 1);
			$activityExecution = reset($activityExecutions);
			$activity = $activityExecutionService->getExecutionOf($activityExecution);
			
			$this->out("<strong>".$activity->getLabel()."</strong>", true);
			$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"', true);
			
			$this->checkAccessControl($activityExecution);
			switch($i){
				case 1:{
					break;
				}
			}	
			
		}
		
	}
	
	/*
	 * Available process vars:
	 * 'unitUri', //to be initialized
		'countryCode', //to be initialized
		'languageCode', //to be initialized
		'npm', //define the *unique* NPM that can access the activity
		'translatorsCount',//the number of translator, used in split connector
		'translator',//serialized array (the system variable) that will be split during parallel branch creation
		'reconciler',//define the *unique* reconciler that can access the activity
		'verifier',
		'translatorSelected',
		'translationFinished',
		'layoutCheck',
		'finalCheck'
	 */
	private function executeServiceTranslation($translators = array()){
		
		$returnValue = false;
		
		//push values:
		$pushedVars = array();
		foreach($translators as $translator){
			if($translator instanceof core_kernel_classes_Resource){
				$pushedVars[] = $translator->uriResource;
			}
		}
		$this->assertTrue(count($pushedVars) > 0);
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$this->assertTrue( $processVariableService->push('translatorsCount', count($pushedVars)) );
		$returnValue = $processVariableService->push('translator', serialize($pushedVars));
		
		return $returnValue;
	}
	
	private function executeServiceReplaceTranslator($replacement = null){
		
		$returnValue = false;
		
		$translatorRole = $this->roles['translator'];
		
		if($replacement instanceof core_kernel_classes_Resource){
			if($replacement->hasType(new core_kernel_classes_Class($translatorRole->uriResource))){
				$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
				$returnValue = $processVariableService->edit('translator', $replacement->uriResource);
			}
		}
		
		//if return false, no replacement assigned!
		
		return $returnValue;
	}
	
	private function executeServiceLayoutCheck($ok = false){
		
		$returnValue = $this->pushBooleanVariable('layoutCheck', $ok);
		return $returnValue;
		
	}
	
	private function executeServiceFinalSignOff($ok = false){
		
		$returnValue = $this->pushBooleanVariable('finalCheck', $ok);
		return $returnValue;
		
	}
	
	private function pushBooleanVariable($variableCode, $ok = false){
		
		$returnValue = false;
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		
		if((bool) $ok){
			$returnValue = $processVariableService->push($variableCode, 1);
		}else{
			$returnValue = $processVariableService->push($variableCode, 0);
		}
		
		return $returnValue;
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
		
		if($this->processDefinition instanceof core_kernel_classes_Resource){
			$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
			$this->assertTrue($authoringService->deleteProcess($this->processDefinition));
			$this->assertFalse($this->processDefinition->exists());
		}
		
		if(!empty($this->vars)){
			foreach($this->vars as $variable){
				$this->assertTrue($variable->delete());
			}
		}
		
	}
}
?>