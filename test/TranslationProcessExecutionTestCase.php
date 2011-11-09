<?php
require_once dirname(__FILE__) . '/wfEngineServiceTest.php';

/**
 * Test the execution of a complex translation process
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
	protected $processDefinition = null;
	protected $processLabel = 'TranslationProcess';
	
	/**
	 * @var core_kernel_classes_Property
	 */
	protected $userProperty = null;
	
	/**
	 * @var core_kernel_versioning_Repo
	 */
	protected $defaultRepository = null;
	
	/**
	 * @var array()
	 */
	protected $userLogins = array();
	protected $users = array();
	protected $roles = array();
	protected $vars = array();
	protected $units = array();
	protected $processExecutions = array();

	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		parent::setUp();
		$this->userPassword = '123456';
		$this->processLabel = 'TranslationProcess';
		$this->createUsers = true;
		$this->createProcess = true;
		$this->langCountries = array(
			'LU' => array('fr', 'de', 'lb'),
			'DE' => array('de')
		);
		
		$this->userProperty = new core_kernel_classes_Property(LOCAL_NAMESPACE.'#translationUser');
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
			foreach($this->langCountries as $countryCode => $languageCodes){
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
	
	private function getPropertyName($type, $countryCode, $langCode){
		return 'Property_'.strtoupper($type).'_'.strtoupper($countryCode).'_'.strtolower($langCode);
	}
	
	private function getFileName($unitLabel, $countryCode, $langCode, $type, core_kernel_classes_Resource $user = null){
		
		$fileName = $unitLabel.'_'.strtoupper($countryCode).'_'.strtolower($langCode);
		if(!is_null($user)){
			$fileName .= '_'.$user->getLabel();
		}
		$fileName .= '.'.strtolower($type);
		
		return $fileName;
	}
	
	private function createItemFile($type, $content = '', $user = null){
		
		$returnValue = null;
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			//create a working file for that user:
			$fileName = $this->getFileName($unit->getLabel(), $countryCode, $languageCode, $type, $user);
			$file = core_kernel_versioning_File::create($fileName, '/', $this->getDefaultRepository());
			$this->assertIsA($file, 'core_kernel_versioning_File');
			
			//set file content:
			if(!empty($content)){
				$this->assertTrue($file->setContent($content));
			}else{
				$this->assertTrue($file->setContent(strtoupper($type) . '" for country "' . $countryCode . '" and language "' . $languageCode . '" : \n'));
			}
			
			$this->assertTrue($file->add());
			$this->assertTrue($file->commit());
			
			$unit->setPropertyValue($this->properties[$this->getPropertyName($type, $countryCode, $languageCode)], $file);
			
			if(!is_null($user)){
				$file->setPropertyValue($this->userProperty, $user);
			}
			
			$this->files[$fileName] = $file;
			
			$returnValue = $file;
		}
		
		return $returnValue;
	}
	
	private function getItemFile(core_kernel_classes_Resource $item, $type, $countryCode, $langCode, core_kernel_classes_Resource $user = null){
		
		$returnValue = null;
		
		if(!isset($this->properties[$this->getPropertyName($type, $countryCode, $langCode)])){
			$this->fail("The item property does not exist for the item {$item->getLabel()} ({$item->uriResource}) : $type, $countryCode, $langCode ");
			return $returnValue;
		}
		
		$file = null;
		if(in_array(strtolower($type), array('xliff_working', 'vff_working'))){
			if(is_null($user)){
				$this->fail('no user given');
				return $returnValue;
			}
			
			$values = $item->getPropertyValues($this->properties[$this->getPropertyName($type, $countryCode, $langCode)]);
			foreach($values as $uri){
				if(common_Utils::isUri($uri)){
					$aFile = new core_kernel_versioning_File($uri);
					$assignedUser = $aFile->getUniquePropertyValue($this->userProperty);
					if($assignedUser->uriResource == $user->uriResource){
						$file = $aFile;
						break;
					}
				}
			}
			
		}else{
			$values = $item->getPropertyValues($this->properties[$this->getPropertyName($type, $countryCode, $langCode)]);
			$this->assertEqual(count($values), 1);
			$file = new core_kernel_versioning_File(reset($values));
		}
		
		if(!is_null($file) && $file->isVersioned()){
			$returnValue = $file;
		}else{
			$this->fail("Cannot get the versioned {$type} file in {$countryCode}_{$langCode} for the item {$item->getLabel()} ({$item->uriResource})");
		}
		
		return $returnValue;
	}
	
	// Get the default repository of the TAO instance
	private function getDefaultRepository(){
		
		$repository = null;
		
		if(!is_null($this->defaultRepository) && $this->defaultRepository instanceof core_kernel_versioning_Repository){
			$repository = $this->defaultRepository;
		}else{
			$versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
			$repositories = $versioningRepositoryClass->getInstances();
			$repository = null;

			if (!count($repositories)) {
				throw new Exception('no default repository exists in TAO');
			}else {
				$repository = array_pop($repositories);
				$repository = new core_kernel_versioning_Repository($repository->uriResource);
				$this->defaultRepository = $repository;
			}
		}
		
		
		return $repository;
	}
	
	public function createTranslationProperty($type, $countryCode = '', $langCode = '', $class = null){
		
		$property = null;
		
		if(is_null($class) && !is_null($this->itemClass)){
			$class = $this->itemClass;
		}
		
		if(!is_null($class)){
			
			if(!empty($countryCode) && !empty($langCode)){
				$label = $this->getPropertyName($type, $countryCode, $langCode);
			}else{
				$label = $type;
			}
			
			$uri = LOCAL_NAMESPACE.'#'.$label;
			
			$property = new core_kernel_classes_Property($uri);
			
			if(!$property->exists()){
				$propertyClass = new core_kernel_classes_Class(RDF_PROPERTY,__METHOD__);
				$propertyInstance = $propertyClass->createInstance($label, '', $uri);
				$property = new core_kernel_classes_Property($propertyInstance->uriResource,__METHOD__);
			}

			if(!$class->setProperty($property)){
				throw new common_Exception('problem creating property : cannot set property to class');
			}
			
			$this->properties[$label] = $property;
		}else{
			throw new common_Exception('problem creating property : no target class given');
		}
		
		return $property;
	}
	
	public function testCreateUnits(){
		
		$unitNames = array('unit01', 'unit02', 'unit03');
		$this->itemClass = null;
		$this->units = array();
		$this->properties = array();
		$this->files = array();
		
		$classUri = LOCAL_NAMESPACE.'#TranslationItemsClass';
		$translationClass = new core_kernel_classes_Class($classUri);
		if(!$translationClass->exists()){
			$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
			$translationClass = $itemClass->createSubClass('Translation Items', 'created for translation process execution test case', $classUri);
			$this->assertIsA($translationClass, 'core_kernel_classes_Class');
		}
		$this->itemClass = $translationClass;
		
		$unitNames = array_unique($unitNames);
		foreach($unitNames as $unitName){
			
			//create unit:
			$this->units[$unitName] = $translationClass->createInstance($unitName, 'created for translation process execution test case');
			$this->assertNotNull($this->units[$unitName]);
			if(GENERIS_VERSIONING_ENABLED){
				foreach ($this->langCountries as $countryCode => $languageCodes){

					foreach ($languageCodes as $langCode){
						
						$this->assertIsA($this->createTranslationProperty('xliff', $countryCode, $langCode),'core_kernel_classes_Property');
						$this->assertIsA($this->createTranslationProperty('xliff_working', $countryCode, $langCode),'core_kernel_classes_Property');
						$this->assertIsA($this->createTranslationProperty('vff', $countryCode, $langCode),'core_kernel_classes_Property');
						$this->assertIsA($this->createTranslationProperty('vff_working', $countryCode, $langCode),'core_kernel_classes_Property');

						foreach(array('xliff', 'vff') as $fileType){
							$fileName = $this->getFileName($unitName, $countryCode, $langCode, $fileType);
							$file = core_kernel_versioning_File::create($fileName, '/', $this->getDefaultRepository());
							$this->assertIsA($file, 'core_kernel_versioning_File');
							$this->assertTrue($file->setContent(strtoupper($fileType).' for country "' . $countryCode . '" and language "' . $langCode . '" : \n'));
							$this->assertTrue($file->add());
							$this->assertTrue($file->commit());

							$this->assertTrue($this->units[$unitName]->setPropertyValue($this->properties[$this->getPropertyName($fileType, $countryCode, $langCode)], $file));
							
							$values = $this->units[$unitName]->getPropertyValues($this->properties[$this->getPropertyName($fileType, $countryCode, $langCode)]);
							$this->assertEqual(count($values), 1);
			
							$this->files[$fileName] = $file;
						}
					}

				}
			}
		}
		
//		var_dump($this->units, $this->properties, $this->files);
		
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
			'finalCheck',
			'xliff',//holds the current xliff svn revision number
			'vff',//holds the current vff svn revision number
			'xliff_working',//holds the current working xliff svn revision number
			'vff_working',//holds the current working vff svn revision number
			'workingFiles'//holds the working versions of the xliff and vff files, plus their revision number, in an serialized array()
		);
		//"workingFiles" holds the working versions of the xliff and vff files, plus their revision number, in an serialized array()
		//during translation: workingFiles = array('user'=>#007, 'xliff' => array('uri' => #123456, 'revision'=>3), 'vff'=> array('uri' => #456789, 'revision'=>5))
		
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
		$activityService->setControls($activitySelectTranslators, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorSelectTranslators = $authoringService->createConnector($activitySelectTranslators);
		$this->assertNotNull($connectorSelectTranslators);
		
		//translate:
		$activityTranslate = $authoringService->createActivity($processDefinition, 'Translate');
		$this->assertNotNull($activityTranslate);
		$activityService->setAcl($activityTranslate, $aclUser, $vars['translator']);
		$activityService->setControls($activityTranslate, array(INSTANCE_CONTROL_FORWARD));
		
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
		$activityService->setControls($activityReconciliation, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorReconciliation = $authoringService->createConnector($activityReconciliation);
		$this->assertNotNull($connectorReconciliation);
		
		//verify translations
		$activityVerifyTranslations = $authoringService->createSequenceActivity($connectorReconciliation, null, 'Verify Translations');
		$this->assertNotNull($activityVerifyTranslations);
		$activityService->setAcl($activityVerifyTranslations, $aclUser, $vars['verifier']);
		$activityService->setControls($activityVerifyTranslations, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorVerifyTranslations = $authoringService->createConnector($activityVerifyTranslations);
		$this->assertNotNull($connectorVerifyTranslations);

		//correct verification
		$activityCorrectVerification = $authoringService->createSequenceActivity($connectorVerifyTranslations, null, 'Correct Verification Issues');
		$this->assertNotNull($activityCorrectVerification);
		$activityService->setAcl($activityCorrectVerification, $aclUser, $vars['reconciler']);
		$activityService->setControls($activityCorrectVerification, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorCorrectVerification = $authoringService->createConnector($activityCorrectVerification);
		$this->assertNotNull($connectorCorrectVerification);

		//correct layout :
		$activityCorrectLayout = $authoringService->createSequenceActivity($connectorCorrectVerification, null, 'Correct Layout Issues');
		$this->assertNotNull($activityCorrectLayout);
		$activityService->setAcl($activityCorrectLayout, $aclRole, $this->roles['developer']);
		$activityService->setControls($activityCorrectLayout, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorCorrectLayout = $authoringService->createConnector($activityCorrectLayout);
		$this->assertNotNull($connectorCorrectLayout);
		
		//final check :
		$activityFinalCheck = $authoringService->createSequenceActivity($connectorCorrectLayout, null, 'Final Check');
		$this->assertNotNull($activityFinalCheck);
		$activityService->setAcl($activityFinalCheck, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityFinalCheck, array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD));
		
		$connectorFinalCheck = $authoringService->createConnector($activityFinalCheck);
		$this->assertNotNull($connectorFinalCheck);
		
		//if final check ok, go to scoring definition :
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheck, '^layoutCheck == 1');
		$this->assertNotNull($transitionRule);
		
		$activityScoringDefinition = $authoringService->createConditionalActivity($connectorFinalCheck, 'then', null, 'Scoring Definition and Testing');//if ^layoutCheck == 1
		$this->assertNotNull($activityScoringDefinition);
		$activityService->setAcl($activityScoringDefinition, $aclUser, $vars['reconciler']);
		$activityService->setControls($activityScoringDefinition, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorScoringDefinition = $authoringService->createConnector($activityScoringDefinition);
		$this->assertNotNull($connectorScoringDefinition);
		
		
		//if not ok, can go to optional activity to review corrections:
		$connectorFinalCheckElse = $authoringService->createConditionalActivity($connectorFinalCheck, 'else', null, $connectorFinalCheck->getLabel().'_c', true);//if ^layoutCheck != 1
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheckElse, '^layoutCheck == 2');
		$this->assertNotNull($transitionRule);
		
		$activityReviewCorrection = $authoringService->createConditionalActivity($connectorFinalCheckElse, 'then', null, 'Review corrections');//if ^layoutCheck == 2
		$this->assertNotNull($activityReviewCorrection);
		$activityService->setAcl($activityReviewCorrection, $aclUser, $vars['verifier']);
		$activityService->setControls($activityReviewCorrection, array(INSTANCE_CONTROL_FORWARD));
		
		//link review correction back to the final "check activity"
		$connectorReviewCorrections = $authoringService->createConnector($activityReviewCorrection);
		$this->assertNotNull($connectorReviewCorrections);
		$activityFinalCheckBis = $authoringService->createSequenceActivity($connectorReviewCorrections, $activityFinalCheck);
		$this->assertEqual($activityFinalCheck->uriResource, $activityFinalCheckBis->uriResource);
		
		//if still not ok, go to correct layout:
		$connectorFinalCheckElseElse = $authoringService->createConditionalActivity($connectorFinalCheckElse, 'else', null, $connectorFinalCheckElse->getLabel().'_c', true);//if ^layoutCheck != 2
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheckElseElse, '^layoutCheck == 3');
		$this->assertNotNull($transitionRule);
		
		//else return to "correct verification":
		$activityCorrectVerificationBis = $authoringService->createConditionalActivity($connectorFinalCheckElseElse, 'then', $activityCorrectVerification);//if ^layoutCheck == 3
		$this->assertEqual($activityCorrectVerification->uriResource, $activityCorrectVerificationBis->uriResource);
			
		$activityCorrectLayoutBis = $authoringService->createConditionalActivity($connectorFinalCheckElseElse, 'else', $activityCorrectLayout);//if ^layoutCheck != 3
		$this->assertEqual($activityCorrectLayout->uriResource, $activityCorrectLayoutBis->uriResource);		
		//end of if(^layoutCheck == 1) elseif(^layoutCheck == 2) elseif(^layoutCheck == 3)
		
		//check transition rules:
		$thenActivity = $transitionRuleService->getThenActivity($transitionRule);
		$this->assertNotNull($thenActivity);
		$this->assertEqual($thenActivity->uriResource, $activityCorrectVerification->uriResource);
		$elseActivity = $transitionRuleService->getElseActivity($transitionRule);
		$this->assertNotNull($elseActivity);
		$this->assertEqual($elseActivity->uriResource, $activityCorrectLayout->uriResource);
		
		//scoring verification:
		$activityScoringVerification = $authoringService->createSequenceActivity($connectorScoringDefinition, null, 'Scoring verification');
		$this->assertNotNull($activityScoringVerification);
		$activityService->setAcl($activityScoringVerification, $aclUser, $vars['verifier']);
		$activityService->setControls($activityScoringVerification, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorScoringVerification = $authoringService->createConnector($activityScoringVerification);
		$this->assertNotNull($connectorScoringVerification);
		
		//final sign off :
		$activityTDSignOff = $authoringService->createSequenceActivity($connectorScoringVerification, null, 'Test Developer Sign Off');
		$this->assertNotNull($activityTDSignOff);
		$activityService->setAcl($activityTDSignOff, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityTDSignOff, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorTDSignOff = $authoringService->createConnector($activityTDSignOff);
		$this->assertNotNull($connectorTDSignOff);
		
		//link back to final check:
		$authoringService->createConditionalActivity($connectorTDSignOff, 'else', $activityFinalCheck);
		$transitionRule = $authoringService->createTransitionRule($connectorTDSignOff, '^finalCheck == 1');
		$this->assertNotNull($transitionRule);
		
		//sign off :
		$activityCountrySignOff = $authoringService->createConditionalActivity($connectorTDSignOff, 'then', null, 'Country Sign Off');
		$activityService->setAcl($activityCountrySignOff, $aclUser, $vars['reconciler']);
		$activityService->setControls($activityCountrySignOff, array(INSTANCE_CONTROL_FORWARD));
		
		//end of process definition
		
		$this->processDefinition = $processDefinition;
		
	}
	
	public function testExecuteTranslationProcess(){
		
		$simulationOptions = array(
			'repeatBack' => 0,//O: do not back when possible
			'repeatLoop' => 1,
			'translations' => 2//must be >= 1
		);
		
		if(!$this->processDefinition instanceof core_kernel_classes_Resource){
			$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$translationProcesses = $processClass->searchInstances(array(RDFS_LABEL => (string) $this->processLabel), array('like'=>false));
			if(!empty($translationProcesses)){
				$this->processDefinition = array_pop($translationProcesses);
			}
		}
		if(!$this->processDefinition instanceof core_kernel_classes_Resource){
			$this->fail('No process definition found to be executed');
		}
		
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$this->assertIsA($this->createTranslationProperty('unitUri', '', '', $processInstancesClass), 'core_kernel_classes_Property');
		$this->assertIsA($this->createTranslationProperty('countryCode', '', '', $processInstancesClass), 'core_kernel_classes_Property');
		$this->assertIsA($this->createTranslationProperty('languageCode', '', '', $processInstancesClass), 'core_kernel_classes_Property');
		
		foreach($this->units as $unit){
			foreach ($this->langCountries as $countryCode => $languageCodes){
				foreach ($languageCodes as $langCode){
					$this->out("executes translation process {$unit->getLabel()}/{$countryCode}/{$langCode}:");
					$this->assertIsA($unit, 'core_kernel_classes_Resource');
					$this->executeTranslationProcess($this->processDefinition, $unit->uriResource, $countryCode, $langCode, $simulationOptions);
//					break(3);
				}
			}
		}
		
	}
	
	private function executeTranslationProcess($processDefinition, $unitUri, $countryCode, $languageCode, $simulationOptions){
		
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		
		$processExecName = 'Test Translation Process Execution';
		$processExecComment = 'created by '.__CLASS__.'::'.__METHOD__;
		
		$users = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode);
		
		if(empty($users)){
			$this->fail("cannot find the authorized npm, verifier and reconciler for this country-language : {$countryCode}/{$languageCode}");
			return;
		}
		
		//check that the xliff and vff exist for the given country-language:
		$unit = new core_kernel_classes_Resource($unitUri);
		
		$vffRevision = 0;
		$xliffRevision = 0;
		if(GENERIS_VERSIONING_ENABLED){
			
			$xliffFile = $this->getItemFile($unit, 'xliff', $countryCode, $languageCode);
			$this->assertNotNull($xliffFile);
			$xliffRevision = $xliffFile->getVersion();
			
			$vffFile = $this->getItemFile($unit, 'vff', $countryCode, $languageCode);
			$this->assertNotNull($vffFile);
			$vffRevision = $vffFile->getVersion();
			
		}
		
		$initVariables = array(
			$this->vars['unitUri']->uriResource => $unit->uriResource,
			$this->vars['countryCode']->uriResource => $countryCode,
			$this->vars['languageCode']->uriResource => $languageCode,
			$this->vars['npm']->uriResource => $users['npm'],
			$this->vars['reconciler']->uriResource => $users['reconciler'],
			$this->vars['verifier']->uriResource => $users['verifier'],
			$this->vars['xliff']->uriResource => $xliffRevision,
			$this->vars['vff']->uriResource => $vffRevision,
		);
			
		$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $initVariables);
		$this->assertEqual($processDefinition->uriResource, $processExecutionService->getExecutionOf($processInstance)->uriResource);
		
		$processInstance->setPropertyValue($this->properties['unitUri'], $unit);
		$processInstance->setPropertyValue($this->properties['countryCode'], $countryCode);
		$processInstance->setPropertyValue($this->properties['languageCode'], $languageCode);
		
		$this->assertTrue($processExecutionService->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);
		$this->processExecutions[$processInstance->uriResource] = $processInstance;
			
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($currentActivityExecutions), 1);

		$this->out("<strong>Forward transitions:</strong>", true);
		
		$nbTranslators = (isset($simulationOptions['translations']) && intval($simulationOptions['translations'])>=1 )?intval($simulationOptions['translations']):2;//>=1
		$nbLoops = isset($simulationOptions['repeatLoop'])?intval($simulationOptions['repeatLoop']):1;
		$nbBacks = isset($simulationOptions['repeatBack'])?intval($simulationOptions['repeatBack']):0;
		
		$loopsCounter = array();
		
		$indexActivityTranslate = 2;//the index of the activity in the process definition
		$iterations = $indexActivityTranslate + $nbTranslators +9;
		$this->changeUser($this->userLogins[$countryCode]['NPM']);
		$selectedTranslators = array();
		
		$i = 1;
		$activityIndex = $i;
		while($activityIndex <= $iterations){
			
			$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$activityExecution = null;
			$activity = null;
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				$this->assertEqual(count($activityExecutions), $nbTranslators);
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
			
			$this->out("<strong>Iteration {$i} : activity no{$activityIndex} : ".$activity->getLabel()."</strong>", true);
			$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->uriResource.'"', true);
			
			$this->checkAccessControl($activityExecution);
			
			$currentActivityExecution = null;
			
			//for loop managements:
			$goto = 0;
			
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				
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
				
				//switch to activity's specific check:
				switch ($activityIndex) {
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
					case $indexActivityTranslate + $nbTranslators:
					case $indexActivityTranslate + $nbTranslators +2:
					case $indexActivityTranslate + $nbTranslators +6:
					case $indexActivityTranslate + $nbTranslators +9:{
						//reconciliation:
						//correct verification issues:
						//correct verification issues:
						//scoring definition and testing:
						//country sign off:
						$login = $this->userLogins[$countryCode][$languageCode]['reconciler'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +5:{
						//review corrections:
						//the next activity is "final check":
						$goto = $indexActivityTranslate + $nbTranslators +4;
					}
					case $indexActivityTranslate + $nbTranslators +1:
					case $indexActivityTranslate + $nbTranslators +7:{
						//verify translations :
						//scoring verification
						$login = $this->userLogins[$countryCode][$languageCode]['verifier'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}	
					case $indexActivityTranslate + $nbTranslators +3:{
						
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
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +4:{
						
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
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						
						if(!isset($loopsCounter['correctLayout'])){
							$loopsCounter['correctLayout'] = $nbLoops;
							$this->assertTrue($this->executeServiceLayoutCheck(0));
							$goto = $indexActivityTranslate + $nbTranslators +3;
						}else if(!isset($loopsCounter['reviewCorrections'])){
							$loopsCounter['reviewCorrections'] = $nbLoops;
							$this->assertTrue($this->executeServiceLayoutCheck(2));
							$goto = $indexActivityTranslate + $nbTranslators +5;
						}else if(!isset($loopsCounter['correctVerification'])){
							$loopsCounter['correctVerification'] = $nbLoops;
							$this->assertTrue($this->executeServiceLayoutCheck(3));
							$goto = $indexActivityTranslate + $nbTranslators +2;
						}else{
							$this->assertTrue($this->executeServiceLayoutCheck(1));
							$goto = $indexActivityTranslate + $nbTranslators +6;
						}
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +8:{
						
						//TD sign off:
						$developersLogins = $this->userLogins['testDeveloper'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						if(!isset($loopsCounter['finalCheck'])){
							
							$loopsCounter = array();//reinitialize the loops counter
							
							$loopsCounter['finalCheck'] = $nbLoops;
							$this->assertTrue($this->executeServiceFinalSignOff(false));
							$goto = $indexActivityTranslate + $nbTranslators +4;
						}else{
							$this->assertTrue($this->executeServiceFinalSignOff(true));
						}
						
						break;
					}
				}
				
				//update xliff and vff:
				if(GENERIS_VERSIONING_ENABLED){
					$xliffContent = $this->executeServiceDownloadFile('xliff');
					$this->assertFalse(empty($xliffContent));
					$vffContent = $this->executeServiceDownloadFile('vff');
					$this->assertFalse(empty($vffContent));

					$this->executeServiceUploadFile('xliff', $xliffContent.' \n XLIFF by user '.$this->currentUser->getLabel().' \n', $this->currentUser);
					$this->executeServiceUploadFile('vff', $vffContent.' \n VFF by user '.$this->currentUser->getLabel().' \n', $this->currentUser);
				}
			}
			
			//transition to next activity
			$transitionResult = $processExecutionService->performTransition($processInstance, $currentActivityExecution);
			$goto = intval($goto);
			if($activityIndex == $indexActivityTranslate + $nbTranslators +8 && $goto == $indexActivityTranslate + $nbTranslators +4){
				//the same users are authorized to execute the current and the next activity (final check and correct layout)
				$this->assertEqual(count($transitionResult), 1);
				$this->assertTrue($processExecutionService->checkStatus($processInstance, 'resumed'));
			}else if($activityIndex == $iterations){
				//test finished:
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isFinished($processInstance));
			}else{
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}
			
			//manage next activity index:
			if($goto){
				$activityIndex = $goto;
			}else{
				$activityIndex++;
			}
			
			//increment iteration counts:
			$i++;
			
			$this->out("activity status : ".$activityExecutionService->getStatus($currentActivityExecution)->getLabel());
			$this->out("process status : ".$processExecutionService->getStatus($processInstance)->getLabel());
		}
		
		$activityExecutionsData = $processExecutionService->getAllActivityExecutions($processInstance);
		var_dump($activityExecutionsData);
		
		$executionHistory = $processExecutionService->getExecutionHistory($processInstance);
		$this->assertEqual(count($executionHistory), $i-1);
		
		
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
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			$xliffFileContent = '';
			$vffFileContent = '';
			if(GENERIS_VERSIONING_ENABLED){
				$xliffFile = $this->getItemFile($unit, 'xliff', $countryCode, $languageCode);
				$vffFile = $this->getItemFile($unit, 'vff', $countryCode, $languageCode);

				$xliffFileContent = (string) $xliffFile->getFileContent();
				$vffFileContent = (string) $vffFile->getFileContent();

				if(empty($xliffFileContent)){
					throw new Exception('the original xliff file is empty!');
				}
				if(empty($vffFileContent)){
					throw new Exception('the original vff file is empty!');
				}
			}
			
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

				//creating working xliff and vff file for them for intial files:
				if(GENERIS_VERSIONING_ENABLED){
					$this->createItemFile('xliff_working', $xliffFileContent, $translatorResource);
					$this->createItemFile('vff_working', $vffFileContent, $translatorResource);
				}

			}
			$this->assertTrue(count($pushedVars) > 0);

			$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
			$this->assertTrue($processVariableService->push('translatorsCount', count($pushedVars)));
			$returnValue = $processVariableService->push('translator', serialize($pushedVars));

		}
		return $returnValue;
	}
	
	private function executeServiceTranslate($options = array()){
		
		$returnValue = false;
		
		$this->out('executing service translate ', true);
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		
		if(GENERIS_VERSIONING_ENABLED){
			$xliffContent = $this->executeServiceDownloadFile('xliff_working', $this->currentUser);
			$this->assertFalse(empty($xliffContent));
			$vffContent = $this->executeServiceDownloadFile('vff_working', $this->currentUser);
			$this->assertFalse(empty($vffContent));

			$this->executeServiceUploadFile('xliff_working', $xliffContent.' \n translation by user '.$this->currentUser->getLabel().' \n', $this->currentUser);
			$this->executeServiceUploadFile('vff_working', $vffContent.' \n vff by user '.$this->currentUser->getLabel().' \n', $this->currentUser);
		}
				
		$valid = true;
		if($valid){
			$this->assertTrue($processVariableService->push('translationFinished', 1));
			$returnValue = true;
		}
		
		return $returnValue;
		
	}
	
	private function executeServiceDownloadFile($type, core_kernel_classes_Resource $user = null){
		
		$returnValue = '';
		
		$type = strtolower($type);
		
		$this->out("downloading {$type} file : ", true);
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			$file = $this->getItemFile($unit, $type, $countryCode, $languageCode, $user);
			if(is_null($file)){
				$this->fail("cannot find {$type} file of the unit {$unit->getLabel()}");
			}else{
				$returnValue = $file->getFileContent();
			}

			$this->out("downloaded {$type} file : \n ".$returnValue);
		}
		
		return $returnValue;
	}
	
	private function executeServiceUploadFile($type, $content, $user){
		
		$returnValue = false;
		
		$this->out("uploading {$type} file : ", true);
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			$type = strtolower($type);
			$file = $this->getItemFile($unit, $type, $countryCode, $languageCode, $user);
			if(is_null($file)){
				$this->fail("cannot find {$type} file of the unit {$unit->getLabel()}");
			}else{
				$this->out('inserting new content : '.$content);
				
				$this->assertTrue($file->setContent($content));
				$returnValue = $file->commit();

				//update the file revision number in the process context:
				//@TODO: use $file->getVersion() instead when implemented
				$revisionNumber = intval($file->getVersion());
				$processVariableService->edit($type, $revisionNumber);
				
				$this->out("{$type} file uploaded.");
			}
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
		if(in_array($outputCode, array(0, 1, 2, 3))){
			$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
			$returnValue = $processVariableService->edit('layoutCheck', $outputCode);
		}else{
			$this->fail('wrong output code for layout check activity');
		}
		
		return $returnValue;
	}
	
	private function executeServiceFinalSignOff($ok = false){
		
		$this->out("execute service final sign off", true);
		
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$returnValue = $processVariableService->edit('finalCheck', (bool)$ok?1:0);
		
		return $returnValue;
		
	}
	
	public function testDeleteCreatedResources(){
		
		return;//prevent deletion
		
		if(!empty($this->properties)){
			foreach($this->properties as $prop){
				$this->assertTrue($prop->delete());
			}
		}
		
		if(!is_null($this->itemClass)){
			$this->itemClass->delete();
		}
		
		if(!empty($this->units)){
			foreach($this->units as $unit){
				$this->assertTrue($unit->delete());
			}
		}
		
		if(!empty($this->files)){
			foreach($this->files as $file){
				$this->assertTrue($file->delete());
			}
		}
		
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
		
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		foreach($this->processExecutions as $processInstance){
			if($processInstance instanceof core_kernel_classes_Resource){
				$this->assertTrue($processInstance->exists());
				$this->assertTrue($processExecutionService->deleteProcessExecution($processInstance));
				$this->assertFalse($processInstance->exists());
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