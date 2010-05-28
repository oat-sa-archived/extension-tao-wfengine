<?php
require_once('tao/actions/CommonModule.class.php');
require_once('tao/actions/TaoModule.class.php');

/**
 * ProcessAuthoring Controller provide actions to edit a process
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class ProcessAuthoring extends TaoModule {
	
	protected $processTreeService = null;
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = new wfEngine_models_classes_ProcessAuthoringService();
		$this->defaultData();
		
		//add the tree service
		$this->processTreeService = new wfEngine_models_classes_ProcessTreeService();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected instance from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $instance
	 */
	protected function getCurrentInstance(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$instance = $this->service->getInstance($uri, 'uri', $clazz);
		if(is_null($instance)){
			throw new Exception("No instance of the class {$clazz->getLabel()} found for the uri {$uri}");
		}
		
		return $instance;
	}
	
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return null;
	}

	protected function getCurrentActivity(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('activityUri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid activity uri found");
		}
		
		$instance = $this->service->getInstance($uri, 'uri', new core_kernel_classes_Class(CLASS_ACTIVITIES));
		if(is_null($instance)){
			throw new Exception("No instance of the class Activities found for the uri {$uri}");
		}
		
		return $instance;
	}
	
	protected function getCurrentProcess(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('processUri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid process uri found");
		}
		
		$instance = $this->service->getInstance($uri, 'uri', new core_kernel_classes_Class(CLASS_PROCESS));
		if(is_null($instance)){
			throw new Exception("No instance of the class Process found for the uri {$uri}");
		}
		
		return $instance;
	}

/*
 * controller actions
 */
	/**
	 * Render json data to populate the delivery tree 
	 * 'modelType' must be in the request parameters
	 * @return void
	 */
	public function getInstancesOf(){
				
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$instanceOf = strtolower($_GET["instanceof"]);
		$classUri='';
		switch($instanceOf){
			case 'servicedefinition': 
				$classUri=CLASS_SERVICESDEFINITION;// <=> CLASS_WEBSERVICES or CLASS_SUPPORTSERVICES
				break;
			case 'formalparameter': 
				$classUri=CLASS_FORMALPARAMETER;break;
			case 'variable': 
				$classUri=CLASS_PROCESSVARIABLES;break;
			case 'role': 
				$classUri=CLASS_ROLE_BACKOFFICE;break;//use to be CLASS_ROLE, now only back office roles are authorized to wf users (including TAO managers)
			default:
				throw new Exception('unknown class');break;
		}
		// $classUri = CLASS_SERVICEDEFINITION;
		//!!! currently, not the uri of the class is provided: better to pass it to "get" parameter somehow
		//one possibility: replace all by their uriResource from the authoring template.
		$clazz=new core_kernel_classes_Class($classUri);
		if( !$this->service->isAuthorizedClass($clazz) ){
			throw new Exception("wrong class uri in parameter");
		}
		
		$highlightUri = '';
		// if($this->hasSessionAttribute("showNodeUri")){
			// $highlightUri = $this->getSessionAttribute("showNodeUri");
			// unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		// }
		
		$filter = '';
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
		}
		echo json_encode( $this->service->toTree( $clazz, true, true, $highlightUri, $filter));
	}
	
	public function getActivities(){
		//getCurrentProcess from delivery
		
		// $processUri = tao_helpers_Uri::decode($_POST["processUri"]);
		// $processUri = "http://127.0.0.1/middleware/demo.rdf#i1265636054002217400";
		$currentProcess = null;
		$currentProcess = $this->getCurrentProcess();
		if(!empty($currentProcess)){
			echo json_encode($this->processTreeService->activityTree($currentProcess));
		}else{
			throw new Exception("no process uri found");
		}
	}
	
	public function getActivityTree(){
		$this->setView('process_tree_activity.tpl');
	}
	
	public function addActivity(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$label='';
		if(isset($_POST['label'])){
			$label = $_POST['label'];
		}
		
		$currentProcess = $this->getCurrentProcess();
		$newActivity = $this->service->createActivity($currentProcess, $label);
		$newConnector = $this->service->createConnector($newActivity);
		
		//attach the created activity to the process
		if(!is_null($newActivity) && $newActivity instanceof core_kernel_classes_Resource){
			$class = 'node-activity';
			if($newActivity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL))->uriResource == GENERIS_TRUE){
				//just set the first activity as a such
				$class .= ' node-activity-initial';
			}
			
			echo json_encode(array(
				'label'	=> $newActivity->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($newActivity->uriResource),
				'connector' => $this->processTreeService->defaultConnectorNode($newConnector),
				'class' => $class
			));
		}
	}
	
	public function addInteractiveService(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$currentActivity = $this->getCurrentActivity();
		$newService = $this->service->createInteractiveService($currentActivity);
		
		//set default position and size value:
		$newService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH), 100);
		$newService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT), 100);
		$newService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP), 0);
		$newService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT), 0);
		
		if(isset($_POST['serviceDefinitionUri'])){
			$serviceDefinition = new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['serviceDefinitionUri']));
			if(!is_null($serviceDefinition)){
				$this->saveCallOfService(array(
					'callOfServiceUri' => $newService->uriResource,
					PROPERTY_CALLOFSERVICES_SERVICEDEFINITION => $serviceDefinition->uriResource,
					'label' => "service: ".$serviceDefinition->getLabel()
				));
			}
		}
		
		if(!is_null($newService) && $newService instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $newService->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($newService->uriResource)
			));
		}
	}
	
	public function addInferenceRule(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$currentActivity = $this->getCurrentActivity();
		$inferenceRule = $this->service->createInferenceRule($currentActivity, $_POST['type']);
		if(!is_null($inferenceRule) && $inferenceRule instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $inferenceRule->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($inferenceRule->uriResource)
			));
		}
	}
	
	public function addConsistencyRule(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$currentActivity = $this->getCurrentActivity();
		$consistencyRule = $this->service->createConsistencyRule($currentActivity);
		if(!is_null($consistencyRule) && $consistencyRule instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $consistencyRule->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($consistencyRule->uriResource)
			));
		}
	}
	
	public function getSectionTrees(){
		$section = $_POST["section"];
		$this->setData('section', $section);
		$this->setView('process_tree.tpl');
	}
	
	public function editInstance(){
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		// var_dump($instance);
		$excludedProperty = array();
		$excludedProperty[] = 'http://www.tao.lu/middleware/Interview.rdf#i122354397139712';
		
		$formName="";
		//define the type of instance to be edited:
		if(strcasecmp($clazz->uriResource, CLASS_FORMALPARAMETER) == 0){
			$formName = "formalParameter";
		}elseif(strcasecmp($clazz->uriResource, CLASS_ROLE_BACKOFFICE) == 0){
			$formName = "role";
		}elseif( (strcasecmp($clazz->uriResource, CLASS_WEBSERVICES) == 0) || (strcasecmp($clazz->uriResource, CLASS_SUPPORTSERVICES) == 0) ){
			//note: direct instanciating CLASS_SERVICEDEFINITION should be forbidden
			$formName = "serviceDefinition";
			
			//unquote the following lines to disable the display of formal parameters fields
			// $excludedProperty[] = PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT;
			// $excludedProperty[] = PROPERTY_SERVICESDEFINITION_FORMALPARAMIN;
		}elseif(strcasecmp($clazz->uriResource, CLASS_PROCESSVARIABLES) == 0){
			$formName = "variable";
		}else{
			throw new Exception("attempt to editing an instance of an unsupported class");
		}
				
		$myForm = null;
		$myForm = wfEngine_helpers_ProcessFormFactory::instanceEditor($clazz, $instance, $formName, array("noSubmit"=>true,"noRevert"=>true) , $excludedProperty );
		$myForm->setActions(array(), 'bottom');	
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$propertyValues = $myForm->getValues();
				
				// if(empty($propertyValues[PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT])){
					// unset($propertyValues[PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT]);
					// $instance->removePropertyValues(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT));
				// }
				// if(empty($propertyValues[PROPERTY_SERVICESDEFINITION_FORMALPARAMIN])){
					// unset($propertyValues[PROPERTY_SERVICESDEFINITION_FORMALPARAMIN]);
					// $instance->removePropertyValues(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
				// }
				
				foreach($propertyValues as $key=>$value){
					if(empty($value)){
						$instance->removePropertyValues(new core_kernel_classes_Property($key));
						unset($propertyValues[$key]);
					}
				}
				
				$instance = $this->service->bindProperties($instance, $propertyValues);
				echo __("saved");exit;
			}
		}
		
		$this->setData('section', $formName);
		$this->setData('formPlus', $myForm->render());
		$this->setView('process_form_tree.tpl');
	}
	
	public function editActivityProperty(){
		$formName = "activityPropertyEditor";
		$activity = $this->getCurrentActivity();
		$excludedProperty = array(
			PROPERTY_ACTIVITIES_INTERACTIVESERVICES,
			PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE,
			PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE,
			PROPERTY_ACTIVITIES_CONSISTENCYRULE,
			PROPERTY_ACTIVITIES_DISPLAYCALENDAR,
			PROPERTY_ACTIVITIES_HYPERCLASSES,
			PROPERTY_ACTIVITIES_STATEMENTASSIGNATION,
			PROPERTY_ACTIVITIES_ISINITIAL,
			PROPERTY_ACTIVITIES_ALLOWFREEVALUEOF
		);
		
		$this->setData('saved', false);
		$this->setData('sectionName', 'activity');
		
		$myForm = null;
		$myForm = wfEngine_helpers_ProcessFormFactory::instanceEditor(new core_kernel_classes_Class(CLASS_ACTIVITIES), $activity, $formName, array("noSubmit"=>true,"noRevert"=>true), $excludedProperty);
		$myForm->setActions(array(), 'bottom');	
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				
				$properties = $myForm->getValues();
				if($this->saveActivityProperty($properties)){
					//replace with a clean template upload
					$this->setData('saved', true);
				}
				$this->setView('process_form_property.tpl');
				return;
			}
		}
		
		
		$this->setData('myForm', $myForm->render());
		$this->setView('process_form_property.tpl');
	}
	
	public function saveActivityProperty($param = array()){
	
		$activity = $this->getCurrentActivity();
		
		$saved = true;
		
		$properties = array();
		if(empty($param)){
			//take the data from POST:
			
			//decode uri:
			foreach($_POST as $key=>$value){
				$properties[tao_helpers_Uri::decode($key)] = tao_helpers_Uri::decode($value);
			}
		}else{
			$properties = $param;
		}
		
		//set label:
		if(isset($properties[RDFS_LABEL])){
			$activity->setLabel($properties[RDFS_LABEL]);
		}
		
		//set role:
		if(isset($properties[PROPERTY_ACTIVITIES_ROLE])){
			$this->service->setActivityRole($activity, new core_kernel_classes_Resource($properties[PROPERTY_ACTIVITIES_ROLE]));
		}
		
		//set ishidden:
		if(isset($properties[PROPERTY_ACTIVITIES_ISHIDDEN])){
			$bool = wfEngine_models_classes_ProcessAuthoringService::generisBooleanConvertor($properties[PROPERTY_ACTIVITIES_ISHIDDEN]);
			if(!is_null($bool)){
				$this->service->setActivityHidden($activity, $bool);
			}
		}
		//save ACL mode:
		if(isset($properties[PROPERTY_ACTIVITIES_ACL_MODE])){
			$mode = $properties[PROPERTY_ACTIVITIES_ACL_MODE];
			
			if(!empty($mode)){
				$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
				$target = null;
				switch($mode){
					case INSTANCE_ACL_USER:
						if(!empty($properties[PROPERTY_ACTIVITIES_RESTRICTED_USER])){
							$target = new core_kernel_classes_Resource($properties[PROPERTY_ACTIVITIES_RESTRICTED_USER]);
						}
						break;
					
					case INSTANCE_ACL_ROLE:
					case INSTANCE_ACL_ROLE_RESTRICTED_USER:
					case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
						if(!empty($properties[PROPERTY_ACTIVITIES_RESTRICTED_ROLE])){
							$target = new core_kernel_classes_Resource($properties[PROPERTY_ACTIVITIES_RESTRICTED_ROLE]);
						}
						break;
					
					default:
						throw new Exception('unknown ACL mode: '.$mode);
				}
				
				if($activityExecutionService->setAcl($activity, new core_kernel_classes_Resource($mode), $target) instanceof core_kernel_classes_Resource){
					$saved = true;
				}
			}
		}
		return $saved;
	}
	
	/**
	 * Get the list of roles that can be selected for an inherited ACL mode
	 * Render a json array of the roles
	 * @return void
	 */
	public function getActivityInheritableRoles(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception('Wrong request mode');
		}
		
		$availableRoles = array();

		$activity = $this->getCurrentActivity();
		
		$activityModeProp	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE);
        $restrictedRoleProp	= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE);
        $actsProp 			= new core_kernel_classes_Property(PROCESS_ACTIVITIES);
		
		$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
        $subjects 	= $apiModel->getSubject(PROPERTY_PROCESS_ACTIVITIES, $activity->uriResource);
        foreach($subjects->getIterator() as $process){
        	
			foreach ($process->getPropertyValues($actsProp) as $pactivityUri){
				
				$pactivity = new core_kernel_classes_Resource($pactivityUri);
				
        		//get an activity with the same mode
        		$mode = $pactivity->getOnePropertyValue($activityModeProp);
        		if($mode->uriResource == INSTANCE_ACL_ROLE_RESTRICTED_USER){
        			$pRole = $pactivity->getUniquePropertyValue($restrictedRoleProp);
        			if(!is_null($pRole)){
        				if(!array_key_exists($pRole->uriResource, $availableRoles)){
        					$availableRoles[$pRole->uriResource] = $pRole->getLabel();
        				}
        			}
        		}
	        }
        }
        $availableRoles = tao_helpers_Uri::encodeArray($availableRoles, tao_helpers_Uri::ENCODE_ARRAY_KEYS, true);
		echo json_encode(array('roles' => $availableRoles));
	}
	
	public function editProcessProperty(){
		$formName = "processPropertyEditor";
		$process = $this->getCurrentProcess();
		$excludedProperty = array(
			PROPERTY_PROCESS_ACTIVITIES,
			'http://www.tao.lu/middleware/Interview.rdf#i122354397139712'
		);
		
		$this->setData('saved', false);
		$this->setData('sectionName', 'process');
		
		$myForm = null;
		$myForm = wfEngine_helpers_ProcessFormFactory::instanceEditor(new core_kernel_classes_Class(CLASS_PROCESS), $process, $formName, array("noSubmit"=>true,"noRevert"=>true), $excludedProperty, true);
		$myForm->setActions(array(), 'bottom');	
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$process = $this->service->bindProperties($process, $myForm->getValues());
				
				//replace with a clean template upload
				$this->setData('saved', true);
				$this->setView('process_form_property.tpl');
				exit;
			}
		}
		
		$this->setData('myForm', $myForm->render());
		$this->setView('process_form_property.tpl');
	}
	
	public function editConsistencyRule(){
		$formName = uniqid("consistencyRuleEditor_");
		$myForm = wfEngine_helpers_ProcessFormFactory::consistencyRuleEditor(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['consistencyRuleUri'])), $formName);

		$this->setData('formId', $formName);
		$this->setData('formConsistencyRule', $myForm->render());
		$this->setView('process_form_consistencyRule.tpl');
	}
	
	/**
	 * Add an instance        
	 * @return void
	 */
	public function addInstance(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$instance = $this->service->createInstance($clazz);
		
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
		
			//case when a process variable has been just added:
			if($clazz->uriResource == CLASS_PROCESSVARIABLES){
				//set the new instance of process variable as a property of the class process instance:
				$ok = core_kernel_impl_ApiModelOO::singleton()->setStatement($instance->uriResource, RDF_TYPE, RDF_PROPERTY, '');
				if($ok){
					$newProcessInstanceProperty = new core_kernel_classes_Property($instance->uriResource);
					$newProcessInstanceProperty->setDomain(new core_kernel_classes_Class(CLASS_PROCESSINSTANCE));
					$newProcessInstanceProperty->setRange(new core_kernel_classes_Class(RDFS_LITERAL));//literal only??
				}else{
					throw new Exception("the newly created process variable cannot be set as a property of the class process instance");
				}
			}
			
			echo json_encode(array(
				'label'	=> $instance->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
			));
		}
	}
		
	/**
	 * Delete a delivery or a delivery class
	 * @return void
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteInstance($this->getCurrentInstance());
		}
		// else{
			// $deleted = $this->service->deleteDeliveryClass($this->getCurrentClass());
		// }
		
		echo json_encode(array('deleted' => $deleted));
	}
	
	public function deleteCallOfService(){
		$callOfService = new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST["serviceUri"]));
		
		//delete its related properties
		$deleted = $this->service->deleteActualParameters($callOfService);
		
		//remove the reference from this interactive service
		$deleted = $this->service->deleteReference(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $callOfService);
		
		//delete call of service itself
		$deleted = $this->service->deleteInstance($callOfService);
	
		echo json_encode(array('deleted' => $deleted));
	}
	
	public function deleteConnector(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		if(empty($_POST["connectorUri"])){
			$deleted = false;
		}
		$deleted = $this->service->deleteConnector(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST["connectorUri"])));
	
		echo json_encode(array('deleted' => $deleted));
	}
	
	public function deleteActivity(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = $this->service->deleteActivity($this->getCurrentActivity());
	
		echo json_encode(array('deleted' => $deleted));
	}
	
	public function deleteInferenceRule(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$deleted = $this->service->deleteInferenceRule(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST["inferenceUri"])));
	
		echo json_encode(array('deleted' => $deleted));
		
	}
	
	public function deleteConsistencyRule(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$deleted = $this->service->deleteConsistencyRule(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST["consistencyUri"])));
	
		echo json_encode(array('deleted' => $deleted));
		
	}
	
	/**
	 * Duplicate an instance
	 * A bit more complicated here
	 * @return void
	 */
	// public function cloneInstance(){
		// if(!tao_helpers_Request::isAjax()){
			// throw new Exception("wrong request mode");
		// }
		
		// $instance = $this->getCurrentInstance();
		// $clazz = $this->getCurrentClass();
		// if(! $this->service->isAuthorizedClass($clazz)){
			// throw new Exception("attempt to clone an instance of an unauthorized class!");
		// }
		// $clone = $this->service->createInstance($clazz);
		// if(!is_null($clone)){
			
			// foreach($clazz->getProperties() as $property){
				// foreach($instance->getPropertyValues($property) as $propertyValue){
					// $clone->setPropertyValue($property, $propertyValue);
				// }
			// }
			// $clone->setLabel($instance->getLabel()."'");
			// echo json_encode(array(
				// 'label'	=> $clone->getLabel(),
				// 'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			// ));
		// }
	// }
	
	public function editCallOfService(){
		$callOfServiceUri = tao_helpers_Uri::decode($_POST['uri']);
		if(empty($callOfServiceUri)){
			throw new Exception("no call of service uri found");
		}
		$id = 'callOfServiceId';
		if(stripos($callOfServiceUri,".rdf#")>0){
			$id = substr($callOfServiceUri, stripos($callOfServiceUri,".rdf#")+5);
		}
		
		$callOfService = new core_kernel_classes_Resource($callOfServiceUri);
		$formName=uniqid("callOfServiceEditor_");
		$myForm = wfEngine_helpers_ProcessFormFactory::callOfServiceEditor($callOfService, null, $formName);//NS_TAOQUAL . '#i118595593412394'
		$servicesData = array(
			'current' => array(
				'label' => $callOfService->getLabel(),
				'id' => $id
				),
			'other' => array()
		);
		
		$activity = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_ACTIVITIES_INTERACTIVESERVICES, $callOfServiceUri)->get(0);
		if($activity instanceof core_kernel_classes_Resource){
			
			$serviceCollection = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			
			
			foreach($serviceCollection->getIterator() as $service){
				if( $service->uriResource != $callOfServiceUri ){
				
					$serviceStylingData = array();
					$serviceStylingData['label'] = $service->getLabel();
					$serviceStylingData['uri'] = tao_helpers_Uri::encode($service->uriResource);
					
					//get the position and size data (in %):
					$width = $service->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH));
					if($width != null && $width instanceof core_kernel_classes_Literal){
						if(intval($width)){
							//do not allow width="0"
							$serviceStylingData['width'] = intval($width->literal);
						}
					}
					
					$height = $service->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT));
					if($height != null && $height instanceof core_kernel_classes_Literal){
						if(intval($height->literal)){
							//do not allow height="0"
							$serviceStylingData['height'] = intval($height->literal);
						}
					}
					
					$top = $service->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP));
					if($top != null && $top instanceof core_kernel_classes_Literal){
						$serviceStylingData['top'] = intval($top->literal);
					}
					
					$left = $service->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT));
					if($left != null && $left instanceof core_kernel_classes_Literal){
						$serviceStylingData['left'] = intval($left->literal);
					}
					$servicesData['other'][taoDelivery_helpers_Compilator::getUniqueId($service->uriResource)] = $serviceStylingData;
				}
			}
		}
		
		$this->setData('formId', $formName);
		$this->setData('formInteractionService', $myForm->render());
		$this->setData('servicesData', $servicesData);
		$this->setView('process_form_interactiveServices.tpl');
	}
			
	public function saveCallOfService($param = array()){
		// $param = array("callOfServiceUri", PROPERTY_CALLOFSERVICES_SERVICEDEFINITION, 'label');
		
		$saved = true;
		
		if(empty($param)){
			//take the data from POST
			
			//decode uri:
			$data = array();
			foreach($_POST as $key=>$val){
				$value = tao_helpers_Uri::decode($val);
				if(!empty($value)){//filter the empty string values
				}	$data[tao_helpers_Uri::decode($key)] = $value;
			}
		}else{
			$data = $param;
		}
		
		$callOfService = null;
		if(!isset($data["callOfServiceUri"])){
			throw new Exception("no call of service uri found in data array");
		}else{
			$callOfService = new core_kernel_classes_Resource($data["callOfServiceUri"]);
			unset($data["callOfServiceUri"]);
		}
		
		//edit service definition resource value:
		if(!isset($data[PROPERTY_CALLOFSERVICES_SERVICEDEFINITION])){
			throw new Exception("no service definition uri found in data array");
		}
		$serviceDefinition = new core_kernel_classes_Resource($data[PROPERTY_CALLOFSERVICES_SERVICEDEFINITION]);
		unset($data[PROPERTY_CALLOFSERVICES_SERVICEDEFINITION]);
		
		if(!is_null($serviceDefinition)){
			$this->service->setCallOfServiceDefinition($callOfService, $serviceDefinition);
		}
		
		if(isset($data["label"])){
			$callOfService->setLabel($data["label"]);
		}
		
		//note: equivalent to $callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
		
		//reset new actual parameters : clear ALL and recreate new values at each save
		$deleted = $this->service->deleteActualParameters($callOfService);
		if(!$deleted){
			throw new Exception("the actual parameters related to the call of service cannot be removed");
		}
		
		// var_dump($data);
		foreach($data as $key=>$value){
			$formalParamUri = '';
			$parameterInOrOut='';
			
			//find whether it is a parameter IN or OUT:
			
			//method 1: use the connection relation between the subject serviceDefinition and the object formalParameter: 
			//issue with the use of the same instance of formal parameter for both parameter in and out of an instance of a service definiton
			/*
			$formalParameterType = core_kernel_impl_ApiModelOO::getPredicate($serviceDefinition->uriResource, $formalParam->uriResource);
			if(strcasecmp($formalParameterType->uriResource, PROPERTY_SERVICESDEFINITION_FORMALPARAMIN)==0){
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMIN;
			}elseif(strcasecmp($formalParameterType->uriResource, PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT)==0){
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT;
			}else{
				//unknown actual parameter type to be bind to the current call of service
				continue;
			}
			*/
			
			//method2: use the suffix of the name of the form input:
			$index = 0;
			$suffix = '';
			if($index = strpos($key,'_IN_choice')){
				
				$formalParamUri = substr($key,0,$index);
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMIN;
				$suffix = '_IN';
			}elseif($index = strpos($key,'_OUT_choice')){
				$formalParamUri = substr($key,0,$index);
				$parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT;
				$suffix = '_OUT';
			}else{
				continue;
			}
			
			
			$actualParameterType = '';
			$paramValue = '';
			if($value == 'constant'){
				$actualParameterType = PROPERTY_ACTUALPARAM_CONSTANTVALUE;
				$paramValue = $data[$formalParamUri.$suffix.'_constant'];
				unset($data[$formalParamUri.$suffix.'_constant']);
			}elseif($value == 'processvariable'){
				$actualParameterType = PROPERTY_ACTUALPARAM_PROCESSVARIABLE;
				$paramValue = $data[$formalParamUri.$suffix.'_var'];
				unset($data[$formalParamUri.$suffix.'_var']);
			}else{
				throw new Exception('wrong actual parameter type posted');
			}
			// $actualParameterType = PROPERTY_ACTUALPARAM_CONSTANTVALUE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
		
			$formalParam = new core_kernel_classes_Resource($formalParamUri);
			$saved = $this->service->setActualParameter($callOfService, $formalParam, $paramValue, $parameterInOrOut, $actualParameterType);
			// var_dump($paramValue, $parameterInOrOut, $actualParameterType);
			
			if(!$saved){
				break;
			}
		}
		
		//save positioning and style data:
		$this->service->bindProperties($callOfService, $data);
		
		
		echo json_encode(array("saved" => $saved));
	}
	
	public function editConnector(){
		$connectorUri = tao_helpers_Uri::decode($_POST['connectorUri']);
		
		$formName=uniqid("connectorEditor_");
		$myForm = wfEngine_helpers_ProcessFormFactory::connectorEditor(new core_kernel_classes_Resource($connectorUri), null, $formName, $this->getCurrentActivity());
		
		$this->setData('formId', $formName);
		$this->setData('formConnector', $myForm->render());
		$this->setView('process_form_connector.tpl');
	}
	
	public function editInferenceRule(){
		$inferenceRule = new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['inferenceRuleUri']));
		
		$formName = uniqid("inferenceRuleEditor_");
		$myForm = wfEngine_helpers_ProcessFormFactory::inferenceRuleEditor($inferenceRule, $formName);
		
		$this->setData('formId', $formName);
		$this->setData('formInferenceRule', $myForm->render());
		$this->setView('process_form_inferenceRule.tpl');
	}
	
	public function saveConnector(){
		$saved = true;
		$propNextActivities = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		
		//decode uri:
		$data = array();
		foreach($_POST as $key=>$value){
			$data[tao_helpers_Uri::decode($key)] = tao_helpers_Uri::decode($value);
		}
		
		if(!isset($data["connectorUri"])){
			$saved = false;
			throw new Exception("no connector uri found in POST");
		}else{	
			$connectorInstance = new core_kernel_classes_Resource($data["connectorUri"]);
		}
		
		//current activity:
		$activity = $this->getCurrentActivity();
						
		//edit service definition resource value:
		if(!isset($data[PROPERTY_CONNECTORS_TYPE])){
			$saved = false;
			throw new Exception("no connector type uri found in POST");
		}
		
		if(trim($data['label']) != ''){
			// $propertyValues[RDFS_LABEL] = $data['label'];
			$connectorInstance->setLabel($data['label']);
		}
		
		if($data[PROPERTY_CONNECTORS_TYPE] != 'none'){
			//check if there is a need for update: in case the old type of connector was 'join':
			$connectorType = $connectorInstance->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if(!is_null($connectorType)){
				if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_JOIN){
					
					$oldNextActivity = $connectorInstance->getOnePropertyValue($propNextActivities);
					if(!is_null($oldNextActivity)){
						//if the current type is still 'join' && target activity has changed || type of connector has changed:
						if( $oldNextActivity->uriResource != $data["join_activityUri"] || $data[PROPERTY_CONNECTORS_TYPE]!= INSTANCE_TYPEOFCONNECTORS_JOIN){
							//check if another activities is joined with the same connector:
							$previousActivityCollection = $connectorInstance->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES)); //old: apimodel->getSubject(PROPERTY_CONNECTORS_PRECACTIVTIES, $oldNextActivity->uriResource);
							$anotherPreviousActivity = null;
							foreach($previousActivityCollection->getIterator() as $previousActivity){
								if($previousActivity instanceof core_kernel_classes_Resource){
									if($previousActivity->uriResource != $activity->uriResource){
										$anotherPreviousActivity = $previousActivity;
										break;
									}
								}
							}
							
							if(!is_null($anotherPreviousActivity)){
								// echo ' creating new connector for it ';
								//there is another activity, so:
								//remove reference of that activity from previous connector, and update its activity reference to the one of the other previous activity, update the 'old' join connector
								$this->service->deleteReference(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES), $activity);
								$connectorInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE), $anotherPreviousActivity->uriResource);
							
								//since it used to share the connector with other previous activities, now that it needs a new connector of its own, create one here:
								//create a new connector for the current previous activity: 
								$newConnectorInstance = $this->service->createConnector($activity, 'merge to ');
								$connectorInstance = $newConnectorInstance;
							}else{
								//the activity is the first activity that is joined via this connector so just let it be edited
							}
							
						}
					}
				}
			}
			$this->service->setConnectorType($connectorInstance, new core_kernel_classes_Resource($data[PROPERTY_CONNECTORS_TYPE]));
		}
		
		
		$followingActivity = null;
		
		if($data[PROPERTY_CONNECTORS_TYPE] == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
			//get form input starting with "next_"
			if(isset($data["next_activityUri"])){
				if($data["next_activityUri"]=="newActivity"){
					$this->service->createSequenceActivity($connectorInstance, null, $data["next_activityLabel"]);
				}else{
					$followingActivity = new core_kernel_classes_Resource($data["next_activityUri"]);
					$this->service->createSequenceActivity($connectorInstance, $followingActivity);
				}
			}
		}elseif($data[PROPERTY_CONNECTORS_TYPE] == INSTANCE_TYPEOFCONNECTORS_SPLIT){
			
			//clean old value of property (use bind property with empty input?)
			$connectorInstance->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
			
			if(isset($data['if'])){
				
				//delete the old rule, if exists:
				$oldRule = $connectorInstance->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				if(!empty($oldRule)){
					$deleted = $this->service->deleteRule($oldRule);
					//TODO: to be called deleteTransitionRule
					//TODO: use editCOndition somewhere instead
					// if(!$deleted){
						// throw new Exception("the old transition rule related to the connector cannot be removed");
					// }
				}
								
				//save the new rule here:
				
				$condition = $data['if'];
				
				if(!empty($condition)){
					if(is_null($this->service->createRule($connectorInstance, $condition))){
						throw new Exception("the condition \"{$condition}\" cannot be created");
					}
				}
				
				//save the "then" and the "else" activity (or connector)
				if(($data['then_activityOrConnector']=="activity") && isset($data["then_activityUri"])){
					//destruction of the connector of the connector?
					if($data["then_activityUri"]=="newActivity"){
						$this->service->createSplitActivity($connectorInstance, 'then', null, $data["then_activityLabel"], false);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["then_activityUri"]);
						$this->service->createSplitActivity($connectorInstance, 'then', $followingActivity, '', false);
					}
				}elseif(($data['then_activityOrConnector']=="connector") && isset($data["then_connectorUri"])){
					if($data["then_connectorUri"]=="newConnector"){
						$this->service->createSplitActivity($connectorInstance, 'then', null, '', true);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["then_connectorUri"]);
						$this->service->createSplitActivity($connectorInstance, 'then', $followingActivity, '', true);
					}
				}elseif($data['then_activityOrConnector']=="delete"){
					$this->service->deleteConnectorNextActivity($connectorInstance, 'then');
				}
			
				//save the activity in "ELSE":
				if(($data['else_activityOrConnector']=="activity") && isset($data["else_activityUri"])){
					if($data["else_activityUri"]=="newActivity"){
						$this->service->createSplitActivity($connectorInstance, 'else', null, $data["else_activityLabel"], false);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["else_activityUri"]);
						$this->service->createSplitActivity($connectorInstance, 'else', $followingActivity, '', false);
					}
				}elseif(($data['else_activityOrConnector']=="connector") && isset($data["else_connectorUri"])){
					if($data["else_connectorUri"]=="newConnector"){
						$this->service->createSplitActivity($connectorInstance, 'else', null, '', true);
					}else{
						$followingActivity = new core_kernel_classes_Resource($data["else_connectorUri"]);
						$this->service->createSplitActivity($connectorInstance, 'else', $followingActivity, '', true);
					}
				}elseif($data['else_activityOrConnector']=="delete"){
					$this->service->deleteConnectorNextActivity($connectorInstance, 'else');
				}
			}
		}elseif($data[PROPERTY_CONNECTORS_TYPE] == INSTANCE_TYPEOFCONNECTORS_PARALLEL){
			
			$connectorInstance->removePropertyValues($propNextActivities);
			
			foreach($data as $key=>$activityUri){
				if(strpos($key, 'parallel_')===0){//find the key-value related to selected activities
					//get the number of that activity:
					$number = $data[$activityUri.'_num_hidden'];
					for($i=0;$i<$number;$i++){
						$connectorInstance->setPropertyValue($propNextActivities, $activityUri);
					}
				}
			}
			
		}elseif($data[PROPERTY_CONNECTORS_TYPE] == INSTANCE_TYPEOFCONNECTORS_JOIN){
		
			if(!empty($data["join_activityUri"])){
				if($data["join_activityUri"] == 'newActivity'){
					$this->service->createJoinActivity($connectorInstance, null, $data["join_activityLabel"]);
				}else{
					if(!is_null($activity)){
						$followingActivity = new core_kernel_classes_Resource($data["join_activityUri"]);
						$this->service->createJoinActivity($connectorInstance, $followingActivity, '', $activity);
					}else{
						throw new Exception('no activity found to be joined');
					}
				}
			}
		}
		
		echo json_encode(array("saved" => $saved));
	}
	
	public function saveInferenceRule(){
		
		$returnValue = false;
		
		$inferenceRule = new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['inferenceRuleUri']));
		
		//save the "if":
		$conditionString = $_POST['if'];
		if(!empty($conditionString)){
			
			$this->service->editCondition($inferenceRule, $conditionString);
		}
		
		//save the "then":
		// $inferenceThenProp = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_THEN);
		$then_assignment = $_POST['then_assignment'];
		$thenDom = $this->service->analyseExpression($then_assignment);
		// var_dump($thenDom->saveXML());
		if(!is_null($thenDom)){
			$newAssignment = $this->service->createAssignment($thenDom);
			$returnValue = $this->service->setAssignment($inferenceRule, 'then', $newAssignment);
		}
		
		//save the "else":
		if(isset($_POST['else_choice'])){
			
			$returnValue = false;
			$inferenceElseProp = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE);
			
			if($_POST['else_choice'] == 'assignment'){
			
				$else_assignment = $_POST['else_assignment'];
				$elseDom = $this->service->analyseExpression($else_assignment);
				if(!is_null($elseDom)){
					$newAssignment = $this->service->createAssignment($elseDom);
					$returnValue = $this->service->setAssignment($inferenceRule, 'else', $newAssignment);
				}
				
			}elseif($_POST['else_choice'] == 'inference'){
				//check if the current "else" is already an inference rule:
				$elseIsInferenceRule = false;
				$else = $inferenceRule->getOnePropertyValue($inferenceElseProp);//assignment, inference rule or null
				
				if(!empty($else)){
					if($else instanceof core_kernel_classes_Resource){
						if($else->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE))->uriResource == CLASS_INFERENCERULES){
							$elseIsInferenceRule = true;
							$returnValue = true;//no need to do anything, since the inference rule already exists
						}
					}
				}
				if(!$elseIsInferenceRule){
					//create a new inference rule
					$newInferenceRule = null;
					$newInferenceRule = $this->service->createInferenceRule($inferenceRule, 'inferenceRuleElse');
					
					if(!is_null($newInferenceRule)){
						$returnValue = true;
					}
				}
			}else{
				//find what is in the "else" property of the current inference rule, and delete it:
				
				$else = $inferenceRule->getOnePropertyValue($inferenceElseProp);
				
				if(!is_null($else)){//delete it whatever it is (assignment or inference rule)
					if($else instanceof core_kernel_classes_Resource){
						$type = $else->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE));
						if($type->uriResource == CLASS_ASSIGNMENT){
							$this->service->deleteAssignment($else);
						}elseif($type->uriResource == CLASS_INFERENCERULES){
							$this->service->deleteInferenceRule($else);
						}
					}
				}
				
				//remove property value:
				$inferenceRule->removePropertyValues($inferenceElseProp);
				$returnValue = true;
			}
		}
		
		echo json_encode(array('saved'=>$returnValue));
	}
	
	public function saveConsistencyRule(){
		
		$returnValue = false;
		
		$consistencyRule = new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['consistencyRuleUri']));
		
		//save the "if":
		$conditionString = $_POST['if'];
		if(!empty($conditionString)){
			$this->service->editCondition($consistencyRule, $conditionString);
		}
		
		//save activities:
		$encodedActivitiesPropUri = tao_helpers_Uri::encode(PROPERTY_CONSISTENCYRULES_INVOLVEDACTIVITIES);
		$activities = array();
		foreach($_POST as $propUri => $propValue){
			if(strpos($propUri, $encodedActivitiesPropUri) === 0){
				
				$activitiUri  = tao_helpers_Uri::decode($propValue);
				$activities[$activitiUri] = new core_kernel_classes_Resource($activitiUri);
			}
		}
		if(!empty($activities)){
			$this->service->setConsistencyActivities($consistencyRule, $activities);
		}
		
		//save suppressable:
		$this->service->setConsistencySuppressable($consistencyRule, tao_helpers_Uri::decode($_POST[tao_helpers_Uri::encode(PROPERTY_CONSISTENCYRULES_SUPPRESSABLE)]));
		
		//save notfication:
		$returnValue = $this->service->setConsistencyNotification($consistencyRule, $_POST[tao_helpers_Uri::encode(PROPERTY_CONSISTENCYRULES_NOTIFICATION)]);
		
		echo json_encode(array('saved'=>$returnValue));
	}
	
	
	public function checkCode(){
	
		$code = $_POST['code'];
		$processVarUri = tao_helpers_Uri::decode($_POST['varUri']);
		
		$returnValue = array('exist' => false);
		
		$processVar = $this->service->getProcessVariable($code);
		if(!is_null($processVar)){
			if($processVarUri != $processVar->uriResource){
				$returnValue['exist'] = true;
				$returnValue['label'] = $processVar->getLabel();
			}
		}
		
		echo json_encode($returnValue);
	}
	
	public function checkExpressionVariables(){
		
		$process = $this->getCurrentProcess();
		$expression = '';
		//to sources of problem: 
		//1-either the code is not associate to any process var ('the process var does not exist') 
		//2-or the process var is not set to the current process
		$returnValue = array(
			'doesNotExist'=>array(),
			'notSet'=>array()
		);
		
		$codes = array();
		//regular expression on the expression and get an array of process variable codes:
		if(preg_match_all('/\^(\w+)/', $expression, $matches)){
			$codes = $matches[1];
		}
		
		$processVarProp = new core_kernel_classes_Property(PROPERTY_PROCESS_VARIABLE);
		foreach($codes as $code){
			//get the variable resource: 
			$processVar = $this->service->getProcessVariable($code);
			if(is_null($processVar)){
				$returnValue['doesNotExist'][] = $code;
			}else{
				//check if the variable is set as a process variable of the current process
				$processCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject($processVarProp, $processVar);
				$ok = false;
				foreach($processCollection->getIterator() as $processTemp){
					if($processTemp->uriResource == $process->uriResource){
						$ok = true;
						break;
					}
				}
				if(!$ok){
					//the variable is not a variable of the current process:
					$returnValue['notSet'][] = $code;
				}
			}
		}
		echo json_encode($returnValue);
	}
	
	public function getOneSubject(core_kernel_classes_Property $property, core_kernel_classes_Resource $resource, $last=false){
		$subject = null;
		$subjectCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject($property->uriResource , $resource->uriResource);
		if(!$subjectCollection->isEmpty()){
			if($last){
				$subject = $subjectCollection->get($subjectCollection->count()-1);
			}else{
				$subject = $subjectCollection->get(0);
			}
		}
		return $subject;
	}
	
	/**
	 *
	 *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @return void
	 */
	public function preview(){
		
	}
	
	public function setFirstActivity(){
		$activity = $this->getCurrentActivity();
		$process = $this->getCurrentProcess();
		
		$returnValue = $this->service->setFirstActivity($process, $activity);
	
		echo json_encode(array('set' => $returnValue));
	}
	
	public function unsetLastActivity(){
		$activity = $this->getCurrentActivity();
		$created = false;
		
		//TODO: check if there is really no connector for the activity
		$connector = $this->service->createConnector($activity);
		if(!is_null($connector)){
			$created = true;
		}
		
		echo json_encode(array('created' => $created));
	}
	
	/*
	 * @TODO implement the following actions
	 */
	
	public function getMetaData(){
		throw new Exception("Not implemented yet");
	}
	
	public function saveComment(){
		throw new Exception("Not implemented yet");
	}
	
}
?>