<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine\models\classes\class.ProcessAuthoringService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.05.2011, 16:22:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/**
 * include wfEngine_models_classes_ProcessCloner
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('wfEngine/models/classes/class.ProcessCloner.php');

/* user defined includes */
// section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D52-includes begin

require_once('wfEngine/plugins/CapiXML/models/class.ConditionalTokenizer.php');
require_once('wfEngine/plugins/CapiImport/models/class.DescriptorFactory.php');

// section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D52-includes end

/* user defined constants */
// section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D52-constants begin
// section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D52-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessAuthoringService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessAuthoringService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D56 begin
		
		parent::__construct();
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D56 end
    }

    /**
     * Short description of method analyseExpression
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string expressionInput
     * @param  boolean isCondition
     * @return DomDocument
     */
    public function analyseExpression($expressionInput, $isCondition = false)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D58 begin
		//place the following bloc in a helper
		if (!empty($expressionInput))
			$question = $expressionInput;
		else
			$question = "";
		
		//question test:
		//$question = "IF    (11+B_Q01a*3)>=2 AND (B_Q01c=2 OR B_Q01c=7)    	THEN ^variable := 2*(B_Q01a+7)-^variable";
		
		//analyse the expressionInput string and convert to an XML document:
		if (get_magic_quotes_gpc()) $question = stripslashes($question);// Magic quotes are deprecated
		//TODO: check if the variables exists and are associated to the process definition 
		
		$returnValue = null;
		if (!empty($question)){ // something to parse
			// str_replace taken from the MsReader class
			$question = str_replace("�", "'", $question); // utf8...
			$question = str_replace("�", "'", $question); // utf8...
			$question = str_replace("�", "\"", $question);
			$question = str_replace("�", "\"", $question);
			if($isCondition){
				$question = "if ".$question;
			}	
			try{
				$analyser = new Analyser();
				$tokens = $analyser->analyse($question);

				// $xml = htmlspecialchars($tokens->getXmlString(true));
				// $xml = $tokens->getXmlString(true);
				
				$returnValue = $tokens->getXml();
				
			}catch(Exception $e){
				throw new Exception("CapiXML error: {$e->getMessage()}");
			}
		}
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D58 end

        return $returnValue;
    }

    /**
     * Short description of method createActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource process
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createActivity( core_kernel_classes_Resource $process, $label = '')
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D62 begin
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
			
			//by default we add the back and forward controls to the activity 		
			$activity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_CONTROLS), array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD));
			
			$returnValue = $activity;
		}else{
			throw new Exception("the activity cannot be created for the process {$process->uriResource}");
		}
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D62 end

        return $returnValue;
    }

    /**
     * Short description of method createActivityFromConnector
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  string newActivityLabel
     * @return core_kernel_classes_Resource
     */
    public function createActivityFromConnector( core_kernel_classes_Resource $connector, $newActivityLabel)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D69 begin
		
		//get the process associate to the connector to create a new instance of activity
		$relatedActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
		$processClass =  new core_kernel_classes_Class(CLASS_PROCESS);
		$processes = $processClass->searchInstances(array(PROPERTY_PROCESS_ACTIVITIES => $relatedActivity->uriResource), array('like' => false, 'recursive' => 0));
		if(!empty($processes)){
			$returnValue = $this->createActivity(array_shift($processes), $newActivityLabel);
		}else{
			throw new Exception("no related process instance found to create an activity");
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D69 end

        return $returnValue;
    }

    /**
     * Short description of method createCondition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  DomDocument xmlDom
     * @return core_kernel_classes_Resource
     */
    public function createCondition( DomDocument $xmlDom)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D70 begin
		
		//create the expression instance:
		foreach ($xmlDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "condition"){
					
					$conditionDescriptor = DescriptorFactory::getConditionDescriptor($childOfChildNode);
					$returnValue = $conditionDescriptor->import();//(3*(^var +  1) = 2 or ^var > 7) AND ^RRR
					break 2;//once is enough...
				
				}
			}
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D70 end

        return $returnValue;
    }

    /**
     * Short description of method createConnector
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createConnector( core_kernel_classes_Resource $activity, $label = '')
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D73 begin
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		
		$connectorLabel = "";
		if(empty($label)){
			$connectorLabel = $activity->getLabel()."_c";//warning: could exist duplicate for children of a split connector
		}else{
			$connectorLabel = $label;
		}
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$returnValue = $connectorClass->createInstance($connectorLabel, "created by ProcessAuthoringService.Class");
		
		if(!empty($returnValue)){
			//associate the connector to the activity
			$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES), $activity->uriResource);
			
			//set the activity reference of the connector:
			$activityRefProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
			if($activityService->isActivity($activity)){
				$returnValue->setPropertyValue($activityRefProp, $activity->uriResource);
			}elseif($connectorService->isConnector($activity)){
				$returnValue->setPropertyValue($activityRefProp, $activity->getUniquePropertyValue($activityRefProp)->uriResource);
			}else{
				throw new Exception("invalid resource type for the activity parameter: {$activity->uriResource}");
			}
		}else{
			throw new Exception("the connector cannot be created for the activity {$activity->uriResource}");
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D73 end

        return $returnValue;
    }

    /**
     * Short description of method createFormalParameter
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string name
     * @param  string type
     * @param  string defaultValue
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createFormalParameter($name, $type, $defaultValue, $label = '')
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D80 begin
		$defaultValueProp = null;
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
		$returnValue = $classFormalParam->createInstance($label, 'created by process authoring service');
		$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME), $name);
		$returnValue->setPropertyValue($defaultValueProp, $defaultValue);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D80 end

        return $returnValue;
    }

    /**
     * Short description of method createInteractiveService
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function createInteractiveService( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D91 begin
		$number = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES))->count();
		$number += 1;
		
		//an interactive service of an activity is a call of service:
		$callOfServiceClass = new core_kernel_classes_Class(CLASS_CALLOFSERVICES);
		
		//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT
		$returnValue = $callOfServiceClass->createInstance($activity->getLabel()."_service_".$number, "created by ProcessAuthoringService.Class");
		
		if(!empty($returnValue)){
			//associate the new instance to the activity instance
			$activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $returnValue->uriResource);
			
			//set default position and size value:
			$returnValue->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH), 100);
			$returnValue->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT), 100);
			$returnValue->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP), 0);
			$returnValue->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT), 0);
		}else{
			throw new Exception("the interactive service cannot be created for the activity {$activity->uriResource}");
		}
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D91 end

        return $returnValue;
    }

    /**
     * Short description of method createJoinActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connectorInstance
     * @param  Resource followingActivity
     * @param  string newActivityLabel
     * @param  Resource previousActivity
     * @return core_kernel_classes_Resource
     */
    public function createJoinActivity( core_kernel_classes_Resource $connectorInstance,  core_kernel_classes_Resource $followingActivity = null, $newActivityLabel = '',  core_kernel_classes_Resource $previousActivity = null)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D9E begin
		
		$this->setConnectorType($connectorInstance, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_JOIN));
		
		$propNextActivity = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		
		if(is_null($followingActivity)){
			//TODO: create an activity if null:
			$followingActivity = $this->createActivityFromConnector($connectorInstance, $newActivityLabel);
		}else{
			//find if a join connector already leads to the following activity:
			$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
			$connectors = $connectorClass->searchInstances(array(
				PROPERTY_CONNECTORS_NEXTACTIVITIES => $followingActivity->uriResource,
				PROPERTY_CONNECTORS_TYPE =>INSTANCE_TYPEOFCONNECTORS_JOIN
				), array('like' => false, 'recursive' => 0));
		
			$found = false;
			foreach($connectors as $connector){
						
				if(!is_null($previousActivity)){
					
					//important: check that the connector found is NOT the same as the current one:
					if($connectorInstance->uriResource != $connector->uriResource){
						//delete old connector, 
						$this->deleteConnector($connectorInstance);
						//and associate the activity to that one the existing one via a set property value to the "previous activities" property
						$connectorInstance = $connector;
						$found = true;
						
						break;//one join connector allowed for a next activity
					}else{
						//nothing to do, since the connector is already 
						//it would be the case when one re-save the join connector with the same followinf activity
						return 'same activity';
					}
					
				}else{
					throw new Exception('no previous activity found to be connected to the next activity');
				}
						
			}
			if($found){
			
			}else{
			
			}
			
		}
		
		if($followingActivity instanceof core_kernel_classes_Resource){
			$connectorInstance->editPropertyValues($propNextActivity, $followingActivity->uriResource);
			$connectorInstance->setLabel(__("merge to ").$followingActivity->getLabel());
			
			//check multiplivity here
			// $connectorInstance = $connector;
			//calculate the number of time the same triple must be set (according to the multiplicity of the related parallel connector):
			$multiplicity = 1;//default multiplicity, if no multiple parallel activity 
			$processFlow = new wfEngine_models_classes_ProcessFlow();
			$parallelConnector = null;
			$parallelConnector = $processFlow->findParallelFromActivityBackward($previousActivity);
			
			if(!is_null($parallelConnector)){
				$firstActivityOfTheBranch = array_pop($processFlow->getCheckedActivities());
				//count the number of time theprevious activity must be set as the previous activity of the join connector
				$multiplicity = 0; //restart counting: we are sure that at least one of such activity will be found
				$nextActivityCollection = $parallelConnector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
				foreach($nextActivityCollection->getIterator() as $nextActivity){
					if($nextActivity->uriResource == $firstActivityOfTheBranch->uriResource){
						$multiplicity++;
					}
				}
			}
			
			if($multiplicity){
				//delete old connector, and associate the activity to that one:
				// $this->deleteConnector($connectorInstance);
				$propConnectorsPreviousActivities = new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES);
				$connectorInstance->removePropertyValues($propConnectorsPreviousActivities, array('pattern' => $previousActivity->uriResource));
				
				for($i=0;$i<$multiplicity;$i++){
					$connectorInstance->setPropertyValue($propConnectorsPreviousActivities, $previousActivity->uriResource);
				}
			}else{
				throw new Exception('unexpected null multiplicity');
			}
			
			$returnValue = $followingActivity;
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004D9E end

        return $returnValue;
    }

    /**
     * Short description of method createProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string label
     * @param  string code
     * @return core_kernel_classes_Resource
     */
    public function createProcessVariable($label = '', $code = '')
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DAC begin
		
		if(!empty($code) && $this->getProcessVariable($code)){
			throw new Exception("A process variable with the code '{$code}' already exists");
		}
		
		$classCode = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		if(empty($label)){
			$label = "Process variable";
            if(!empty($code)){
				$label .= " ".$code;
			}
		}
		$returnValue = $this->createInstance($classCode, $label);
		
		if(!empty($code)){
			$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE), $code);
		}
		
		//set the new instance of process variable as a property of the class process instance:
		$ok = $returnValue->setType(new core_kernel_classes_Class(RDF_PROPERTY));
		if($ok){
			$newTokenProperty = new core_kernel_classes_Property($returnValue->uriResource);
			$newTokenProperty->setDomain(new core_kernel_classes_Class(CLASS_TOKEN));
			$newTokenProperty->setRange(new core_kernel_classes_Class(RDFS_LITERAL));//literal only!
			$newTokenProperty->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MULTIPLE), GENERIS_TRUE);
		}else{
			throw new Exception("the newly created process variable {$label} ({$returnValue->uriResource}) cannot be set as a property of the class Token");
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DAC end

        return $returnValue;
    }

    /**
     * Short description of method createTransitionRule
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  string question
     * @return core_kernel_classes_Resource
     */
    public function createTransitionRule( core_kernel_classes_Resource $connector, $question = '')
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DB0 begin
		
		//associate the newly create expression with the transition rule of the connector
		$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));

		if(empty($transitionRule) || $transitionRule == null){
			//create an instance of transition rule:

			$transitionRuleClass = new core_kernel_classes_Class(CLASS_TRANSITIONRULES);
			$transitionRule = $transitionRuleClass->createInstance('TransitionRule : '  . $question);
			//Associate the newly created transition rule to the connector:
			$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $transitionRule->uriResource);
		}
		
		if(!empty($question)){
			$condition = $this->createCondition( $this->analyseExpression($question, true) );
			if($condition instanceof core_kernel_classes_Resource){
				//delete old condition:
				$oldCondition = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
				if(!is_null($oldCondition)){
					$this->deleteCondition($oldCondition);
				}
				$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_RULE_IF), $condition->uriResource);
			}
		}
		
		$returnValue = $transitionRule;
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DB0 end

        return $returnValue;
    }

    /**
     * Short description of method createSequenceActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  Resource followingActivity
     * @param  string newActivityLabel
     * @return core_kernel_classes_Resource
     */
    public function createSequenceActivity( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $followingActivity = null, $newActivityLabel = '')
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DB6 begin
		$this->setConnectorType($connector, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
		
		if(is_null($followingActivity)){
			$followingActivity = $this->createActivityFromConnector($connector, $newActivityLabel);
		}
		if($followingActivity instanceof core_kernel_classes_Resource){
			//associate it to the property value of the connector
			$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), $followingActivity->uriResource);
			//obvisouly, set the following actiivty as not initial (if it happened to be so):
			$followingActivity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
			$returnValue = $followingActivity;
		}
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DB6 end

        return $returnValue;
    }

    /**
     * Short description of method createSplitActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  string connectionType
     * @param  Resource followingActivity
     * @param  string newActivityLabel
     * @param  boolean followingActivityisConnector
     * @return core_kernel_classes_Resource
     */
    public function createSplitActivity( core_kernel_classes_Resource $connector, $connectionType,  core_kernel_classes_Resource $followingActivity = null, $newActivityLabel = '', $followingActivityisConnector = false)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DC3 begin
		
		$this->setConnectorType($connector, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_CONDITIONAL));
		
		//remove property PROPERTY_CONNECTORS_NEXTACTIVITIES values on connector before:
		if(is_null($followingActivity)){
			
			if($followingActivityisConnector){
				//create a new connector:
				$followingActivity = $this->createConnector($connector);
			}else{
				//get the process associate to the connector to create a new instance of activity
				$relatedActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
				$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
				$processes = $processClass->searchInstances(array(PROPERTY_PROCESS_ACTIVITIES => $relatedActivity->uriResource), array('like'=>false, 'recursive' => 0));
				if(!empty($processes)){
					$followingActivity = $this->createActivity(array_shift($processes), $newActivityLabel);
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
				$transitionRule = $this->createTransitionRule($connector);
				if(is_null($transitionRule)){
					throw new Exception("the transition rule of the connector split cannot be created");
				}
			}
			if(strtolower($connectionType) == 'then'){
				$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), $followingActivity->uriResource);
			}elseif(strtolower($connectionType) == 'else'){
				$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), $followingActivity->uriResource);
			}else{
				throw new Exception("wrong connection type then/else");
			}
			
			$returnValue = $followingActivity;
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DC3 end

        return $returnValue;
    }

    /**
     * Short description of method deleteActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public function deleteActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DDC begin
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$connectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_ACTIVITYREFERENCE => $activity->uriResource), array('like' => false, 'recursive' => 0));
		foreach($connectors as $connector){
			$this->deleteConnector($connector);
		}
		
		//deleting resource "acitivty" with its references should be enough normally to remove all references... to be tested
				
		//delete call of service!!
		$interactiveServices = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
		foreach($interactiveServices->getIterator() as $service){
			$this->deleteCallOfService($service);
		}
		
		//delete activity itself:
		$returnValue = $this->deleteInstance($activity);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DDC end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteActualParameters
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource callOfService
     * @return boolean
     */
    public function deleteActualParameters( core_kernel_classes_Resource $callOfService)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DE0 begin
		
		//remove the property values in the call of service instance
		$callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN));
		$callOfService->removePropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT));
		
		//get all actual param of the current call of service
		$actualParamCollection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
		$actualParamCollection = $actualParamCollection->union($callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT)));
		if($actualParamCollection->count()<=0){
			return true;//no need to delete anything
		}
		
		//delete all of them:
		foreach($actualParamCollection->getIterator() as $actualParam){
		
			if($actualParam instanceof core_kernel_classes_Resource){
				$returnValue = $actualParam->delete(true);
				if(!$returnValue) {
					break;
				}
			}
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DE0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteCallOfService
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource service
     * @return boolean
     */
    public function deleteCallOfService( core_kernel_classes_Resource $service)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DF5 begin
		
		//delete related actual param as well:
			
		$propActualParamIn = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);
		$propActualParamOut = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT);
		
		foreach($service->getPropertyValuesCollection($propActualParamIn)->getIterator() as $actualParam){
			$actualParam->delete(true);
		}
		foreach($service->getPropertyValuesCollection($propActualParamOut)->getIterator() as $actualParam){
			$actualParam->delete(true);
		}
		
		$returnValue = $service->delete(true);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DF5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteCondition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource rule
     * @return boolean
     */
    public function deleteCondition( core_kernel_classes_Resource $rule)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DF9 begin
		
		//get the rule type:
		if(!is_null($rule)){
			//if it is a transition rule: get the uri of the related properties: THEN and ELSE:
			//delete the expression of the conditio and its related terms
			$expression = $rule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			if(!is_null($expression) && ($expression instanceof core_kernel_classes_Resource) ){
				$this->deleteExpression($expression);
			}
			
			//delete reference: should be done on a upper level, at this function call
			
			$returnValue = true;
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DF9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteConnector
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @return boolean
     */
    public function deleteConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DFC begin
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		
		if(!$connectorService->isConnector($connector)){
			// throw new Exception("the resource in the parameter is not a connector: {$connector->getLabel()} ({$connector->uriResource})");
			return $returnValue;
		}
		
		//get the type of connector:
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
		if(!is_null($connectorType) && $connectorType instanceof core_kernel_classes_Resource){
			if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_CONDITIONAL){
				//delete the related rule:
				$relatedRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
				if(!is_null($relatedRule)){
					$this->deleteRule($relatedRule);
				}
			}
		}
				
		//manage the connection to the following activities
		$activityRef = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
		$nextActivityCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
		foreach($nextActivityCollection->getIterator() as $nextActivity){
			if($connectorService->isConnector($nextActivity)){
				$nextActivityRef = $nextActivity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
				if($nextActivityRef == $activityRef){
					$this->deleteConnector($nextActivity);//delete following connectors only if they have the same activity reference
				}
			}
		}
		
		//delete connector itself:
		$returnValue = $this->deleteInstance($connector);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DFC end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteConnectorNextActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  string connectionType
     * @return mixed
     */
    public function deleteConnectorNextActivity( core_kernel_classes_Resource $connector, $connectionType = 'next')
    {
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DFF begin
		
		// $authorizedProperties = array(
			// PROPERTY_CONNECTORS_NEXTACTIVITIES,
			// PROPERTY_TRANSITIONRULES_THEN,
			// PROPERTY_TRANSITIONRULES_ELSE
		// );
		$nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		
		switch($connectionType){
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
				if($connectorService->isConnector($nextActivity)){
					$nextActivityRef = $nextActivity->getUniquePropertyValue($activityRefProp)->uriResource;
					if($nextActivityRef == $activityRef){
						$this->deleteConnector($nextActivity);//delete following connectors only if they have the same activity reference
					}
				}
			}
			$connector->removePropertyValues($nextActivitiesProp);
		}elseif(($property->uriResource == PROPERTY_TRANSITIONRULES_THEN)||($property->uriResource == PROPERTY_TRANSITIONRULES_ELSE)){
			//it is a split connector: get the transition rule, if exists
			$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
			if(!is_null($transitionRule)){
				$nextActivity = $transitionRule->getOnePropertyValue($property);
				if(!is_null($nextActivity)){
					if($connectorService->isConnector($nextActivity)){
						$nextActivityRef = $nextActivity->getUniquePropertyValue($activityRefProp)->uriResource;
						if($nextActivityRef == $activityRef){
							$this->deleteConnector($nextActivity);//delete following connectors only if they have the same activity reference
						}
					}
					$connector->removePropertyValues($nextActivitiesProp, array('pattern' => $nextActivity->uriResource));
					$transitionRule->removePropertyValues($property, array('pattern' => $nextActivity->uriResource));
				}
			}
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004DFF end
    }

    /**
     * Short description of method deleteExpression
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource expression
     * @return boolean
     */
    public function deleteExpression( core_kernel_classes_Resource $expression)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E0D begin
		
		//delete related expressions
		$firstExpressionCollection = $expression->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_FIRST_EXPRESSION));
		$secondExpressionCollection = $expression->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SECOND_EXPRESSION));
		$expressionCollection = $firstExpressionCollection->union($secondExpressionCollection);
		foreach($expressionCollection->getIterator() as $exp){
			$this->deleteExpression($exp);
		}
		
		$terminalExpression = $expression->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION));
		if(!empty($terminalExpression) && $terminalExpression instanceof core_kernel_classes_Resource){
			$this->deleteTerm($terminalExpression);
		}
		
		//delete the expression itself:
		$returnValue = $expression->delete(true);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E0D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteInstance
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource instance
     * @return boolean
     */
    public function deleteInstance( core_kernel_classes_Resource $instance)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E13 begin
		if(!is_null($instance)){
			$returnValue = $instance->delete(true);//delete references!
		}
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E13 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteOperation
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource operation
     * @return boolean
     */
    public function deleteOperation( core_kernel_classes_Resource $operation)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E16 begin
		$firstOperand = $operation->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP));
		if(!is_null($firstOperand) && ($firstOperand instanceof core_kernel_classes_Resource)){
			$this->deleteTerm($firstOperand);
		}
		
		$secondOperand = $operation->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP));
		if(!is_null($secondOperand) && ($secondOperand instanceof core_kernel_classes_Resource)){
			$this->deleteTerm($secondOperand);
		}
		
		$returnValue = $operation->delete(true);
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E16 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteProcess
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource process
     * @return boolean
     */
    public function deleteProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E19 begin
		
		if(!is_null($process)){
			$activities = $this->getActivitiesByProcess($process);
			foreach($activities as $activity){
				if(!$this->deleteActivity($activity)){
					return $returnValue;
				}
			}
			
			$returnValue = $process->delete(true);
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E19 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteRule
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource rule
     * @return boolean
     */
    public function deleteRule( core_kernel_classes_Resource $rule)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E26 begin
		
		//get the rule type:
		if(!is_null($rule)){
			$this->deleteCondition($rule);
			
			//delete the resources
			$returnValue = $rule->delete($rule);
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E26 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteTerm
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource term
     * @return boolean
     */
    public function deleteTerm( core_kernel_classes_Resource $term)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E2B begin
		
		$termClasses = array(
			CLASS_TERM_SUJET_PREDICATE_X,
			CLASS_TERM_CONST,
			CLASS_TERM
		);
		
		//list of terms instance that must not be deleted!
		$termConstants = array(
			INSTANCE_TERM_IS_NULL
		);
		
		if(!is_null($term)){
			//determine the class:
			
			foreach($term->getType() as $class){
				if($class->uriResource == CLASS_OPERATION){
					
					$this->deleteOperation($term);//an operation is a term
					
				}elseif(in_array($class->uriResource,$termClasses)){
				
					if(!in_array($term->uriResource, $termConstants)){//delete all instances but the one that are preset
						$term->delete(true);
					}
					
				}else{
					throw new Exception("trying to delete a term with an unknown term class");
				}
			}
			$returnValue = true;
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E2B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method editCondition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource rule
     * @param  string conditionString
     * @return boolean
     */
    public function editCondition( core_kernel_classes_Resource $rule, $conditionString)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E2E begin
		
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
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E2E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getActivitiesByProcess
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource process
     * @return array
     */
    public function getActivitiesByProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = array();

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E32 begin
		
		if(is_null($process)){
			throw new Exception("the process cannot be null");
			return $returnValue;
		}
		
		
		foreach ($process->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES))->getIterator() as $activity){
			if($activity instanceof core_kernel_classes_Resource){
				$returnValue[$activity->uriResource] = $activity;
			}
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E32 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getConnectorsByActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  array option
     * @param  boolean isConnector
     * @return array
     */
    public function getConnectorsByActivity( core_kernel_classes_Resource $activity, $option = array(), $isConnector = false)
    {
        $returnValue = array();

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E35 begin
		
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
		
		$connectorsClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		
		if(in_array('prev',$option)){
			$previousConnectors = $connectorsClass->searchInstances(array(PROPERTY_CONNECTORS_NEXTACTIVITIES => $activity->uriResource), array('like' => false, 'recursive' => 0));
			foreach ($previousConnectors as $connector){
				if(!is_null($connector)){
					if($connector instanceof core_kernel_classes_Resource ){
						$returnValue['prev'][$connector->uriResource] = $connector; 
					}
				}
			}
		}
		
		if(in_array('next',$option)){
		
			$previousConnectors = $connectorsClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $activity->uriResource), array('like' => false, 'recursive' => false));
			foreach ($previousConnectors as $connector){
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
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E35 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getFormalParameter
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string name
     * @param  string defaultValue
     * @return core_kernel_classes_Resource
     */
    public function getFormalParameter($name, $defaultValue = '')
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E47 begin
		
		$classFormalParam = new core_kernel_classes_Class(CLASS_FORMALPARAMETER);
		
		
		foreach($classFormalParam->getInstances(true) as $formalParam){
			$nameResource = $formalParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME));
			$nameValue = null;
			if(!is_null($nameResource)){
			
				if($nameResource instanceof core_kernel_classes_Literal){
					$nameValue = $nameResource->literal;
				}else if($nameResource instanceof core_kernel_classes_Resource){
					$nameValue = $nameResource->uriResource;//encode??
				}
				
				if($nameValue == $name){
				
					if(empty($defaultValue)){
					
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
										$returnValue = $formalParam;
									}
								}else{
									if($defaultProcessVariable->uriResource == $defaultValue){
										$returnValue = $formalParam;
									}
								}
							}
						}
						
					}
					
				}
			}
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E47 end

        return $returnValue;
    }

    /**
     * Short description of method getInstance
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getInstance($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E53 begin
		
		if(is_null($clazz) || !$this->isAuthorizedClass($clazz)){
			return $returnValue;
		}
		$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E53 end

        return $returnValue;
    }

    /**
     * Short description of method getProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string code
     * @param  boolean forceCreation
     * @return core_kernel_classes_Resource
     */
    public function getProcessVariable($code, $forceCreation = false)
    {
        $returnValue = null;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E63 begin
		$processVariableClass =  new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		$variables = $processVariableClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => 0));
		if(!empty($variables)){
			$returnValue = array_shift($variables);
		}else if($forceCreation){
			$returnValue = $this->createProcessVariable($code, $code);
			if(is_null($returnValue)){
				throw new Exception("the process variable ({$code}) cannot be created.");
			}
		}
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E63 end

        return $returnValue;
    }

    /**
     * Short description of method getServicesByActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return array
     */
    public function getServicesByActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = array();

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E6A begin
		
		$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
		foreach($services->getIterator() as $service){
			if($service instanceof core_kernel_classes_Resource){
				$returnValue[$service->uriResource] = $service;
			}
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E6A end

        return (array) $returnValue;
    }

    /**
     * Short description of method isAuthorizedClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isAuthorizedClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E6D begin
	
        //all classes are authorized
	$returnValue = true;
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E6D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setActivityHidden
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  boolean hidden
     * @return boolean
     */
    public function setActivityHidden( core_kernel_classes_Resource $activity, $hidden)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E70 begin
		
		$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
		$generisBoolean = GENERIS_FALSE;
		if($hidden){
			$generisBoolean = GENERIS_TRUE;
		}
		$returnValue = $activity->editPropertyValues($propHidden, $generisBoolean);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E70 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setActualParameter
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource callOfService
     * @param  Resource formalParam
     * @param  string value
     * @param  string parameterInOrOut
     * @param  string actualParameterType
     * @return boolean
     */
    public function setActualParameter( core_kernel_classes_Resource $callOfService,  core_kernel_classes_Resource $formalParam, $value, $parameterInOrOut = PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, $actualParameterType = PROPERTY_ACTUALPARAMETER_CONSTANTVALUE)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E79 begin
		
		//must exist:
		if($formalParam->hasType(new core_kernel_classes_Class(CLASS_FORMALPARAMETER))){
		
			$actualParameterClass = new core_kernel_classes_Class(CLASS_ACTUALPARAMETER);
	
			//create new resource for the property value of the current call of service PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN or PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT
			$newActualParameter = $actualParameterClass->createInstance($formalParam->getLabel(), "actual parameter created by Process Authoring Service");
			$newActualParameter->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER), $formalParam->uriResource);
			$newActualParameter->setPropertyValue(new core_kernel_classes_Property($actualParameterType), $value);
			
			$returnValue = $callOfService->setPropertyValue(new core_kernel_classes_Property($parameterInOrOut), $newActualParameter->uriResource);
	
		}else{
			throw new Exception('the formal parameter does not exist');
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E79 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setCallOfServiceDefinition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource callOfService
     * @param  Resource serviceDefinition
     * @return boolean
     */
    public function setCallOfServiceDefinition( core_kernel_classes_Resource $callOfService,  core_kernel_classes_Resource $serviceDefinition)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E89 begin
		$returnValue = $callOfService->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->uriResource);
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E89 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setConnectorType
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  Resource typeOfConnector
     * @return boolean
     */
    public function setConnectorType( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $typeOfConnector)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E8D begin
		//TODO: check range of type of connectors:
		$returnValue = $connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), $typeOfConnector->uriResource);
	
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E8D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setFirstActivity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource process
     * @param  Resource activity
     * @return boolean
     */
    public function setFirstActivity( core_kernel_classes_Resource $process,  core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E9D begin
		
		$activities = $this->getActivitiesByProcess($process);
		$propActivityInitial = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
		foreach($activities as $activityTemp){
			$activityTemp->editPropertyValues($propActivityInitial, GENERIS_FALSE);
		}
		
		$returnValue = $activity->editPropertyValues($propActivityInitial, GENERIS_TRUE);
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004E9D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setParallelActivities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connectorInstance
     * @param  array newActivitiesArray
     * @return boolean
     */
    public function setParallelActivities( core_kernel_classes_Resource $connectorInstance, $newActivitiesArray = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004EA1 begin
		
		$this->setConnectorType($connectorInstance, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL));
		
		$propNextActivities = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		$processFlow = new wfEngine_models_classes_ProcessFlow();
		
		//calculate the number of parallel activities, for each activity definition 
		$nextActivitiesCollection = $connectorInstance->getPropertyValuesCollection($propNextActivities);
		$oldActivitiesArray = array();
		foreach ($nextActivitiesCollection->getIterator() as $activityResource){
			if(!isset($oldActivitiesArray[$activityResource->uriResource])){
				$oldActivitiesArray[$activityResource->uriResource] = 1;
			}else{
				$oldActivitiesArray[$activityResource->uriResource] += 1;
			}
		}
		
		$connectorInstance->removePropertyValues($propNextActivities);
			
		//check if the number has changed in the new posted data, otherwise, need to update the related join connector:
		$propConnectorsPreviousActivities = new core_kernel_classes_Resource(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES);
		foreach($oldActivitiesArray as $activityUri=>$count){
			
			//need for update:
			$updateRequired = true;
			
			if(isset($newActivitiesArray[$activityUri])){
				if($newActivitiesArray[$activityUri] == $count){
					//$ok, no need to update
					$updateRequired = false;
				}
			}
			
			if($updateRequired){
				$processFlow->resetCheckedResources();
				$joinConnector = null;
				
				$joinConnector = $processFlow->findJoinFromActivityForward(new core_kernel_classes_Resource($activityUri));
				if(!is_null($joinConnector)){
					$joinConnector->removePropertyValues($propConnectorsPreviousActivities, array('pattern' => $activityUri));
					for($i=0; $i<$newActivitiesArray[$activityUri]; $i++){
						$joinConnector->setPropertyValue($propConnectorsPreviousActivities, $activityUri);
					}
				
				}
			}
		}
		
		//finally, set the next activities values of the parallel connector:
		foreach($newActivitiesArray as $activityUri=>$count){
			//set property value as much as required
			for($i=0;$i<$count;$i++){
				$returnValue = $connectorInstance->setPropertyValue($propNextActivities, $activityUri);
			}
		}
		
        // section 10-13-1-39-2ae24d29:12d124aa1a7:-8000:0000000000004EA1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method createProcess
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Resource
     */
    public function createProcess($label = '', $comment = '')
    {
        $returnValue = null;

        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BD3 begin
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		if(empty($comment)) $comment = 'create by the process authoring service on '.date(DATE_ISO8601);
		if(empty($label)){
			$label = $this->createUniqueLabel($processDefinitionClass);
		}
		$returnValue = $processDefinitionClass->createInstance($label, 'created for the unit test of process execution');
			
        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BD3 end

        return $returnValue;
    }

    /**
     * Short description of method createServiceDefinition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string label
     * @param  string serviceUrl
     * @param  array inputParameters
     * @return core_kernel_classes_Resource
     */
    public function createServiceDefinition($label = '', $serviceUrl = '', $inputParameters = array())
    {
        $returnValue = null;

        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BE0 begin
		if(!empty($serviceUrl)){
			$supportServiceClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
			if(empty($label)){
				$label = $this->createUniqueLabel($supportServiceClass);
			}
			$returnValue = $supportServiceClass->createInstance($label, 'service definition created for the unit test of process execution');
			$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL), $serviceUrl);//add management of wsdl service
			
			$propFormalParam = new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN);
			$classFormalParam = new core_kernel_classes_Class(CLASS_FORMALPARAMETER);
			$classProcessVariables = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
			
			foreach($inputParameters as $paramName => $value){
			
				$formalParam = null;
				$formalParam = $this->getFormalParameter($paramName, $value);
				
				if(is_null($formalParam)){
				
					//create one:
					$defaultValue = '';
					$defaultValueType = 'constant';
					
					if(is_string($value)){
						$value = trim($value);
						if(!empty($value)){
							if(substr($value, 0, 1) == '^'){
								//is a process var, so get related process var resource:
								$code = substr($value, 1);
								$processVar = $this->getProcessVariable($code);
								if(is_null($processVar)){
									$processVar = $this->createProcessVariable($code, $code);
								}
								if(!is_null($processVar)){
									$defaultValue = $processVar->uriResource;
									$defaultValueType = 'processvariable';
								}else{
									throw new Exception('cannot create process variable with the code '.$code);
								}
							}else{
								//it is a constant
								$defaultValue = $value;
							}
						}
					}else if($value instanceof core_kernel_classes_Resource){
						//check if it is a process variable:
						if($value->hasType($classProcessVariables)){
							$defaultValue = $value->uriResource;
							$defaultValueType = 'processvariable';
						}
					}
					
					$formalParam = $this->createFormalParameter($paramName, $defaultValueType, $defaultValue, $paramName);
				}
				
				
				if(!is_null($formalParam)) $returnValue->setPropertyValue($propFormalParam, $formalParam->uriResource);
			}
		}
        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BE0 end

        return $returnValue;
    }

    /**
     * Find and destroy the service with the given serviceUrl
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string serviceUrl
     * @return boolean
     */
    public function deleteServiceDefinition($serviceUrl)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BF9 begin
		$urlProperties = array(PROPERTY_SUPPORTSERVICES_URL);//could add the wsdl url here when wsdl service implemented
		
		foreach($urlProperties as $urlProperty){
			$serviceDefinitionsClass =  new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
			$serviceDefinitions = $serviceDefinitionsClass->searchInstances(array($urlProperty => $serviceUrl), array('like' => false, 'recursive' => 1000));
			foreach($serviceDefinitions as $service){
				$returnValue = $service->delete(true);
			}
		}
		
        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BF9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string code
     * @return boolean
     */
    public function deleteProcessVariable($code)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BFD begin
		if(!is_null($code)){
		
			$processVariableToDelete = null;
			$processVariableToDelete = $this->getProcessVariable($code);
			if(!is_null($processVariableToDelete)){
				$returnValue = $processVariableToDelete->delete(true);
			}
			
		}
        // section 10-13-1-39--6cc6036b:12e4807fb4f:-8000:0000000000002BFD end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessAuthoringService */

?>