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
	
	private function getAuthorizedUsersByCountryLanguage($countryCode, $languageCode, $translatorsNb = 0){
		
		$returnValue = array();
		
		if(!empty($this->userLogins)){
			if(isset($this->userLogins[$countryCode])){
				if(isset($this->userLogins[$countryCode][$languageCode])){
					
					$npmLogin = $this->userLogins[$countryCode]['NPM'];
					$reconcilerLogin =  $this->userLogins[$countryCode][$languageCode]['reconciler'];
					$verifierLogin =  $this->userLogins[$countryCode][$languageCode]['verifier'];
					
					$translators = array();
					if($translatorsNb > 0){
						if(!isset($this->userLogins[$countryCode][$languageCode]['translator'])){
							$this->fail('no translators found for the country/language');
						}
						$translatorLogins = $this->userLogins[$countryCode][$languageCode]['translator'];

						for($i = 0; $i < $translatorsNb; $i++){
							if(isset($translatorLogins[$i+1])){
								$translatorLogin = $translatorLogins[$i+1];
								if(isset($this->users[$translatorLogin])){
									$translators[] = $this->users[$translatorLogin]->uriResource;
								}
							}
						}
					}
					
					if(isset($this->users[$npmLogin]) && isset($this->users[$reconcilerLogin]) && isset($this->users[$verifierLogin])){
						$returnValue = array(
							'npm' => $this->users[$npmLogin]->uriResource,
							'reconciler' => $this->users[$reconcilerLogin]->uriResource,
							'verifier' => $this->users[$verifierLogin]->uriResource,
							'translators' => $translators
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
			$this->roles['testDeveloper'] = $roleService->createInstance($roleClass, 'test developers - '.$usec);
			
			
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
			
			$this->userLogins['testDeveloper'] = array();
			$nbTestDevelopers = 3;
			for($i = 1; $i <= $nbTestDevelopers; $i++){
				$this->userLogins['testDeveloper'][$i] = 'testDeveloper_'.$i.'_'.$usec;//test creators
				$this->roleLogins[$this->roles['testDeveloper']->uriResource][] = $this->userLogins['testDeveloper'][$i];
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
						$this->userLogins[$countryCode][$languageCode]['translator'][$i] = 'translator_'.$countryCode.'_'.$languageCode.'_'.$i.'_'.$usec;
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
			
//			var_dump($this->userLogins, $this->roleLogins, $this->users);
//			exit;
		}
			
	}
	
	public function testCreateTranslationProcess(){
		
		if(!$this->createProcess){
			return;
		}
		
		$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$cardinalityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityCardinalityService');
		$transitionRuleService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TransitionRuleService');
		
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
		$this->assertTrue($connectorService->setSplitVariables($connectorSelectTranslators, array($activityTranslate->uriResource => $vars['translator'])));
		
		$nextActivities = $connectorService->getNextActivities($connectorSelectTranslators);
		$this->assertEqual(count($nextActivities), 1);
		$cardinality = reset($nextActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getActivity($cardinality)->uriResource, $activityTranslate->uriResource);
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->uriResource, $this->vars['translatorsCount']->uriResource);
		
		$connectorTranslate = $authoringService->createConnector($activityTranslate);
		$this->assertNotNull($connectorTranslate);
		
		//reconciliation:
		$activityReconciliation = $authoringService->createJoinActivity($connectorTranslate, null, 'Reconciliation', $activityTranslate);
		$prevActivities = $connectorService->getPreviousActivities($connectorTranslate);
		$this->assertEqual(count($prevActivities), 1);
		$cardinality = reset($prevActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getActivity($cardinality)->uriResource, $activityTranslate->uriResource);
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->uriResource, $this->vars['translatorsCount']->uriResource);
		
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
		$activityCorrectVerification = $authoringService->createSequenceActivity($connectorVerifyTranslations, null, 'Correct Verification Issues');
		$this->assertNotNull($activityCorrectVerification);
		$activityService->setAcl($activityCorrectVerification, $aclUser, $vars['reconciler']);

		$connectorCorrectVerification = $authoringService->createConnector($activityCorrectVerification);
		$this->assertNotNull($connectorCorrectVerification);

		//correct layout :
		$activityCorrectLayout = $authoringService->createSequenceActivity($connectorCorrectVerification, null, 'Correct Layout Issues');
		$this->assertNotNull($activityCorrectLayout);
		$activityService->setAcl($activityCorrectLayout, $aclRole, $this->roles['developer']);

		$connectorCorrectLayout = $authoringService->createConnector($activityCorrectLayout);
		$this->assertNotNull($connectorCorrectLayout);
		
		//final check :
		$activityFinalCheck = $authoringService->createSequenceActivity($connectorCorrectLayout, null, 'Final Check');
		$this->assertNotNull($activityFinalCheck);
		$activityService->setAcl($activityFinalCheck, $aclRole, $this->roles['testDeveloper']);

		$connectorFinalCheck = $authoringService->createConnector($activityFinalCheck);
		$this->assertNotNull($connectorFinalCheck);
		
		//if final check ok, go to scoring definition :
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheck, '^layoutCheck == 1');
		$this->assertNotNull($transitionRule);
		
		$activityScoringDefinition = $authoringService->createConditionalActivity($connectorFinalCheck, 'then', null, 'Scoring Definition and Testing');//if ^layoutCheck == 1
		$this->assertNotNull($activityScoringDefinition);
		$activityService->setAcl($activityScoringDefinition, $aclUser, $vars['reconciler']);
		
		$connectorScoringDefinition = $authoringService->createConnector($activityScoringDefinition);
		$this->assertNotNull($connectorScoringDefinition);
		
		
		//if not ok, can go to optional activity to review corrections:
		$connectorFinalCheckElse = $authoringService->createConditionalActivity($connectorFinalCheck, 'else', null, $connectorFinalCheck->getLabel().'_c', true);//if ^layoutCheck != 1
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheckElse, '^layoutCheck == 2');
		$this->assertNotNull($transitionRule);
		
		$activityReviewCorrection = $authoringService->createConditionalActivity($connectorFinalCheckElse, 'then', null, 'Review corrections');//if ^layoutCheck == 2
		$this->assertNotNull($activityReviewCorrection);
		$activityService->setAcl($activityReviewCorrection, $aclUser, $vars['verifier']);
		
		//link review correction back to the final "check activity"
		$connectorReviewCorrections = $authoringService->createConnector($activityReviewCorrection);
		$this->assertNotNull($connectorReviewCorrections);
		$activityFinalCheckBis = $authoringService->createSequenceActivity($connectorReviewCorrections, $activityFinalCheck);
		$this->assertEqual($activityFinalCheck->uriResource, $activityFinalCheckBis->uriResource);
		
		//else return to "correct verification":
		$activityCorrectVerificationBis = $authoringService->createConditionalActivity($connectorFinalCheckElse, 'else', $activityCorrectVerification);//if ^layoutCheck != 2
		$this->assertEqual($activityCorrectVerification->uriResource, $activityCorrectVerificationBis->uriResource);
		//end of if(^layoutCheck == 1) elseif(^layoutCheck == 2) else
		
		//check transition rules:
		$thenActivity = $transitionRuleService->getThenActivity($transitionRule);
		$this->assertNotNull($thenActivity);
		$this->assertEqual($thenActivity->uriResource, $activityReviewCorrection->uriResource);
		$elseActivity = $transitionRuleService->getElseActivity($transitionRule);
		$this->assertNotNull($elseActivity);
		$this->assertEqual($elseActivity->uriResource, $activityCorrectVerification->uriResource);
		
		//scoring verification:
		$activityScoringVerification = $authoringService->createSequenceActivity($connectorScoringDefinition, null, 'Scoring verification');
		$this->assertNotNull($activityScoringVerification);
		$activityService->setAcl($activityScoringVerification, $aclUser, $vars['verifier']);
		
		$connectorScoringVerification = $authoringService->createConnector($activityScoringVerification);
		$this->assertNotNull($connectorScoringVerification);
		
		//final sign off :
		$activityTDSignOff = $authoringService->createSequenceActivity($connectorScoringVerification, null, 'TD Sign Off');
		$this->assertNotNull($activityTDSignOff);
		$activityService->setAcl($activityTDSignOff, $aclRole, $this->roles['testDeveloper']);

		$connectorTDSignOff = $authoringService->createConnector($activityTDSignOff);
		$this->assertNotNull($connectorTDSignOff);
		
		//link back to final check:
		$authoringService->createConditionalActivity($connectorTDSignOff, 'else', $activityFinalCheck);
		$transitionRule = $authoringService->createTransitionRule($connectorTDSignOff, '^finalCheck == 1');
		$this->assertNotNull($transitionRule);
		
		//sign off :
		$activityCountrySignOff = $authoringService->createConditionalActivity($connectorTDSignOff, 'then', null, 'Country Sign Off');
		$activityService->setAcl($activityCountrySignOff, $aclUser, $vars['reconciler']);
		
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
		
		$nbTranslators = 2;//>=1
		$nbLoops = 2;
		
		$loopsCounter = array();
		
		$indexActivityTranslate = 2;//the index of the activity in the process deifnition
		$iterations = $indexActivityTranslate + $nbTranslators +12;
		$this->changeUser($this->userLogins[$countryCode]['NPM']);
		$selectedTranslators = array();
		
		for($i = 1; $i <= $iterations; $i++){
			
			$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$activityExecution = null;
			$activity = null;
			if($i >= $indexActivityTranslate && $i < $indexActivityTranslate+$nbTranslators){
				$this->assertEqual(count($activityExecutions), 2);
				//parallel translation branch:
				foreach($activityExecutions as $activityExec){
					if(!$activityExecutionService->isFinished($activityExec)){
						$activityExecution = $activityExec;
						break;
					}
				}
			}else{
				$this->assertEqual(count($activityExecutions), 1);
				$activityExecution = reset($activityExecutions);
			}
			
			$activity = $activityExecutionService->getExecutionOf($activityExecution);
			
			$this->out("<strong>Iteration $i : ".$activity->getLabel()."</strong>", true);
			$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"', true);
			
			$this->checkAccessControl($activityExecution);
			
			$currentActivityExecution = null;
			
			if($i >= $indexActivityTranslate && $i < $indexActivityTranslate+$nbTranslators){
				
				//we are executing the translation activity:
				$this->assertFalse(empty($selectedTranslators));
				$theTranslator = null;
				foreach($selectedTranslators as $translatorUri){
					$translator = new core_kernel_classes_Resource($translatorUri);
					if($activityExecutionService->checkAcl($activityExecution, $translator)){
						$theTranslator = $translator;
						break;
					}
				}

				$this->assertNotNull($theTranslator);
				$login = (string) $theTranslator->getUniquePropertyValue($loginProperty);
				$this->assertFalse(empty($login));

				$this->bashCheckAcl($activityExecution, array($login));
				$this->changeUser($login);

				$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
				
				//execute service:
				$this->assertTrue($this->executeServiceTranslate(array(
					'translatorUri' => $theTranslator->uriResource
				)));
				
			}else{
				
				$login = '';
				
				//switch to activity's specific check:
				switch ($i) {
					case 1: {
						
						$login = $this->userLogins[$countryCode]['NPM'];
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login));

						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						//execute service:
						$translators = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode, $nbTranslators);
						$selectedTranslators = $translators['translators'];
						$this->assertTrue($this->executeServiceSelectTranslators($selectedTranslators));

						break;
					}
					case $indexActivityTranslate + $nbTranslators: {
						//reconciliation:
						$login = $this->userLogins[$countryCode][$languageCode]['reconciler'];
					}
					case $indexActivityTranslate + $nbTranslators +1: 
					case $indexActivityTranslate + $nbTranslators +5:
					case $indexActivityTranslate + $nbTranslators +11:{
						//verify translations and review corrections:
						//verify translations and review corrections:
						//scoring verification
						if(empty($login)) $login = $this->userLogins[$countryCode][$languageCode]['verifier'];
					}	
					case $indexActivityTranslate + $nbTranslators +2:
					case $indexActivityTranslate + $nbTranslators +7:
					case $indexActivityTranslate + $nbTranslators +10:{
						//correct verification issues:
						//correct verification issues:
						//scoring definition and testing:
						if(empty($login)) $login = $this->userLogins[$countryCode][$languageCode]['reconciler'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}	
					case $indexActivityTranslate + $nbTranslators +3:
					case $indexActivityTranslate + $nbTranslators +8:{
						
						//correct layout, by developers:
						
						$developersLogins = $this->userLogins['developer'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$j = 1;
						foreach(array_rand($developersLogins, 3) as $k){
							
							$this->out("developer no$j ".$developersLogins[$k]." corrects layout", true);
							$this->changeUser($developersLogins[$k]);
							$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
							
							//check if all developers can access the activity, even after it has been taken:
							$this->bashCheckAcl($activityExecution, $developersLogins, array_rand($this->users, 8));
							
							$j++;
						}
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +4:
					case $indexActivityTranslate + $nbTranslators +6:
					case $indexActivityTranslate + $nbTranslators +9:{
						
						//final check:
						$developersLogins = $this->userLogins['testDeveloper'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$j = 1;
						foreach(array_rand($developersLogins, 2) as $k){
							
							$this->out("test developer no$j ".$developersLogins[$k]." makes final check", true);
							$this->changeUser($developersLogins[$k]);
							$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
							
							//check if all developers can access the activity, even after it has been taken:
							$this->bashCheckAcl($activityExecution, $developersLogins, array_rand($this->users, 5));
							
							$j++;
						}
						
						if(!isset($loopsCounter['reviewCorrections'])){
							$loopsCounter['reviewCorrections'] = $nbLoops;
							$this->assertTrue($this->executeServiceLayoutCheck(2));
						}else if(!isset($loopsCounter['correctVerification'])){
							$loopsCounter['correctVerification'] = $nbLoops;
							$this->assertTrue($this->executeServiceLayoutCheck(0));
						}else{
							$this->assertTrue($this->executeServiceLayoutCheck(1));
						}
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +12:{
						
						//TD sign off:
						$developersLogins = $this->userLogins['testDeveloper'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						$currentActivityExecution = $currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
							
						break;
					}
				}
				
			}
			
			//transition to next activity
			$transitionResult = $processExecutionService->performTransition($processInstance, $currentActivityExecution);
			if($i == $indexActivityTranslate + $nbTranslators +12){
				$this->assertEqual(count($transitionResult), 1);
				$this->assertFalse($processExecutionService->isPaused($processInstance));
			}else{
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}
			
			
			$this->out("activity status: ".$activityExecutionService->getStatus($currentActivityExecution)->getLabel());
			$this->out("process status: ".$processExecutionService->getStatus($processInstance)->getLabel());
			
		}
		
		$activityExecutionsData = $processExecutionService->getAllActivityExecutions($processInstance);
		var_dump($activityExecutionsData);
			
		//delete process execution:
		$this->assertTrue($processInstance->exists());
		$this->assertTrue($processExecutionService->deleteProcessExecution($processInstance));
		$this->assertFalse($processInstance->exists());
	}
	
	private function initCurrentActivityExecution($activityExecution, $started = true){
		
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processInstance = $activityExecutionService->getRelatedProcessExecution($activityExecution);
		
		//init execution
		$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
		$this->assertNotNull($activityExecution);
		$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
		$this->assertNotNull($activityExecStatus);
		if($started){
			$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_STARTED);
		}else{
			$this->assertEqual($activityExecStatus->uriResource, INSTANCE_PROCESSSTATUS_RESUMED);
		}
		
		return $activityExecution;
	}
	
	private function bashCheckAcl($activityExecution, $authorizedUsers, $unauthorizedUsers = array()){
		
		$currentUser = $this->currentUser;
		
		if(empty($unauthorizedUsers)){
			$allLogins = array_keys($this->users);//all logins
			$unauthorizedUsers = array_diff($allLogins, $authorizedUsers);
		}else{
			$unauthorizedUsers = array_diff($unauthorizedUsers, $authorizedUsers);
		}
		
		
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processInstance = $activityExecutionService->getRelatedProcessExecution($activityExecution);
		
		foreach($unauthorizedUsers as $login){
			$this->assertTrue($this->changeUser($login));
			$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
		}
		
		foreach($authorizedUsers as $login){
			$this->assertTrue($this->changeUser($login));
			$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
		}
		
		//relog initial user:
		$currentLogin = $currentUser->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
		$this->assertTrue($this->changeUser($currentLogin));
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
	private function executeServiceSelectTranslators($translators = array()){
		
		$returnValue = false;
		
		$this->out("execute service select translators :", true);
		
		//push values:
		$pushedVars = array();
		foreach($translators as $translator){
			$translatorResource = null;
			if($translator instanceof core_kernel_classes_Resource){
				$pushedVars[] = $translator->uriResource;
				$translatorResource = $translator;
			}else if(is_string($translator) && common_Utils::isUri($translator)){
				$pushedVars[] = $translator;
				$translatorResource = new core_kernel_classes_Resource($translator);
			}
			$this->out("selected translator : {$translatorResource->getLabel()} ({$translatorResource->uriResource})");
		}
		$this->assertTrue(count($pushedVars) > 0);
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$this->assertTrue($processVariableService->push('translatorsCount', count($pushedVars)));
		$returnValue = $processVariableService->push('translator', serialize($pushedVars));
		
		return $returnValue;
	}
	
	private function executeServiceTranslate($options = array()){
		
		$returnValue = false;
		
		//check validity of xliff file and VFF
		
		$this->out('executing service translate ', true);
		
		$valid = true;
		if($valid){
			$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
			$this->assertTrue($processVariableService->push('translationFinished', 1));
			$returnValue = true;
		}
		
		return $returnValue;
		
	}
	
	//deprecated:
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
	
	private function executeServiceLayoutCheck($outputCode = 0){
		
		$returnValue = false;
		
		$this->out('executing service layout check with output code : '.$outputCode, true);
		
		$outputCode = intval($outputCode);
		if(in_array($outputCode, array(0, 1, 2))){
			$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
			$returnValue = $processVariableService->edit('layoutCheck', $outputCode);
		}else{
			$this->fail('wrong output code for layout check activity');
		}
		
		return $returnValue;
	}
	
	private function executeServiceFinalSignOff($ok = false){
		
		$this->out("execute service final sign off", true);
		
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