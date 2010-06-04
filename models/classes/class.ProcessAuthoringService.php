<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every service instances.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('tao/models/classes/class.Service.php');

/**
 * 
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('wfEngine/plugins/CapiXML/models/class.ConditionalTokenizer.php');

/**
 * 
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('wfEngine/plugins/CapiImport/models/class.DescriptorFactory.php');

/**
 * The wfEngine_models_classes_ProcessAuthoringService class provides methods to access and edit the process ontology
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_models_classes_ProcessAuthoringService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---
	
	protected $processUri = '';
		
			
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct()
    {
		parent::__construct();
		
    }
	
	/**
     * Returns a delivery by providing either its uri (default) or its label and the delivery class
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getInstance($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null){
        $returnValue = null;

		if(is_null($clazz) || !$this->isAuthorizedClass($clazz)){
			return $returnValue;
		}
		$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
				
        return $returnValue;
    }
		
	/**
     * Method to be called to delete an instance
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource instance
     * @return boolean
     */
    public function deleteInstance( core_kernel_classes_Resource $instance){
        $returnValue = (bool) false;
		
		if(!is_null($instance)){
			$returnValue = $instance->delete();
		}

        return (bool) $returnValue;
    }
	
	/**
     * Description
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
     * @return core_kernel_classes_Resource
     */
	public function createInteractiveService(core_kernel_classes_Resource $activity){
		$number = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->count();
		$number += 1;
		
		//an interactive service of an activity is a call of service:
		$callOfServiceClass = new core_kernel_classes_Class(CLASS_CALLOFSERVICES);
		
		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT
		$callOfService = $callOfServiceClass->createInstance("InteractiveService_$number", "created by ProcessAuthoringService.Class");
		
		if(!empty($callOfService)){
			//associate the new instance to the activity instance
			$activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $callOfService->uriResource);
			
			//set default position and size value:
			$callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH), 100);
			$callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT), 100);
			$callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP), 0);
			$callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT), 0);
		}else{
			throw new Exception("the interactive service cannot be created for the activity {$activity->uriResource}");
		}
		
		return $callOfService;
	}
	
	/**
     * Description
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
	 * @param  core_kernel_classes_Resource formalParam
	 * @param  string value
	 * @param  string parameterInOrOut
	 * @param  string actualParameterType
     * @return boolean
     */
	public function setActualParameter(core_kernel_classes_Resource $callOfService, core_kernel_classes_Resource $formalParam, $value, $parameterInOrOut, $actualParameterType=PROPERTY_ACTUALPARAM_CONSTANTVALUE){
		
		//to be clarified:
		// $actualParameterType = PROPERTY_ACTUALPARAM_CONSTANTVALUE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
		
		$actualParameterClass = new core_kernel_classes_Class(CLASS_ACTUALPARAMETER);
		
		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT
		$newActualParameter = $actualParameterClass->createInstance($formalParam->getLabel(), "created by Process Authoring Service");
		$newActualParameter->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAM), $formalParam->uriResource);
		$newActualParameter->setPropertyValue(new core_kernel_classes_Property($actualParameterType), $value);
	
		return $callOfService->setPropertyValue(new core_kernel_classes_Property($parameterInOrOut), $newActualParameter->uriResource);
	}
	
	/**
     * Clean the triples for a call of service and its related resource (i.e. actual parameters)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource callOfService
     * @return boolean
     */
	public function deleteActualParameters(core_kernel_classes_Resource $callOfService){
		
		$returnValue = (bool) false;
		
		//remove the property values in the call of service instance
		$callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMIN));
		$callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT));
		
		//get all actual param of the current call of service
		$actualParamCollection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
		$actualParamCollection = $actualParamCollection->union($callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT)));
		if($actualParamCollection->count()<=0){
			return true;//no need to delete anything
		}
		
		//delete all of them:
		foreach($actualParamCollection->getIterator() as $actualParam){
		
			if($actualParam instanceof core_kernel_classes_Resource){
				$returnValue=$actualParam->delete();
				if(!$returnValue) {
					return (bool) $returnValue;
				}
			}
		}
		
		return (bool) $returnValue;
	}
	
	/**
     * Clean the triples for a transition rule and its related resource
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource rule
     * @return boolean
     */
	public function deleteRule(core_kernel_classes_Resource $rule){//transition rule only!!!!
		$returnValue = false;
		
		//get the rule type:
		if(!is_null($rule)){
			$this->deleteCondition($rule);
			
			//delete reference:
			$this->deleteReference(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $rule);
			
			//delete the resources
			$returnValue = $rule->delete($rule);
		}
		
		return $returnValue;
	}
	
	public function deleteCondition(core_kernel_classes_Resource $rule){
		$returnValue = false;
		
		//get the rule type:
		if(!is_null($rule)){
			//if it is a transition rule: get the uri of the related properties: THEN and ELSE:
			//delete the expression of the conditio and its related terms
			$expression = $rule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			if(!is_null($expression) && ($expression instanceof core_kernel_classes_Resource) ){
				$this->deleteExpression($expression);
			}
			
			//delete reference: should be done on a upper level, at this function call
		}
		
		return $returnValue;
	}
	/**
     * Clean the triples for an expression and its related resource
	 * note: always recursive: delete the expressions that make up the current expression
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource expression
     * @return boolean
     */
	public function deleteExpression(core_kernel_classes_Resource $expression){
		
		$returnValue = false;
		
		//delete related expressions
		$firstExpressionCollection = $expression->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_EXPRESSION_FIRSTEXPRESSION));
		$secondExpressionCollection = $expression->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_EXPRESSION_SECONDEXPRESSION));
		$expressionCollection = $firstExpressionCollection->union($secondExpressionCollection);
		foreach($expressionCollection->getIterator() as $exp){
			$this->deleteExpression($exp);
		}
		
		$terminalExpression = $expression->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_EXPRESSION_TERMINALEXPRESSION));
		if(!empty($terminalExpression) && $terminalExpression instanceof core_kernel_classes_Resource){
			$this->deleteTerm($terminalExpression);
		}
		
		//delete the expression itself:
		$returnValue = $expression->delete();
		
		return $returnValue;
	}
	
	/**
     * Clean the ontology from a process triples
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
     * @return boolean
     */
	public function deleteProcess(core_kernel_classes_Resource $process){
		
		$returnValue = false;
		
		if(!is_null($process)){
			$activities = $this->getActivitiesByProcess($process);
			foreach($activities as $activity){
				if(!$this->deleteActivity($activity)){
					return $returnValue;
				}
			}
			
			$returnValue = $process->delete();
		}
		
		return $returnValue;
	}
	
	/**
     * Clean the ontology from an activity triples
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
     * @return boolean
     */
	public function deleteActivity(core_kernel_classes_Resource $activity){
		
		$returnValue = false;
		
		$apiModel = core_kernel_impl_ApiModelOO::singleton();
		
		
		
		
		//delete related connector
		$connectorCollection = $apiModel->getSubject(PROPERTY_CONNECTORS_ACTIVITYREFERENCE , $activity->uriResource);
		foreach($connectorCollection->getIterator() as $connector){
			$this->deleteConnector($connector);
		}
		
		//delete reference to this activity from previous ones, via connectors
		$prevConnectorCollection = $apiModel->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES , $activity->uriResource);
		foreach($prevConnectorCollection->getIterator() as $prevConnector){
			$apiModel->removeStatement($prevConnector->uriResource, PROPERTY_CONNECTORS_NEXTACTIVITIES, $activity->uriResource, '');
			
			/*
			//cleaner method to delete all the reference but much slower
			//get the type of connector is "split", delete the reference in the transition rule: either PROPERTY_TRANSITIONRULES_THEN or ELSE
			if($prevConnector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->uriResource == INSTANCE_TYPEOFCONNECTORS_SPLIT){
				
				//get the transition rule:
				$transitonRule = $prevConnector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				if(!is_null($transitonRule) && $transitonRule instanceof core_kernel_classes_Resource){
					
					$then = $transitonRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN));
					if(!is_null($then) && $then instanceof core_kernel_classes_Resource){
						if($then->uriResource == $activity->uriResource){
						
						}
					}
				
				}
			
			}
			*/
		}
		
		//clean reference in transition rule (faster method)
		// $thenCollection = $apiModel->getSubject(PROPERTY_TRANSITIONRULES_THEN , $activity->uriResource);
		// foreach($thenCollection->getIterator() as $transitionRule){
			// $apiModel->removeStatement($transitionRule->uriResource, PROPERTY_TRANSITIONRULES_THEN, $activity->uriResource, '');
		// }
		// $elseCollection = $apiModel->getSubject(PROPERTY_TRANSITIONRULES_ELSE , $activity->uriResource);
		// foreach($elseCollection->getIterator() as $transitionRule){
			// $apiModel->removeStatement($transitionRule->uriResource, PROPERTY_TRANSITIONRULES_ELSE, $activity->uriResource, '');
		// }
		$this->deleteReference(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), $activity);
		$this->deleteReference(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), $activity);
		
		//delete inference rules:
		foreach($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE))->getIterator() as $inferenceRule){
			$this->deleteInferenceRule($inferenceRule);
		}
		foreach($activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE))->getIterator() as $inferenceRule){
			$this->deleteInferenceRule($inferenceRule);
		}
		
		//delete activity itself:
		$returnValue = $this->deleteInstance($activity);
		
		//delete the activity reference in the process instance.
		$processCollection = $apiModel->getSubject(PROPERTY_PROCESS_ACTIVITIES , $activity->uriResource);
		if(!$processCollection->isEmpty()){
			$apiModel->removeStatement($processCollection->get(0)->uriResource, PROPERTY_PROCESS_ACTIVITIES, $activity->uriResource, '');
		}else{
			return false;
		}
		
		return $returnValue=true;
	}
	
	/**
     * delete a connector and its related resources
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
     * @return boolean
     */
	public function deleteConnector(core_kernel_classes_Resource $connector){
		
		$returnValue = false;
		
		if(!self::isConnector($connector)){
			throw new Exception("the resource in the parameter is not a connector");
			return $returnValue;
		}
		
		//get the type of connector:
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
		if(!is_null($connectorType) && $connectorType instanceof core_kernel_classes_Resource){
			if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_SPLIT){
				//delete the related rule:
				$relatedRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				if(!is_null($relatedRule)){
					$this->deleteRule($relatedRule);//warning: do not do this for a join connector
				}
			}
		}
		
		
		//manage the connection to the previous activities: clean the reference to this connector:
		$previousActivityCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
		foreach($previousActivityCollection->getIterator() as $previousActivity){
			if($this->isConnector($previousActivity)){
				core_kernel_impl_ApiModelOO::singleton()->removeStatement($previousActivity->uriResource, PROPERTY_CONNECTORS_NEXTACTIVITIES, $connector->uriResource, '');
			}
		}
		
		//manage the connection to the following activities
		$activityRef = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
		$nextActivityCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
		foreach($nextActivityCollection->getIterator() as $nextActivity){
			if($this->isConnector($nextActivity)){
				$nextActivityRef = $nextActivity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
				if($nextActivityRef == $activityRef){
					$this->deleteConnector($nextActivity);//delete following connectors only if they have the same activity reference
				}
			}
		}
		
		//delete connector itself:
		$returnValue = $this->deleteReference(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES), $connector);
		// $returnValue = $this->deleteReference(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $connector);
		$returnValue = $this->deleteInstance($connector);
		
		return $returnValue;
	}
	
	public function deleteConnectorNextActivity(core_kernel_classes_Resource $connector, $type='next'){
		
		// $authorizedProperties = array(
			// PROPERTY_CONNECTORS_NEXTACTIVITIES,
			// PROPERTY_TRANSITIONRULES_THEN,
			// PROPERTY_TRANSITIONRULES_ELSE
		// );
		$nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		
		switch($type){
			case 'next':{
				$property = $nextActivitiesProp;
				break;
			}
			case 'then':{
				$property = new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN);
				break;
			}
			case 'else':{
				$property = new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE);
				break;
			}
			default:{
				throw new Exception('Trying to delete the value of an unauthorized connector property');
			}
		}
		
		$activityRefProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
		$activityRef = $connector->getUniquePropertyValue($activityRefProp)->uriResource;
	
		if($property->uriResource == PROPERTY_CONNECTORS_NEXTACTIVITIES){
			//manage the connection to the following activities
			$nextActivityCollection = $connector->getPropertyValuesCollection($property);
			foreach($nextActivityCollection->getIterator() as $nextActivity){
				if(self::isConnector($nextActivity)){
					$nextActivityRef = $nextActivity->getUniquePropertyValue($activityRefProp)->uriResource;
					if($nextActivityRef == $activityRef){
						$this->deleteConnector($nextActivity);//delete following connectors only if they have the same activity reference
					}
				}
			}
			$connector->removePropertyValues($nextActivitiesProp);
		}elseif(($property->uriResource == PROPERTY_TRANSITIONRULES_THEN)||($property->uriResource == PROPERTY_TRANSITIONRULES_ELSE)){
			//it is a split connector: get the transition rule, if exists
			$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTOR_TRANSITIONRULE));
			if(!is_null($transitionRule)){
				$nextActivity = $transitionRule->getOnePropertyValue($property);
				if(!is_null($nextActivity)){
					if(self::isConnector($nextActivity)){
						
						$nextActivityRef = $nextActivity->getUniquePropertyValue($activityRefProp)->uriResource;
						if($nextActivityRef == $activityRef){
							$this->deleteConnector($nextActivity);//delete following connectors only if they have the same activity reference
						}
					}
					$this->deleteReference($nextActivitiesProp, $nextActivity);
					$this->deleteReference($property, $nextActivity);
				}
			}
		}
		
	}
	
	/**
     * delete the reference to an object via a given property
	 *Useful when the object has been deleted and the sources related to it must be deleted reference to it.
     *
	 * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource property
	 * @param  core_kernel_classes_Resource object
	 * @param  boolean multiple
     * @return boolean
     */
	public function deleteReference(core_kernel_classes_Property $property, core_kernel_classes_Resource $object, $multiple = false){
		
		$returnValue = false;
		
		$apiModel = core_kernel_impl_ApiModelOO::singleton();
		
		$subjectCollection = $apiModel->getSubject($property->uriResource, $object->uriResource);
		if(!$subjectCollection->isEmpty()){
			if($multiple){
				$returnValue = true;
				foreach($subjectCollection->getIterator() as $subject){
					if( !$apiModel->removeStatement($subjectCollection->get(0)->uriResource, $property->uriResource, $object->uriResource, '') ){
						$returnValue = false;
						break;
					}
				}
			}else{
				$returnValue = $apiModel->removeStatement($subjectCollection->get(0)->uriResource, $property->uriResource, $object->uriResource, '');
			}
		}else{
			$returnValue = true;
		}
		
		return $returnValue;
	}
	
    /**
     * Check whether the class is authorized 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Class clazz
     * @return boolean
     */
    public function isAuthorizedClass( core_kernel_classes_Class $clazz){
	
        $returnValue = (bool) false;

		$authorizedClassUri=array(
			CLASS_ACTIVITIES,
			CLASS_PROCESSVARIABLES,
			CLASS_SERVICESDEFINITION,
			CLASS_WEBSERVICES,
			CLASS_SUPPORTSERVICES,
			CLASS_FORMALPARAMETER,
			CLASS_ROLE_BACKOFFICE,
			CLASS_PROCESS
		);
		
		if( in_array($clazz->uriResource, $authorizedClassUri) ){
			$returnValue = true;	
		}
		
        return (bool) $returnValue;
    }
	
	/**
     * Create an activity for a process
     *
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
	 * @param  string label
     * @return core_kernel_classes_Resource
     */	
	public function createActivity(core_kernel_classes_Resource $process, $label=''){
		
		$activity = null;
		$activityLabel = "";
		$number = 0;
		
		if(empty($label)){
			$number = $process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->count();
			$number += 1;
			$activityLabel = "Activity_$number";
		}else{
			$activityLabel = $label;
		}
		
		$activityClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
		$activity = $activityClass->createInstance($activityLabel, "created by ProcessAuthoringService.Class");
		
		if(!empty($activity)){
			//associate the new instance to the process instance
			$process->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activity->uriResource);
		
			//set if it is the first or not:
			if($number == 1){
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
			}else{
				$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
			}
			
			//by default, set the 'isHidden' property value to false:
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), GENERIS_FALSE);
			
		}else{
			throw new Exception("the activity cannot be created for the process {$process->uriResource}");
		}
		return $activity;
	}
	
	/**
     * Create a connector for an activity
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
	 * @param  string label
     * @return core_kernel_classes_Resource
     */	
	public function createConnector(core_kernel_classes_Resource $activity, $label=''){
		$connectorLabel = "";
		if(empty($label)){
			$connectorLabel = $activity->getLabel()."_c";//warning: could exist duplicate for children of a split connector
		}else{
			$connectorLabel = $label;
		}
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$connector = $connectorClass->createInstance($connectorLabel, "created by ProcessAuthoringService.Class");
		if(!empty($connector)){
			//associate the connector to the activity
			$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES), $activity->uriResource);
			
			//set the activity reference of the connector:
			$activityRefProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
			if($this->isActivity($activity)){
				$connector->setPropertyValue($activityRefProp, $activity->uriResource);
			}elseif($this->isConnector($activity)){
				$connector->setPropertyValue($activityRefProp, $activity->getUniquePropertyValue($activityRefProp)->uriResource);
			}else{
				var_dump($activity);
				throw new Exception("invalid resource type for the activity parameter: {$activity->uriResource}");
			}
		}else{
			throw new Exception("the connector cannot be created for the activity {$activity->uriResource}");
		}
		return $connector;
	}
	
	/**
     * Create a new activity and assign it the next activity of a connector
	 * If the activity already exists and is put in the parameter, simply set it as the next activity of a connector  
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
	 * @param  core_kernel_classes_Resource followingActivity
	 * @param  string newActivityLabel
     * @return void
     */	
	public function createSequenceActivity(core_kernel_classes_Resource $connector, core_kernel_classes_Resource $followingActivity = null, $newActivityLabel = ''){
		
		$this->setConnectorType($connector, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
		
		if(is_null($followingActivity)){
			$followingActivity = $this->createActivityFromConnector($connector, $newActivityLabel);
		}
		if($followingActivity instanceof core_kernel_classes_Resource){
			//associate it to the property value of the connector
			$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $followingActivity->uriResource);
		}
		
		return $followingActivity;
	}
	
	public function createActivityFromConnector(core_kernel_classes_Resource $connector, $newActivityLabel){
		//get the process associate to the connector to create a new instance of activity
		$relatedActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
		$processCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_PROCESS_ACTIVITIES, $relatedActivity->uriResource);
		if(!$processCollection->isEmpty()){
			$followingActivity = $this->createActivity($processCollection->get(0), $newActivityLabel);
			//warning: a connector is created at the same time of the activity:
			$newConnector = $this->createConnector($followingActivity);
		}else{
			throw new Exception("no related process instance found to create an activity");
		}
		
		return $followingActivity;
	}
	
	/**
	 * Create a rule according to a condition
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
	 * @param  string condiiton
     * @return core_kernel_classes_Resource
     */	
	public function createRule(core_kernel_classes_Resource $connector, $question=''){//transiiton rule only! rename as such!
		
		$returnValue = null;
			
		// $xmlDom = $this->analyseExpression($condition);
		$condition = $this->createCondition( $this->analyseExpression($question, true) );

		if($condition instanceof core_kernel_classes_Resource){
			//associate the newly create expression with the transition rule of the connector
			$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));

			if(empty($transitionRule) || $transitionRule == null){
				//create an instance of transition rule:

				$transitionRuleClass = new core_kernel_classes_Class(CLASS_TRANSITIONRULES);
				$transitionRule = $transitionRuleClass->createInstance('TransitionRule : '  . $question);
				//Associate the newly created transition rule to the connector:
				$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $transitionRule->uriResource);
			}
			//delete old condition:
			$oldCondition = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			if(!is_null($oldCondition)){
				$this->deleteCondition($oldCondition);
			}
			$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_RULE_IF), $condition->uriResource);
			$returnValue = $transitionRule;
		}

		return $returnValue;
	}
	
	public function analyseExpression($condition, $isCondition = false){
		//place the following bloc in a helper
		if (!empty($condition))
			$question = $condition;
		else
			$question = "";
		
		//question test:
		//$question = "IF    (11+B_Q01a*3)>=2 AND (B_Q01c=2 OR B_Q01c=7)    	THEN ^variable := 2*(B_Q01a+7)-^variable";
		
		//analyse the condition string and convert to an XML document:
		if (get_magic_quotes_gpc()) $question = stripslashes($question);// Magic quotes are deprecated
		//TODO: check if the variables exists and are associated to the process definition 
		
		$xmlDom = null;
		if (!empty($question)){ // something to parse
			// str_replace taken from the MsReader class
			$question = str_replace("’", "'", $question); // utf8...
			$question = str_replace("‘", "'", $question); // utf8...
			$question = str_replace("“", "\"", $question);
			$question = str_replace("”", "\"", $question);
			if($isCondition){
				$question = "if ".$question;
			}	
			try{
				$analyser = new Analyser();
				$tokens = $analyser->analyse($question);

				// $xml = htmlspecialchars($tokens->getXmlString(true));
				// $xml = $tokens->getXmlString(true);
				
				$xmlDom = $tokens->getXml();
				
			}catch(Exception $e){
				throw new Exception("CapiXML error: {$e->getMessage()}");
			}
		}
		return $xmlDom;
	}
	
	//^SCR = ((^SCR)*31+^SCR*^SCR) => fail
	public function createCondition($xmlDom){
		//create the expression instance:

		$condition = null;
		foreach ($xmlDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "condition"){
					
					$conditionDescriptor = DescriptorFactory::getConditionDescriptor($childOfChildNode);
					$condition = $conditionDescriptor->import();//(3*(^var +  1) = 2 or ^var > 7) AND ^RRR
					break 2;//once is enough...
				
				}
			}
		}

		return $condition;
	}
	
	public function editCondition($rule, $conditionString){
		
		$returnValue = false;
		
		if(!empty($conditionString)){
			$conditionDom =  $this->analyseExpression($conditionString, true);
			$condition = $this->createCondition($conditionDom);
			if(is_null($condition)){
				throw new Exception("the condition \"{$conditionString}\" cannot be created for the inference rule {$rule->getLabel()}");
			}else{
				//delete old condition if exists:
				$this->deleteCondition($rule);
				
				//associate the new condition:
				$returnValue = $rule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_RULE_IF), $condition->uriResource);
			}
		}
		
		return $returnValue;
	}
		
	public function createAssignment($xmlDom){
		//create the expression instance:
		$assignment = null;
		foreach ($xmlDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "then"){
					
					$assignmentDescriptor = DescriptorFactory::getAssignDescriptor($childOfChildNode);
					$assignment = $assignmentDescriptor->import();//(3*(^var +  1) = 2 or ^var > 7) AND ^RRR
					break 2;//stop at the first occurence of course
				}
			}
		}
		return $assignment;
	}
	
	public function createInferenceRule(core_kernel_classes_Resource $activity, $type, $label=''){
		
		//note: the resource in the parameter "activity" can be either an actual activity or a parent inferenceRule
		
		$inferenceRule = null;
		
		switch($type){
			case 'onBefore':{
				$inferenceRuleProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE);
				break;
			}
			case 'onAfter': {
				$inferenceRuleProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE);
				break;
			}
			case 'inferenceRuleElse': {
				$inferenceRuleProp = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE);
				if(empty($label)){
					$label = ' ';
				}
				break;
			}
			default:{
				return $inferenceRule;
			}
		}
		
		$inferenceRuleLabel = "";
		if(empty($label)){
			// $activity->getPropertyValuesCollection($inferenceRuleProp);
			$nb = $activity->getPropertyValuesCollection($inferenceRuleProp)->count()+1;
			$inferenceRuleLabel = "$type Inference Rule $nb";
		}else{
			$inferenceRuleLabel = $label;
		}
		
		$inferenceRuleClass = new core_kernel_classes_Class(CLASS_INFERENCERULES);
		$inferenceRule = $inferenceRuleClass->createInstance($inferenceRuleLabel, "created by ProcessAuthoringService.Class");
		
		if(!empty($inferenceRule)){
			//associate the inference rule to the activity or the parent inference rule
			if($type == 'inferenceRuleElse'){
				$activity->editPropertyValues($inferenceRuleProp, $inferenceRule->uriResource);//only one single inference rule is allowed 
			}else{
				//we add a new inference rule to an activity
				$activity->setPropertyValue($inferenceRuleProp, $inferenceRule->uriResource);
			}
		}else{
			throw new Exception("the inference rule cannot be created for the activity {$activity->getLabel()}: {$activity->uriResource}");
		}
		return $inferenceRule;
	}
	
	public function createConsistencyRule(core_kernel_classes_Resource $activity, $label=''){
		
		$consistency = null;
		
		$consistencyRuleLabel = "";
		if(empty($label)){
			$nb = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONSISTENCYRULE))->count()+1;
			$consistencyRuleLabel = "Consistency Rule $nb";
		}else{
			$consistencyRuleLabel = $label;
		}
		
		$consistencyRuleClass = new core_kernel_classes_Class(CLASS_CONSISTENCYRULES);
		$consistencyRule = $consistencyRuleClass->createInstance($consistencyRuleLabel, "created by ProcessAuthoringService.Class");
		
		if(!empty($consistencyRule)){
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONSISTENCYRULE), $consistencyRule->uriResource);//only one single inference rule is allowed 
		}else{
			throw new Exception("the consistency rule cannot be created for the activity {$activity->getLabel()}: {$activity->uriResource}");
		}
		
		return $consistencyRule;
	}
	
	public function deleteInferenceRule(core_kernel_classes_Resource $inferenceRule){
		// $if = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));//conditon or null
		$then = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_INFERENCERULES_THEN));//assignment or null only
		$else = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE));//assignment, inference rule or null
		
		$this->deleteCondition($inferenceRule);
		
		if(!is_null($then) && ($then instanceof core_kernel_classes_Resource) ){
			$this->deleteAssignment($then);
		}
		
		if(!is_null($else) && ($then instanceof core_kernel_classes_Resource) ){
			$classUri = $else->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE))->uriResource;
			if($classUri == CLASS_ASSIGNMENT){
				$this->deleteAssignment($else);
			}elseif($classUri == CLASS_INFERENCERULES){
				$this->deleteInferenceRule($else);
			}
		}
		
		//last: delete the reference to this inferenceRule in case of successive inference rule:
		$this->deleteReference(new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE), $inferenceRule);
		$this->deleteReference(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONAFTERINFERENCERULE), $inferenceRule);
		// $this->deleteReference(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ONBEFOREINFERENCERULE), $inferenceRule);
		
		return $inferenceRule->delete();
	}
	
	public function deleteConsistencyRule(core_kernel_classes_Resource $consistencyRule){
		$this->deleteCondition($consistencyRule);
		$this->deleteReference(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONSISTENCYRULE), $consistencyRule);
		return $consistencyRule->delete();
	}
	
	public function deleteAssignment(core_kernel_classes_Resource $assignment, $fullDelete = true){
		
		if(!is_null($assignment)){
		
			$assignmentVariable = $assignment->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ASSIGNMENT_VARIABLE));
			//should be an SPX:
			if($assignmentVariable instanceof core_kernel_classes_Resource){
				$assignmentVariable->delete();
			}
			
			$assignmentValue = $assignment->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ASSIGNMENT_VALUE));
			// var_dump($assignment, $assignmentValue);
			if(!is_null($assignmentValue)){
				//could be a term, an operation or a constant (even though its range is resource)
				if($assignmentValue instanceof core_kernel_classes_Resource){
					
					$this->deleteTerm($assignmentValue);
					
				}
			}
			
			if($fullDelete){
				$assignment->delete();
			}
		
		}
		
		return true;
	}
	
	public function deleteOperation(core_kernel_classes_Resource $operation){
			
		$firstOperand = $operation->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP));
		if(!is_null($firstOperand) && ($firstOperand instanceof core_kernel_classes_Resource)){
			$this->deleteTerm($firstOperand);
		}
		
		$secondOperand = $operation->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP));
		if(!is_null($secondOperand) && ($secondOperand instanceof core_kernel_classes_Resource)){
			$this->deleteTerm($secondOperand);
		}
		
		return $operation->delete();
	}
	
	public function deleteTerm(core_kernel_classes_Resource $term){
		$termClasses = array(
			CLASS_TERM_SUJET_PREDICATE_X,
			CLASS_TERM_CONST
		);
		
		//list of terms instance that must not be deleted!
		$termConstants = array(
			INSTANCE_TERM_IS_NULL
		);
		
		if(!is_null($term)){
			//determine the class:
			$classUri = $term->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE))->uriResource;
			
			if($classUri == CLASS_OPERATION){
				
				$this->deleteOperation($term);//an operation is a term
				
			}elseif(in_array($classUri,$termClasses)){
			
				if(!in_array($term->uriResource, $termConstants)){//delete all instances but the one that are preset
					$term->delete();
				}
				
			}else{
				throw new Exception("trying to delete a term with an unknown term class");
			}
		}
	}
	
	/**
     * Create the following activity for a connector.
	 * If the following activity is given, define it as the 'next' activity and the type: 'then' or 'else'
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource connector
	 * @param  string connectorType
	 * @param  core_kernel_classes_Resource followingActivity
	 * @param  string newActivityLabel
	 * @param  boolean followingActivityisConnector
     * @return void
     */	
	public function createSplitActivity(core_kernel_classes_Resource $connector, $connectorType, core_kernel_classes_Resource $followingActivity = null, $newActivityLabel ='', $followingActivityisConnector = false){
		
		$this->setConnectorType($connector, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SPLIT));
		
		//remove property PROPERTY_CONNECTORS_NEXTACTIVITIES values on connector before:
		if(is_null($followingActivity)){
			
			if($followingActivityisConnector){
				//create a new connector:
				$followingActivity = $this->createConnector($connector);
			}else{
				//get the process associate to the connector to create a new instance of activity
				$relatedActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
				$processCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_PROCESS_ACTIVITIES, $relatedActivity->uriResource);
				if(!$processCollection->isEmpty()){
					$followingActivity = $this->createActivity($processCollection->get(0), $newActivityLabel);
					$newConnector = $this->createConnector($followingActivity);
				}else{
					throw new Exception("no related process instance found to create an activity");
				}
			}
		}
		
		if($followingActivity instanceof core_kernel_classes_Resource){
			//associate it to the property value of the connector
			$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $followingActivity->uriResource);//use this function and not editPropertyValue!
			$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
			if(empty($transitionRule)){
				//create an instance of transition rule:
				$transitionRuleClass = new core_kernel_classes_Class(CLASS_TRANSITIONRULES);
				$transitionRule = $transitionRuleClass->createInstance("ruleOf".$connector->getLabel(),"generated by ProcessAuthoringService");
				//associate it to the connector:
				$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $transitionRule->uriResource);
			}
			if(strtolower($connectorType) == 'then'){
				$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), $followingActivity->uriResource);
			}elseif(strtolower($connectorType) == 'else'){
				$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), $followingActivity->uriResource);
			}
		}
		return $followingActivity;
	}
	
	/**
     * Get an array of activities of the process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
     * @return array
     */	
	public function getActivitiesByProcess(core_kernel_classes_Resource $process){
		
		$returnValue = array();
		
		//eventually, put $processUri in a class property
		if(empty($process) && !empty($this->processUri)){
			$process = new core_kernel_classes_Resource($this->processUri);
		}
		if(is_null($process)){
			throw new Exception("the process cannot be null");
			return $returnValue;
		}
		
		
		foreach ($process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->getIterator() as $activity){
			if($activity instanceof core_kernel_classes_Resource){
				$returnValue[$activity->uriResource] = $activity;
			}
		}
		
		return $returnValue;
	}
	
	public function getServicesByActivity(core_kernel_classes_Resource $activity){
		
	}	
	
	
	/**
     * Get all connectors of a process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource process
     * @return array
     */	
	public function getConnectorsByProcess(core_kernel_classes_Resource $process){
		$activities = $this->getActivitiesByProcess($process);
		$connectors = array();
		foreach($activities as $activity){
			$tempConnectorArray = array();
			$tempConnectorArray = $this->getConnectorsByActivity($activity, array('next'));//connectors of connector are not included here!
			//use the property value: activity reference here:	
			
		}
	
	}
	
	/**
     * Get all connectors of an activity
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource activity
	 * @param  array option
	 * @param  boolean isConnector
     * @return array
     */
	public function getConnectorsByActivity(core_kernel_classes_Resource $activity, $option=array(), $isConnector=false ){
			
		//prev: the connectors that links to the current activity
		//next: the connector (should be unique for an activiy that is not a connector itself) that follows the current activity
		$returnValue = array(
			'prev'=>array(),
			'next'=>array()
		);
		
		if(empty($option)){
		//the default option: select all connectors
			$option = array('prev','next');
		}else{
			$option = array_map('strtolower', $option);
		}
		
		if(in_array('prev',$option)){
		
			$previousConnectorsCollection=core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES, $activity->uriResource);
		
			foreach ($previousConnectorsCollection->getIterator() as $connector){
				if(!is_null($connector)){
					if($connector instanceof core_kernel_classes_Resource ){
						$returnValue['prev'][$connector->uriResource] = $connector; 
					}
				}
			}
		}
		
		if(in_array('next',$option)){
		
			$followingConnectorsCollection=core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_PRECACTIVITIES, $activity->uriResource);
		
			foreach ($followingConnectorsCollection->getIterator() as $connector){
				if(!is_null($connector)){
					if($connector instanceof core_kernel_classes_Resource){
						$returnValue['next'][$connector->uriResource] = $connector; 
						if($isConnector){
							continue; //continue selecting potential other following activities or connector
						}else{
							break; //select the unique FOLLOWING connector in case of a real activity  (i.e. not a connector)
						}
					}
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Check if the resource is an activity instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource resource
     * @return boolean
     */	
	public static function isActivity(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		$activityType = core_kernel_impl_ApiModelOO::singleton()->getObject($resource->uriResource, RDF_TYPE);
		if($activityType->count()>0){
			if($activityType->get(0) instanceof core_kernel_classes_Resource){//should be a generis class
				if( $activityType->get(0)->uriResource == CLASS_ACTIVITIES){
					$returnValue = true;
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Check if the resource is a connector instance
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  core_kernel_classes_Resource resource
     * @return boolean
     */	
	public static function isConnector(core_kernel_classes_Resource $resource){
		$returnValue = false;
		
		$activityType = core_kernel_impl_ApiModelOO::singleton()->getObject($resource->uriResource, RDF_TYPE);
		if($activityType->count()>0){
			if($activityType->get(0) instanceof core_kernel_classes_Resource){
				if( $activityType->get(0)->uriResource == CLASS_CONNECTORS){
					$returnValue = true;
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
     * Get the process variable with a given code
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string code
     * @return core_kernel_classes_Resource
     */
	public function getProcessVariable($code){
		$returnValue = null;
		
		$processVarCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $code);
		if(!$processVarCollection->isEmpty()){
			$returnValue = $processVarCollection->get(0);
		}
		
		return $returnValue;
	}
	
	//type: constant or processvariable
	function createFormalParameter($name, $type, $defaultValue, $label=''){
		
		if(strtolower($type) == 'constant'){
			$defaultValueProp = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE);
		}elseif(strtolower($type) == 'processvariable'){
			$defaultValueProp = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE);
		}else{
			return null;
		}
		
		$classFormalParam = new core_kernel_classes_Class(CLASS_FORMALPARAMETER);
		if(empty($label)){
			$label = $name;
		}
		$formalParam = $classFormalParam->createInstance($label, 'created by process authoring service');
		$formalParam->setPropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME), $name);
		$formalParam->setPropertyValue($defaultValueProp, $defaultValue);
		
		return $formalParam;
	}
	
	function getFormalParameter($name, $defaultValue = null){
		
		$returnValue = null;
		
		$classFormalParam = new core_kernel_classes_Class(CLASS_FORMALPARAMETER);
		
		
		foreach($classFormalParam->getInstances(true) as $formalParam){
			$nameResource = $formalParam->getOnePropertyvalue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME));
			$nameValue = null;
			if(!is_null($nameResource)){
			
				if($nameResource instanceof core_kernel_classes_Literal){
					$nameValue = $nameResource->literal;
				}else if($nameResource instanceof core_kernel_classes_Resource){
					$nameValue = $nameResource->uriResource;//encode??
				}
				
				if($nameValue == $name){
				
					if(is_null($defaultValue)){
					
						return $returnValue = $formalParam;
						
					}else{
						//check defaultvalue:
						
						$defaultConstantValueContainer = $formalParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE));
						if(!is_null($defaultConstantValueContainer)){
							if($defaultConstantValueContainer instanceof core_kernel_classes_Literal){
								$defaultConstantValue = $defaultConstantValueContainer->literal;
							}else if($defaultConstantValueContainer instanceof core_kernel_classes_Resource){
								$defaultConstantValue = $defaultConstantValueContainer->uriResource;
							}
							if($defaultConstantValue == $defaultValue){
								return $formalParam;
							}
						}
						
						$defaultProcessVariable = $formalParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE));
						if(!is_null($defaultProcessVariable)){
							if($defaultProcessVariable instanceof core_kernel_classes_Resource){
								if($defaultValue instanceof core_kernel_classes_Resource){
									if($defaultProcessVariable->uriResource == $defaultValue->uriResource){
										return $formalParam;
									}
								}else{
									if($defaultProcessVariable->uriResource == $defaultValue){
										return $formalParam;
									}
								}
							}
						}
						
					}
					
				}
			}
		}
		
		return $returnValue;
	}
	
	public function setAssignment(core_kernel_classes_Resource $inferenceRule, $type, $assignment){
		
		if($type == 'then'){
			$property = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_THEN);
		}else if($type == 'else'){
			$property = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE);
		}else{
			throw new Exception('unknown type of assignment');
		}
	
		//delete old assignment resource:
		$oldAssignment = $inferenceRule->getOnePropertyValue($property);
		if(!is_null($oldAssignment)){
			$this->deleteAssignment($oldAssignment);
		}
		
		//save new one:
		return $inferenceRule->editPropertyValues($property, $assignment->uriResource);
	}
	
	public function setActivityRole(core_kernel_classes_Resource $activity, core_kernel_classes_Resource $role){
		return $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ROLE), $role->uriResource);
	}
	
	public function setActivityHidden(core_kernel_classes_Resource $activity, $boolean){
		$returnValue = false;
		
		if(is_bool($boolean)){
			$generisBoolean = wfEngine_models_classes_ProcessAuthoringService::generisBooleanConvertor($boolean);
			if(!is_null($generisBoolean)){
				$returnValue = $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN), $generisBoolean->uriResource);
			}
		}
		
		return $returnValue;
	}
	
	public function setConnectorType(core_kernel_classes_Resource $connector,core_kernel_classes_Resource $typeOfConnector){
		
		//TODO: check range of type of connectors:
		return $connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), $typeOfConnector->uriResource);
	
	}
	
	public function setCallOfServiceDefinition(core_kernel_classes_Resource $callOfService, core_kernel_classes_Resource $serviceDefinition){
		return $callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
	}
	
	public function setConsistencySuppressable(core_kernel_classes_Resource $consistencyRule, $boolean){
		//$boolean is either GENERIS_TRUE or FALSE
		return $consistencyRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_SUPPRESSABLE), $boolean);
	}
	
	public function setConsistencyNotification(core_kernel_classes_Resource $consistencyRule, $notificationString){
		return $consistencyRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_NOTIFICATION), $notificationString);
	}
	
	public function setConsistencyActivities(core_kernel_classes_Resource $consistencyRule, $activities){
	
		$involvedActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_INVOLVEDACTIVITIES);
		$consistencyRule->removePropertyValues($involvedActivitiesProp);
		
		foreach($activities as $activityUri => $activity){
			$consistencyRule->setPropertyValue($involvedActivitiesProp, $activityUri);
		}
		return true;
	}
	
	public function setFirstActivity(core_kernel_classes_Resource $process, core_kernel_classes_Resource $activity){
		
		$returnValue = false;
		
		$activities = $this->getActivitiesByProcess($process);
		foreach($activities as $activityTemp){
			$activityTemp->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
		}
		
		$returnValue = $activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		
		return $returnValue;
	}
	
	//TODO create a helper and put it inside
	public static function generisBooleanConvertor($var){
		
		$returnValue = null;
		
		if(is_bool($var)){
			//is a boolean, so convert it to a generis boolean:
			if($var){
				$returnValue = new core_kernel_classes_Resource(GENERIS_TRUE);
			}else{
				$returnValue = new core_kernel_classes_Resource(GENERIS_FALSE);
			}
		}else{
			if($var instanceof core_kernel_classes_Resource){
				if($var->uriResource == GENERIS_TRUE){
					$returnValue = true;
				}elseif($var->uriResource == GENERIS_FALSE){
					$returnValue = false;
				}
			}elseif(is_string($var)){
				if($var == GENERIS_TRUE){
					$returnValue = true;
				}elseif($var == GENERIS_FALSE){
					$returnValue = false;
				}
			}
		}
		
		return $returnValue;
	}
	
	
		
	public function createJoinActivity(core_kernel_classes_Resource $connectorInstance, core_kernel_classes_Resource $followingActivity = null, $newActivityLabel = '', core_kernel_classes_Resource $previousActivity){
		
		$this->setConnectorType($connectorInstance, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_JOIN));
		
		$propNextActivity = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		
		if(is_null($followingActivity)){
			//TODO: create an activity if null:
			$followingActivity = $this->createActivityFromConnector($connectorInstance, $newActivityLabel);
		}else{
			//find if a join connector already leads to the following activity:
			$connectorCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES, $followingActivity->uriResource);
			foreach($connectorCollection->getIterator() as $connector){
				if($connector instanceof core_kernel_classes_Resource){
					if($connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTOR_TYPEOFCONNECTOR))->uriResource == INSTANCE_TYPEOFCONNECTORS_JOIN){
						//join connector found: connect the previous activity to that one:
						
						if(!is_null($previousActivity)){
							// echo 'connector found:';var_dump($connector);
							
							//important: check that the connector found is NOT the same as the current one:
							if($connectorInstance->uriResource != $connector->uriResource){
								//delete old connector, and associate the activity to that one:
								$this->deleteConnector($connectorInstance);
								// $connectorInstance = $connector;
								$connector->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES), $previousActivity->uriResource);
							}else{
								//nothing to do
							}
						}else{
							throw new Exception('no previous activity found to be connected to the next activity');
						}
						return $followingActivity;
					}
				}
			}
			
		}
		
		if($followingActivity instanceof core_kernel_classes_Resource){
			$connectorInstance->editPropertyValues($propNextActivity, $followingActivity->uriResource);
			$connectorInstance->setLabel(__("merge to ").$followingActivity->getLabel());
			return $followingActivity;
		}else{
			return null;
		}
		
	}
	
	public function updateJoinedActivity(core_kernel_classes_Resource $followingActivity){
		
		$joinConnectors = array();
		$conditionString = '';
		$prevConnectorsCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CONNECTORS_NEXTACTIVITIES, $followingActivity->uriResource);
		$currentProcessCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_PROCESS_ACTIVITIES, $followingActivity->uriResource);
		if($currentProcessCollection->isEmpty()){
			throw new Exception('');
			return false;
		}
		$currentProcess = $currentProcessCollection->get(0);
		
		foreach($prevConnectorsCollection->getIterator() as $prevConnector){
			if($prevConnector instanceof core_kernel_classes_Resource){
			
				$connectorType = null;
				$connectorType = $prevConnector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_JOIN){
					//TODO: check if the connector pre
					$transitionRuleTemp = $prevConnector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
					if($transitionRuleTemp instanceof core_kernel_classes_Resource){
						$this->deleteRule($transitionRuleTemp);
						// $transitionRule = $transitionRuleTemp;//note: the transition rule for these connectors should be exactly the same
					}
					$joinConnectors[] = $prevConnector;
					$previousActivity = $prevConnector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
					
					$activityProcessVar = $this->getProcessVariableForActivity($previousActivity);
					/*
					//create activity 'isFinished' process variable:
					$label = $previousActivity->getLabel();
					$code = 'activity';
					// var_dump($previousActivity, $previousActivity->uriResource, stripos($previousActivity->uriResource,".rdf#"));
					if(stripos($previousActivity->uriResource,".rdf#")>0){
						$code .= '_'.substr($previousActivity->uriResource, stripos($previousActivity->uriResource,".rdf#")+5);
					}
					//check if the code (i.e. the variable) does not exist yet:
					$activityProcessVar = $this->getProcessVariable($code);
					if(is_null($activityProcessVar)){
						$activityProcessVar = $this->createProcessVariable('isFinished: '.$label, $code);
					}*/
					
					//assign process var to the current process definition:
					if($activityProcessVar){
						$currentProcess->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_VARIABLE), $activityProcessVar->uriResource);
					}else{
						throw new Exception("the \"isfinished\" process variable of the activity {$activityProcessVar->uriResource} is empty");
					}
					
					//add statement assignation to activity prec:
					
					$conditionString .= "^".$activityProcessVar->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CODE))." == 'true' AND ";
				}
				
			}
		}
		$conditionString = substr_replace($conditionString,'',-4);
		// echo 'condition: '.$conditionString;
		
		//if transition rule exists, replace conditio (prop "if"):
		$transitionRule = null;
		$transitionRule = $this->createRule($prevConnector, $conditionString);
		$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), $followingActivity->uriResource);//how to set 'void' to 'ELSE'?
		
		//for each connector, except the current one (already set on the line above), set the transition rule:
		// echo 'joinConnectors:';var_dump($joinConnectors);
		foreach($joinConnectors as $connector){
			$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $followingActivity->uriResource);
			$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $transitionRule->uriResource);
			// var_dump('new transiitonrule:',$transitionRule);
		}
		
		return true;
	}
	
	public function createProcessVariable($label='', $code=''){
		$processVariable = null;
		
		if(!empty($code) && $this->getProcessVariable($code)){
			throw new Exception("A process variable with the code '{$code}' already exists");
		}
		
		$classCode = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$processVariable = $this->createInstance($classCode);
		
		if(!empty($label)){
			$processVariable->setLabel($label);
		}
		
		// $processVariable = $classCode->createInstance($label, 'created by ProcessAuthoringService');
		if(!empty($code)){
			$processVariable->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE), $code);
		}
		
		//set the new instance of process variable as a property of the class process instance:
		// $ok = core_kernel_impl_ApiModelOO::singleton()->setStatement($instance->uriResource, RDF_TYPE, RDF_PROPERTY, '');
		$ok = $processVariable->setPropertyValue(new core_kernel_classes_Property(RDF_TYPE), RDF_PROPERTY);
		if($ok){
			$newProcessInstanceProperty = new core_kernel_classes_Property($processVariable->uriResource);
			$newProcessInstanceProperty->setDomain(new core_kernel_classes_Class(CLASS_PROCESSINSTANCE));
			$newProcessInstanceProperty->setRange(new core_kernel_classes_Class(RDFS_LITERAL));//literal only??
		}else{
			throw new Exception("the newly created process variable {$label} ({$processVariable->uriResource}) cannot be set as a property of the class process instance");
		}
		
		return $processVariable;
	}
	
	public function getProcessVariableForActivity(core_kernel_classes_Resource $activity){
		
		//create code from the label
		$label = $activity->getLabel();
		$code = 'activity';
		// var_dump($activity, $activity->uriResource, stripos($activity->uriResource,".rdf#"));
		if(stripos($activity->uriResource,".rdf#")>0){
			$code .= '_'.substr($activity->uriResource, stripos($activity->uriResource,".rdf#")+5);
		}else{
			throw new Exception('from format of resource uri');
		}
		
		//check if the code (i.e. the variable) does not exist yet:
		$activityProcessVar = $this->getProcessVariable($code);
		if(is_null($activityProcessVar)){
			$activityProcessVar = $this->createProcessVariable('isFinished: '.$label, $code);
		}
		
		return $activityProcessVar;
	}
	
	
} /* end of class wfEngine_models_classes_ProcessAuthoringService */

?>