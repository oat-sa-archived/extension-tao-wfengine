<?php
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class ProcessSampleCreator{
	
	//created resources:
	protected static $processes = array();
	protected static $variables = array();
	protected static $roles = array();
	protected static $users = array();
	
	protected $activityService = null;
	protected $connectorService = null;
	protected $processVariableService = null;
	protected $authoringService = null;
	protected $activityExecutionService = null;
	
	public function __construct(){
		
		//init services
		$this->activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$this->processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
		$this->activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$this->connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		$this->processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		
			
	}
	
	public static function getProcesses(){
		return self::$processes;
	}
	
	public static function getVariables(){
		return self::$variables;
	}
	
	public static function getRoles(){
		return self::$roles;
	}
	
	public static function getUsers(){
		return self::$users;
	}
	
	public static function clean(){
		
		$returnValue = false;
		
		$returnValue = self::deleteProcesses();
		
		foreach(self::$variables as $code => $variable){
			if($variable instanceof core_kernel_classes_Resource){
				$returnValue = $variable->delete();
			}
			unset(self::$processes[$code]);
		}
		
		foreach(self::$roles as $uri => $role){
			if($role instanceof core_kernel_classes_Resource){
				$returnValue = $role->delete();
			}
			unset(self::$roles[$uri]);
		}
		
		foreach(self::$users as $uri => $user){
			if($user instanceof core_kernel_classes_Resource){
				$returnValue = $user->delete();
			}
			unset(self::$users[$uri]);
		}
		
		return $returnValue;
	}
	
	public static function deleteProcesses(){
		
		$returnValue = false;
		
		foreach(self::$processes as $processUri => $process){
			
			if($process->exists()){
				$returnValue = $this->authoringService->deleteProcess($process);
			}
			unset(self::$processes[$processUri]);
			
		}
		
		return $returnValue;
	}


	protected function createProcess($label, $comment = ''){
		
		$returnValue = null;
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$returnValue = $processDefinitionClass->createInstance($label, 'created by the script CreateProcess.php on ' . date(DATE_ISO8601));
		if(!is_null($returnValue) && $returnValue instanceof core_kernel_classes_Resource){
			self::$processes[$returnValue->uriResource] = $returnValue;
		}else{
			throw new Exception('cannot create process '.$label);
		}
		
		return $returnValue;
	}
	
	protected function getVariable($code){
		
		$returnValue = null;
		
		$variables = $this->processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => 0));
		if (!empty($variables)) {
			$returnValue = reset($variables);
		}else{
			$returnValue = $this->processVariableService->createProcessVariable($code, $code);
			if (is_null($returnValue)) {
				throw new Exception("the process variable ({$code}) cannot be created.");
			}else{
				self::$variables[$code] = $returnValue;
			}
		}
		
		return $returnValue;
	}
	
	public function createSimpleSequenceProcess(){
		
		//create a new process def
		$processDefinition = $this->createProcess('Simple Sequence Process');

		//define activities and connectors
		$activity1 = $this->authoringService->createActivity($processDefinition, 'activity1');
		$this->authoringService->setFirstActivity($processDefinition, $activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$activity2 = $this->authoringService->createSequenceActivity($connector1, null, 'activity2');
		$connector2 = $this->authoringService->createConnector($activity2);

		$activity3 = $this->authoringService->createSequenceActivity($connector2, null, 'activity3');
		$connector3 = $this->authoringService->createConnector($activity3);

		$activity4 = $this->authoringService->createSequenceActivity($connector3, null, 'activity4');
		$connector4 = $this->authoringService->createConnector($activity4);

		$activity5 = $this->authoringService->createSequenceActivity($connector4, null, 'activity5');
		
		return $processDefinition;
	}
	
	public function createSimpleParallelProcess(){
		
		//set testUserRole
		$this->testUserRole = new core_kernel_classes_Resource(CLASS_ROLE_WORKFLOWUSERROLE);
		
		//process definition
		$processDefinition = $this->createProcess('Simple Parallel Process');
			
		//activities definitions
		$activity0 = $this->authoringService->createActivity($processDefinition, 'activity0');
		$this->authoringService->setFirstActivity($processDefinition, $activity0);
		$connector0 = $this->authoringService->createConnector($activity0);
		
		$connectorParallele = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL);
		$this->authoringService->setConnectorType($connector0, $connectorParallele);

		$parallelActivity1 = $this->authoringService->createActivity($processDefinition, 'activity1');
		$roleRestrictedUser = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER);
		$this->activityService->setAcl($parallelActivity1, $roleRestrictedUser, $this->testUserRole); //!!! it is mendatory to set the role restricted user ACL mode to make this parallel process test case work

		$connector1 = $this->authoringService->createConnector($parallelActivity1);

		$parallelActivity2 = $this->authoringService->createActivity($processDefinition, 'activity2');
		$this->activityService->setAcl($parallelActivity2, $roleRestrictedUser, $this->testUserRole); //!!! it is mendatory to set the role restricted user ACL mode to make this parallel process test case work

		$connector2 = $this->authoringService->createConnector($parallelActivity2);

		//define parallel activities, first branch with constant cardinality value, while the second listens to a process variable:
		$parallelCount1 = 3;
		$parallelCount2 = 5;
		$parallelCount2_processVar_key = 'unit_var';
		$parallelCount2_processVar = $this->getVariable($parallelCount2_processVar_key);
		$prallelActivitiesArray = array(
			$parallelActivity1->uriResource => $parallelCount1,
			$parallelActivity2->uriResource => $parallelCount2_processVar
		);

		$result = $this->authoringService->setParallelActivities($connector0, $prallelActivitiesArray);

		//set several split variables:
		$splitVariable1_key = 'unit_split_var1';
		$splitVariable1 = $this->getVariable($splitVariable1_key);
		$splitVariable2_key = 'unit_split_var2';
		$splitVariable2 = $this->getVariable($splitVariable2_key);

		$splitVariablesArray = array(
			$parallelActivity1->uriResource => array($splitVariable1),
			$parallelActivity2->uriResource => array($splitVariable1, $splitVariable2)
		);
		$this->connectorService->setSplitVariables($connector0, $splitVariablesArray);

		$prallelActivitiesArray[$parallelActivity2->uriResource] = $parallelCount2;


		$joinActivity = $this->authoringService->createActivity($processDefinition, 'activity3');

		//join parallel Activity 1 and 2 to "joinActivity"
		$this->authoringService->createJoinActivity($connector1, $joinActivity, '', $parallelActivity1);
		$this->authoringService->createJoinActivity($connector2, $joinActivity, '', $parallelActivity2);
		
		return $processDefinition;
	}
	
}

?>
