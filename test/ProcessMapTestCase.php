<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

class ProcessMapTestCase extends UnitTestCase {
	
	
	protected $authoringService = null;
	protected $proc = null;
	protected $apiModel = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processMapTestCase','created for the unit test ProcessMapTestCase');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		$this->authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
	}
	
	public function testCreateSequenceActivity(){
		$authoringService = $this->authoringService;
		$processDefinition = $this->proc;
		
		//set the required process variables subjectUri and wsdlContract
		$var_map = $authoringService->getProcessVariable("map");
		if(is_null($var_map)){
			$var_map = $authoringService->createProcessVariable("process var map", "map");
		}
		
		$var_param1 = $authoringService->getProcessVariable("param1");
		if(is_null($var_param1)){
			$var_param1 = $authoringService->createProcessVariable("process var param1", "param1");
		}
		
		$var_param2 = $authoringService->getProcessVariable("param2");
		if(is_null($var_param2)){
			$var_param2 = $authoringService->createProcessVariable("process var param1", "param2");
		}
		
		//create formal param associated to the 3 required proc var:
		$paramMap = $authoringService->getFormalParameter('map');
		if(is_null($paramMap)){
			$paramMap = $authoringService->createFormalParameter('map', 'processvariable', $var_map->uriResource, 'label of the formal param "map"');
		}
		
		$param1 = $authoringService->getFormalParameter('param1');
		if(is_null($param1)){
			$param1 = $authoringService->createFormalParameter('param1', 'processvariable', $var_param1->uriResource, 'label of the formal param "param1"');
		}
		
		$param2 = $authoringService->getFormalParameter('param2');
		if(is_null($param2)){
			$param2 = $authoringService->createFormalParameter('param2', 'processvariable', $var_param2->uriResource, 'label of the formal param "param2"');
		}
		
		//creating the activity definition and connecting them sequentially:
		$activityDefinitions = array();
		
		//create an activity and set it as the first:
		$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
		$this->assertNotNull($activity1);
		$authoringService->setFirstActivity($processDefinition, $activity1);
		$activityDefinitions[] = $activity1;
		
		//create a connector to the first activity and set the type as "sequential"
		$connector1  = null; 
		$connector1 = $authoringService->createConnector($activity1);
		$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
		$this->assertNotNull($connector1);
		
		//same for the 2nd activity:
		$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
		$connector2 = $authoringService->createConnector($activity2);
		$authoringService->setConnectorType($connector2, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
		$activityDefinitions[] = $activity2;
		
		//then the last:
		$activity3 = $authoringService->createSequenceActivity($connector2, null, 'activity3');
		$activityDefinitions[] = $activity3;
		//the last activity does not have a connector
		
		//set the service(i.e. unit) to each activity:
		$services = array();
		$services[1] = array('url' => 'url of unit1', 'label'=>'label of unit1');
		$services[2] = array('url' => 'url of unit2', 'label'=>'label of unit2');
		$services[3] = array('url' => 'url of unit3', 'label'=>'label of unit3');
		
		$i = 1;
		foreach($activityDefinitions as $activity){
			
			$url_unit = $services[$i]['url'];
			$label_unit = $services[$i]['label'];
			
			//try to find if a service definiton has already been created for the unit:
			$serviceDefinition = null;
			$serviceDefinitionCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_SUPPORTSERVICES_URL,$url_unit);
			if(!$serviceDefinitionCollection->isEmpty()){
				if($serviceDefinitionCollection->get(0) instanceof core_kernel_classes_Resource){
					$serviceDefinition = $serviceDefinitionCollection->get(0);
				}
			}
			if(is_null($serviceDefinition)){
				//if no corresponding service def found, create a service definition:
				$serviceDefinitionClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
				$serviceDefinition = $serviceDefinitionClass->createInstance($label_unit, 'created by process map testcase');
				
				//set service definition (the unit) and parameters:
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL), $url_unit);
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $paramMap->uriResource);
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $param1->uriResource);
				$serviceDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN), $param2->uriResource);
			}
			$this->assertNotNull($serviceDefinition);
			
			//create a call of service and associate the service definition to it:
			$service = $authoringService->createInteractiveService($activity);
			$service->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
			
			$authoringService->setActualParameter($service, $paramMap, $var_map->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);
			$authoringService->setActualParameter($service, $param1, $var_param1->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);
			$authoringService->setActualParameter($service, $param2, $var_param2->uriResource, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);
			
			$this->assertNotNull($service);
			
			$i++;
		}
		//end of process definition creation
		
		
		
		//get the ordered list of activity of the sequential process:
		$activityList = array();
		
		//get list of all activities:
		$activities = $authoringService->getActivitiesByProcess($processDefinition);
		$totalNumber = count($activities);
		
		//find the first one: property isinitial == true (must be only one, if not error) and set as the currentActivity:
		$currentActivity = null;
		foreach($activities as $activity){
			
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->uriResource == GENERIS_TRUE){
					$currentActivity = $activity;
					break;
				}
			}
		}
		
		$this->assertNotNull($currentActivity);
		
		//start the loop:
		for($i=0;$i<$totalNumber;$i++){
			//set the test in the table:
			$activityList[$i] = $currentActivity;
			
			//get its connector (check the type is "sequential) if ok, get the next activity
			$connectorCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES, $currentActivity->uriResource);
			$nextActivity = null;
			foreach($connectorCollection->getIterator() as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->uriResource = INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
					$nextActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
					break;
				}
			}
			if(!is_null($nextActivity)){
				$currentActivity = $nextActivity;
			}else{
				if($i == $totalNumber-1){
					//it is normal, since it is the last activity and test
				}else{
					throw new Exception('the next activity of the connector is not found');
				}	
			}
		}
		
		$this->assertEqual(count($activityList), 3);
		
		//execute the process definition:
		/*
		$factory = new wfEngine_models_classes_ProcessExecutionFactory();
		$factory->name = 'ProcessMap Process Execution';
		$factory->execution = $processDefinition->uriResource;
		$factory->ownerUri = SYS_USER_LOGIN;
		
		$proc = $factory->create();
		
		$i = 1;
		while($i <= 3 ){
			
			$activity = $proc->currentActivity[0];
			
			$this->assertTrue($activity->label == 'activity'.$i);
			
			//init execution
			$this->assertTrue($processExecutionService->initCurrentExecution($proc->resource, $activity->resource, $this->currentUser));
			
			$activityExecuction = $this->service->getExecution($activity->resource, $this->currentUser, $proc->resource);
			$this->assertNotNull($activityExecuction);
			
			//transition to the following activity
			$proc->performTransition($activityExecuction->uriResource);
			
			$this->assertFalse($proc->isPaused());
			
			$i++;
		}
		$this->assertTrue($proc->isFinished());
		*/
		
	}
	
	public function tearDown(){
        $this->authoringService->deleteProcess($this->proc);
    }

}
?>