<?php

error_reporting(E_ALL);

/**
 * The GenerisFormFactory enables you to create Forms using rdf data and the
 * api to provide it. You can give any node of your ontology and the factory
 * create the appriopriate form. The Generis ontology (with the Widget Property)
 * required to use it.
 * Now only the xhtml rendering mode is implemented
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @see core_kernel_classes_* packages
 * @subpackage helpers_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The GenerisFormFactory enables you to create Forms using rdf data and the
 * api to provide it. You can give any node of your ontology and the factory
 * create the appriopriate form. The Generis ontology (with the Widget Property)
 * required to use it.
 * Now only the xhtml rendering mode is implemented
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @see core_kernel_classes_* packages
 * @subpackage helpers_form
 */
class wfEngine_helpers_ProcessFormFactory extends tao_helpers_form_GenerisFormFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the default top level (to stop the recursivity look up) class commonly used
     *
     * @access public
     * @var string
     */
    const DEFAULT_TOP_LEVEL_CLASS = 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource';
	
    /**
     * @var array
     */
    protected static $forms = array();
    
	/**
     * Create a form from a class of your ontology, the form data comes from the
     * The default rendering is in xhtml
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  Resource instance
     * @param  string name
     * @param  array options
     * @return tao_helpers_form_Form
     */
    public static function instanceEditor( core_kernel_classes_Class $clazz,  core_kernel_classes_Resource $instance = null, $name = '', $options = array(), $excludedProp = array(), $displayCode=false)
    {
        $returnValue = null;

		if(!is_null($clazz)){
			
			if(empty($name)){
				$name = 'form_'.(count(self::$forms)+1);
			}
			
			$myForm = tao_helpers_form_FormFactory::getForm($name, $options);
			
			$defaultProperties 	= self::getDefaultProperties();
			
			$classProperties = self::getClassProperties($clazz, new core_kernel_classes_Class(self::DEFAULT_TOP_LEVEL_CLASS));
					
			foreach(array_merge($defaultProperties, $classProperties) as $property){
				
				if(!empty($excludedProp) && in_array($property->uriResource, $excludedProp)){
					continue;
				}
				
				$property->feed();
				
				//map properties widgets to form elments 
				$element = self::elementMap($property, $displayCode);
				
				if(!is_null($element)){
			
					//take instance values to populate the form
					if(!is_null($instance)){
						$values = $instance->getPropertyValuesCollection($property);
						foreach($values->getIterator() as $value){
							if(!is_null($value)){
								if($value instanceof core_kernel_classes_Resource){
									$element->setValue($value->uriResource);
								}
								if($value instanceof core_kernel_classes_Literal){
									$element->setValue((string)$value);
								}
							}
						}
					}
					$myForm->addElement($element);
				}
			}
			
			//add an hidden elt for the class uri
			$classUriElt = tao_helpers_form_FormFactory::getElement('classUri', 'Hidden');
			$classUriElt->setValue(tao_helpers_Uri::encode($clazz->uriResource));
			$myForm->addElement($classUriElt);
			
			if(!is_null($instance)){
				//add an hidden elt for the instance Uri
				$instanceUriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
				$instanceUriElt->setValue(tao_helpers_Uri::encode($instance->uriResource));
				$myForm->addElement($instanceUriElt);
			}
			
			//form data evaluation
			$myForm->evaluate();
				
			self::$forms[$name] = $myForm;
			$returnValue = self::$forms[$name];
		}
		
        return $returnValue;
    }
	
	public static function elementMap( core_kernel_classes_Property $property, $displayCode=false){
	
        $returnValue = null;
		
		//create the element from the right widget
		$widgetResource = $property->getWidget();
		if(is_null($widgetResource)){
			return null;
		}
		$widget = ucfirst(strtolower(substr($widgetResource->uriResource, strrpos($widgetResource->uriResource, '#') + 1 )));
		$element = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode($property->uriResource), $widget);
		if(!is_null($element)){
			if($element->getWidget() != $widgetResource->uriResource){
				return null;
			}
	
			//use the property label as element description
			(strlen(trim($property->getLabel())) > 0) ? $propDesc = tao_helpers_Display::textCleaner($property->getLabel(), ' ') : $propDesc = 'field '.(count($myForm->getElements())+1);	
			$element->setDescription($propDesc);
			
			//multi elements use the property range as options
			if(method_exists($element, 'setOptions')){
				$range = $property->getRange();
				if($range != null){
					$options = array();
					foreach($range->getInstances(true) as $rangeInstance){
						$value = $rangeInstance->getLabel();
						if($displayCode){
							//get the code of the process variable:
							$code = $rangeInstance->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CODE));
							if(!empty($code) && $code instanceof core_kernel_classes_Literal){
								$value .= " (code:{$code->literal})";
							}
						}
						$options[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $value;
					}
					
					//set the default value to an empty space
					if(method_exists($element, 'setEmptyOption')){
						$element->setEmptyOption(' ');
					}
					
					//complete the options listing
					$element->setOptions($options);
				}
			}
			$returnValue = $element;
		}

        return $returnValue;
    }
	
	//$callOfService already created beforehand in the model
	public static function callOfServiceEditor(core_kernel_classes_Resource $callOfService, core_kernel_classes_Resource $serviceDefinition = null, $formName=''){
		
		if(empty($formName)){
			$formName = 'callOfServiceEditor';
		}
		$myForm = null;
		$myForm = tao_helpers_form_FormFactory::getForm($formName, array());
		$myForm->setActions(array(), 'bottom');//delete the default 'save' and 'revert' buttons
		
		//add a hidden input to post the uri of the call of service that is being edited
		$classUriElt = tao_helpers_form_FormFactory::getElement('callOfServiceUri', 'Hidden');
		$classUriElt->setValue(tao_helpers_Uri::encode($callOfService->uriResource));
		$myForm->addElement($classUriElt);
		
		//add label input:
		$elementLabel = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		$elementLabel->setDescription(__('Label'));
		$elementLabel->setValue($callOfService->getLabel());
		$myForm->addElement($elementLabel);
		
		//add a drop down select input to allow selecting ServiceDefinition
		$elementServiceDefinition = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), 'Combobox');
		$elementServiceDefinition->setDescription(__('Service Definition'));
		$range = new core_kernel_classes_Class(CLASS_SERVICESDEFINITION);
		if($range != null){
			$options = array();
			foreach($range->getInstances(true) as $rangeInstance){
				$options[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
			}
			$elementServiceDefinition->setOptions($options);
		}
		// $myForm->addElement($elementServiceDefinition);
		
		//check if the property value serviceDefiniiton PROPERTY_CALLOFSERVICES_SERVICEDEFINITION of the current callOfService exists
		if(empty($serviceDefinition)){
			
			//get list of available service definition
			$collection = null;
			$collection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
			if($collection->count()<=0){
				//if the serviceDefinition is not set yet, simply return a dropdown menu of available servicedefinition
				$myForm->addElement($elementServiceDefinition);
				return $myForm;
			}
			else{
				foreach ($collection->getIterator() as $value){
					if($value instanceof core_kernel_classes_Resource){//a service definition has been found!
						$serviceDefinition = $value;
						$elementServiceDefinition->setValue($serviceDefinition->uriResource);//no need to use tao_helpers_Uri::encode here: seems like that it would be done sw else
						$myForm->addElement($elementServiceDefinition);
						break;//stop at the first occurence, which should be the unique one
					}
				}
			}
		}
		
		//if the service definition is still not set here,there is a problem
		if(empty($serviceDefinition)){
			throw new Exception("an empty value of service definition has been found for the call of service that is being edited");
			return $myForm;
		}
		//useless because already in the select field
		/*else{
			//add a hidden input element to allow easier form value evaluation after submit
			$serviceDefinitionUriElt = tao_helpers_form_FormFactory::getElement('serviceDefinitionUri', 'Hidden');
			$serviceDefinitionUriElt->setValue(tao_helpers_Uri::encode($serviceDefinition->uriResource));
			// $classUriElt->setLevel($level);
			$myForm->addElement($serviceDefinitionUriElt);
		}*/
		
		//build position elements: top, left, width, height,
		$styleProperties = array(
			PROPERTY_CALLOFSERVICES_TOP,
			PROPERTY_CALLOFSERVICES_LEFT,
			PROPERTY_CALLOFSERVICES_WIDTH,
			PROPERTY_CALLOFSERVICES_HEIGHT
		);
		$styleDescElement = tao_helpers_form_FormFactory::getElement('positionning', 'Free');
		$styleDescElement->setValue('<b>'.__('Positionning').': </b>');
		
		$styleElements = array();
		$styleElements[] = $styleDescElement;
		foreach($styleProperties as $propUri){
			$prop = new core_kernel_classes_Property($propUri);
			$styleElements[] = self::getCallOfServiceStyleElement($callOfService, $prop);//eventually, use the option hidden = true
		}
		
		//continue building the form associated to the selected service:
		//get list of parameters from the service definition PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT and IN
		//create a form element and fill the content with the default value
		$elementInputs = array_merge(
			self::getCallOfServiceParameterElements($serviceDefinition, $callOfService, "formalParameterIn"),
			self::getCallOfServiceParameterElements($serviceDefinition, $callOfService, "formalParameterOut"),
			$styleElements
		);
				
		// $elementInputs = self::getCallOfServiceParameterElements($serviceDefinition, $callOfService, "formalParameterin");
		foreach($elementInputs as $elementInput){
			$myForm->addElement($elementInput);
		}
		
        return $myForm;
	}
	
	protected static function getCallOfServiceStyleElement(core_kernel_classes_Resource $callOfService, core_kernel_classes_Property $prop, $hidden=false){
		
		$element = null;
		
		$authorizedProperties = array(
			PROPERTY_CALLOFSERVICES_TOP,
			PROPERTY_CALLOFSERVICES_LEFT,
			PROPERTY_CALLOFSERVICES_WIDTH,
			PROPERTY_CALLOFSERVICES_HEIGHT
		);
		
		if(!in_array($prop->uriResource, $authorizedProperties)){
			throw new Exception("wrong type of property for the call of service position");
		}
		
		if($hidden){
			$widget = 'Hidden';
		}else{
			$widget = 'Textbox';
		}
		
		$element = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode($prop->uriResource), $widget);
		$element->setDescription($prop->getLabel());
		$value = $callOfService->getOnePropertyValue($prop);
		if($value != null && $value instanceof core_kernel_classes_Literal){
			$element->setValue($value->literal);
		}
		return $element;
		
	}
	
	//return an array of elments
	protected static function getCallOfServiceParameterElements(core_kernel_classes_Resource $serviceDefinition, core_kernel_classes_Resource $callOfService, $paramType){
	
		$returnValue = array();//array();
		if(empty($paramType) || empty($serviceDefinition)){
			return $returnValue;
		}
		
		$formalParameterType = '';
		$actualParameterInOutType = '';
		$formalParameterName = '';
		$formalParameterSuffix = '';
		if(strtolower($paramType) == "formalparameterin"){
		
			$formalParameterType = PROPERTY_SERVICESDEFINITION_FORMALPARAMIN;
			$actualParameterInOutType = PROPERTY_CALLOFSERVICES_ACTUALPARAMIN;
			$formalParameterName = __('Formal Parameter IN'); 
			$formalParameterSuffix = '_IN';
			
		}elseif(strtolower($paramType) == "formalparameterout"){
		
			$formalParameterType = PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT;
			$actualParameterInOutType = PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT;
			$formalParameterName = __('Formal Parameter OUT');
			$formalParameterSuffix = '_OUT';
			
		}else{
			throw new Exception("unsupported formalParameter type : $paramType");
		}
		
		//get the other parameter input elements
		$collection = null;
		$collection = $serviceDefinition->getPropertyValuesCollection(new core_kernel_classes_Property($formalParameterType));
		if($collection->count()>0){
			//start creating the BLOC of form element
			$descriptionElement = tao_helpers_form_FormFactory::getElement($paramType, 'Free');
			$descriptionElement->setValue("<b>{$formalParameterName} :</b>");
			$returnValue[$paramType]=$descriptionElement;
		}
		
		foreach ($collection->getIterator() as $formalParam){
			if($formalParam instanceof core_kernel_classes_Resource){
			
				//create a form element:
				$inputName = $formalParam->getLabel();//which will be equal to $actualParam->getLabel();
				$inputUri = $formalParam->uriResource;
				// $inputUri = "";
				$inputValue = "";
				
				//get current value:
				//find actual param first!
				$actualParamValue='';
				$actualParamFromFormalParam = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_ACTUALPARAM_FORMALPARAM, $formalParam->uriResource);
				$actualParamFromCallOfServices = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property($actualParameterInOutType)); 
				
				//make an intersect with $collection = $callOfService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMOUT));
				$actualParamCollection = $actualParamFromFormalParam->intersect($actualParamFromCallOfServices);
				
				if(!$actualParamCollection->isEmpty()){
					foreach($actualParamCollection->getIterator() as $actualParam){
						if($actualParam instanceof core_kernel_classes_Resource){
							//the actual param associated to the formal parameter of THE call of services has been found!
						
							//check the type of actual parameter:
							$inParameterProcessVariable = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_PROCESSVARIABLE));//a resource
							$inParameterConstant = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_CONSTANTVALUE));
							// var_dump($actualParam, $inParameterProcessVariable, $inParameterConstant);
							
							if(!is_null($inParameterProcessVariable)){
								//the type is a processvariable so must be a resource:
								if(!($inParameterProcessVariable instanceof core_kernel_classes_Resource)){
									throw new Exception("the process variable set as the value of the actual parameter is not a resource");
								}
								
								$paramType = 'processvariable';
								$inputValue = $inParameterProcessVariable->uriResource;
								
							}elseif(!is_null($inParameterConstant)){
								//the type is a constant:
								$paramType = 'constant';
								if($inParameterConstant instanceof core_kernel_classes_Literal){
									$inputValue = $inParameterConstant->literal;
								}else if($inParameterConstant instanceof core_kernel_classes_Resource){
									$inputValue = $inParameterConstant->uriResource;//encode??
								}
								
							}else{
								//the type is not specified yet:
								
							}
			
							// $actualParameterType = PROPERTY_ACTUALPARAM_CONSTANTVALUE; //PROPERTY_ACTUALPARAM_CONSTANTVALUE;//PROPERTY_ACTUALPARAM_PROCESSVARIABLE //PROPERTY_ACTUALPARAM_QUALITYMETRIC
							
							// $actualParamValueCollection = $actualParam->getPropertyValuesCollection(new core_kernel_classes_Property($actualParameterType));
							// if(!$actualParamValueCollection->isEmpty()){
								// if($actualParamValueCollection->get(0) instanceof core_kernel_classes_Resource){
									// $actualParamValue = $actualParamValueCollection->get(0)->uriResource;
								// }elseif($actualParamValueCollection->get(0) instanceof core_kernel_classes_Literal){
									// $actualParamValue = $actualParamValueCollection->get(0)->literal;
								// }
								// $inputValue = $actualParamValue;
							// }
						
							break; //stop as one iteration: there normally should be only one actual parameter set for a given formal parameter 
						}
					}
				}
					
				
				/*
				if(empty($inputUri)){//place ce bloc dans la creation de call of service: cad retrouver systematiquement l'actual parameter associ� � chaque fois, � partir du formal parameter et call of service, lors de la sauvegarde
					// if no actual parameter has been found above (since $inputUri==0) create an instance of actual parameter and associate it to the call of service:
					$property_actualParam_formalParam = new core_kernel_classes_Property(PROPERTY_ACTUALPARAM_FORMALPARAM);
					$class_actualParam = new core_kernel_classes_Class(CLASS_ACTUALPARAM);
					$newActualParameter = $class_actualParam->createInstance($formalParam->getLabel(), "created by ProcessFormFactory");
					$newActualParameter->setPropertyValue($property_actualParam_formalParam, $formalParam->uriResource);
					
					// $inputUri = $newActualParameter->uriResource;//we add an "empty" value in 
				}
				*/
				
				if(empty($inputValue)){
					//if no value set yet, try finding the default value (literal only! or url that are considered as a literal)
					
					$defaultConstantValue = $formalParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE));
					$defaultProcessVariable = $formalParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE));
					// var_dump($formalParam, $defaultConstantValue, $defaultProcessVariable);
					
					$defaultValue = '';
					if(!is_null($defaultProcessVariable)){
						if($defaultProcessVariable instanceof core_kernel_classes_Resource){
							$defaultValue = $defaultProcessVariable->uriResource;//the case a url
						}else{
							throw new Exception('the process variable must be a resource');
						}
						// echo 'skjdsk';
						// var_dump($defaultValue);
						if(!empty($defaultValue)){
							//the input value has been set as the default one:
							$paramType = 'processvariable';
							$inputValue = $defaultValue;
						}
					}elseif(!is_null($defaultConstantValue)){
						if($defaultConstantValue instanceof core_kernel_classes_Literal){
							$defaultValue = $defaultConstantValue->literal;
						}else if($defaultConstantValue instanceof core_kernel_classes_Resource){
							$defaultValue = $defaultConstantValue->uriResource;//the case a url
						}
						
						if(!empty($defaultValue)){
							//the input value has been set as the default one:
							$paramType = 'constant';
							$inputValue = $defaultValue;
						}
					}
				}
				
				$elementId = tao_helpers_Uri::encode($inputUri).$formalParameterSuffix;
				$elementChoiceId = $elementId.'_choice';
				$elementInputId = $elementId.'_constant';
				$elementVarId = $elementId.'_var';
				
				//element of type "free":
				$element = tao_helpers_form_FormFactory::getElement($elementId, 'Free');
				$element->setValue($inputName.': ');
				
				//set the choice element (radiobox: constant/processVariable:
				$elementChoice = tao_helpers_form_FormFactory::getElement($elementChoiceId, 'Radiobox');
				$elementChoice->setDescription(' ');
				$options = array(
					"constant" => __("Constant"),
					"processvariable" => __("Process Variable")
				);
				$elementChoice->setOptions($options);
				$elementChoice->setValue($paramType);
				
				//element input:
				$elementInput = tao_helpers_form_FormFactory::getElement($elementInputId, 'Textbox');
				$elementInput->setDescription(' ');
				
				//element choice of process var (range: all or selected only?):
				$elementVar = tao_helpers_form_FormFactory::getElement($elementVarId, 'ComboBox');
				$elementVar->setDescription(' ');
				
				$processVariables = array();
				$processVariables = array('none' => ' ');
				$range = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
				foreach($range->getInstances(true) as $rangeInstance){
					$processVariables[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
				}
				$elementVar->setOptions($processVariables);
				
				//set value here:
				if($paramType == 'constant'){
					$elementInput->setValue($inputValue);
				}elseif($paramType == 'processvariable'){
					$elementVar->setValue($inputValue);
				}
								
				$returnValue[$elementId] = $element;
				$returnValue[$elementChoiceId] = $elementChoice;
				$returnValue[$elementInputId] = $elementInput;
				$returnValue[$elementVarId] = $elementVar;
			}
		}
		
		return $returnValue;
	}
	
	public function formalParameterEditor(core_kernel_classes_Resource $formalParam){
		
	}
	
	public function connectorEditor(core_kernel_classes_Resource $connector, core_kernel_classes_Resource $connectorType=null, $formName='', core_kernel_classes_Resource $activity){
		if(empty($formName)){
			$formName = 'connectorForm';
		}
		$myForm = null;
		$myForm = tao_helpers_form_FormFactory::getForm($formName, array());
		$myForm->setActions(array(), 'bottom');//delete the default 'save' and 'revert' buttons
		
		//add a hidden input to post the uri of the connector that is being edited
		$elementConnectorUri = tao_helpers_form_FormFactory::getElement('connectorUri', 'Hidden');
		$elementConnectorUri->setValue(tao_helpers_Uri::encode($connector->uriResource));
		$myForm->addElement($elementConnectorUri);
		
		//add a hidden input to post the uri of the activity of the connector that is being edited
		$elementActivityUri = tao_helpers_form_FormFactory::getElement('activityUri', 'Hidden');
		$elementActivityUri->setValue(tao_helpers_Uri::encode($activity->uriResource));
		$myForm->addElement($elementActivityUri);
		
		//add label input: authorize connector label editing or not?
		$elementLabel = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		$elementLabel->setDescription(__('Label'));
		$elementLabel->setValue($connector->getLabel());
		$myForm->addElement($elementLabel);
		
		//add a drop down select input to allow selecting Type of Connector
		$elementConnectorType = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE), 'Combobox');
		$elementConnectorType->setDescription(__('Connector Type'));
		$range = new core_kernel_classes_Class(CLASS_TYPEOFCONNECTORS);
		if($range != null){
			$options = array();
			foreach($range->getInstances(true) as $rangeInstance){
				$options[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
			}
			$elementConnectorType->setOptions($options);
		}
		//TODO: check if the parent of the current connector is a connector as well: if so, only allow the split type connector, since there will be no use of a sequential one
		
		//check if the property value "type of connector" of the current connector exists
		if(empty($connectorType)){
			
			//get the type of connector of the current connector
			$collection = null;
			$collection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if($collection->isEmpty()){
				//if the type of connector is not set yet, simply return a dropdown menu of available type of connector
				$myForm->addElement($elementConnectorType);
				return $myForm;
			}
			else{
				foreach ($collection->getIterator() as $value){
					if($value instanceof core_kernel_classes_Resource){//a connector type has been found!
						$connectorType = $value;
						$elementConnectorType->setValue($connectorType->uriResource);//no need to use tao_helpers_Uri::encode here: seems like that it would be done sw else
						$myForm->addElement($elementConnectorType);
						break;//stop at the first occurence, which should be the unique one (use newly added getOnePropertyValue here instead)
					}
				}
			}
		}
		
		//if the type of connector is still not set here,there is a problem
		if(empty($connectorType)){
			throw new Exception("an empty value of service definition has been found for the call of service that is being edited");
			return $myForm;
		}
		
		//notification system
		$myForm->addElement(tao_helpers_form_FormFactory::getElement('notify_set', 'Hidden'));
		
		$notifyMsgProperty = new core_kernel_classes_Property(PROPERTY_CONNECTOR_NOTIFICATION_MESSAGE);
		$notifyMsgElt = tao_helpers_form_GenerisFormFactory::elementMap($notifyMsgProperty);
		
		$notifyMsgElt->setValue((string)$connector->getOnePropertyValue($notifyMsgProperty));
		$myForm->addElement($notifyMsgElt);
		
		$notifyProperty = new core_kernel_classes_Property(PROPERTY_CONNECTOR_NOTIFY);
		$notifyElt = tao_helpers_form_GenerisFormFactory::elementMap($notifyProperty);
		$notifyElt->addAttribute('class', 'notify-element');
		$notifyElt->setValues(tao_helpers_Uri::encodeArray($connector->getPropertyValues($notifyProperty)));
		$myForm->addElement($notifyElt);
		
		$notifyUserProperty = new core_kernel_classes_Property(PROPERTY_CONNECTOR_USER_NOTIFIED);
		$notifyUserElt = tao_helpers_form_GenerisFormFactory::elementMap($notifyUserProperty);
		$notifyUserElt->addAttribute('class', 'notify-user');
		$notifyUserElt->setValues(tao_helpers_Uri::encodeArray($connector->getPropertyValues($notifyUserProperty)));
		$myForm->addElement($notifyUserElt);
		
		$notifyGroupProperty = new core_kernel_classes_Property(PROPERTY_CONNECTOR_ROLE_NOTIFIED);
		$notifyGroupElt = tao_helpers_form_GenerisFormFactory::elementMap($notifyGroupProperty);
		$notifyGroupElt->addAttribute('class', 'notify-group');
		$notifyGroupElt->setValues(tao_helpers_Uri::encodeArray($connector->getPropertyValues($notifyGroupProperty)));
		$myForm->addElement($notifyGroupElt);
		
		
		//continue building the form according the the type of connector:
		$elementInputs=array();
		if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
			
			$elementInputs = self::nextActivityElements($connector, 'next');
			
		}else if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_SPLIT){
				
			$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
			$elementInputs[] = self::getConditionElement($transitionRule);
			
			$elementInputs = array_merge($elementInputs, self::nextActivityElements($connector, 'then'));
			$elementInputs = array_merge($elementInputs, self::nextActivityElements($connector, 'else'));
			
		}else if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_PARALLEL){
			
			$elementInputs = self::nextActivityElements($connector, 'parallel', false, false, 'Checkbox');
			
		}else if($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_JOIN){
			
			$elementInputs = self::nextActivityElements($connector, 'join', true, false, 'Combobox');
			
		}else{
			throw new Exception("the selected type of connector {$connectorType->getLabel()} is not supported");
		}
		
		foreach($elementInputs as $elementInput){
			$myForm->addElement($elementInput);
		}
		
        return $myForm;
	}
	
	public function inferenceRuleEditor(core_kernel_classes_Resource $inferenceRule, $formName='inferenceRuleForm'){
		
		$myForm = null;
		$myForm = tao_helpers_form_FormFactory::getForm($formName, array());
		$myForm->setActions(array(), 'bottom');//delete the default 'save' and 'revert' buttons
		
		//add a hidden input to post the uri of the call of service that is being edited
		$elementInferenceRuleUri = tao_helpers_form_FormFactory::getElement('inferenceRuleUri', 'Hidden');
		$elementInferenceRuleUri->setValue(tao_helpers_Uri::encode($inferenceRule->uriResource));
		$myForm->addElement($elementInferenceRuleUri);
		
		//add label input: authorize elementInferenceRule label editing or not?
		// $elementLabel = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		// $elementLabel->setDescription(__('Label'));
		// $elementLabel->setValue($callOfService->getLabel());
		// $myForm->addElement($elementLabel);
		
		//the if condition:
		$myForm->addElement(self::getConditionElement($inferenceRule));
		
		//THEN: assignment:
		//create the description element
		$elementDescription = tao_helpers_form_FormFactory::getElement('then_description', 'Free');
		$elementDescription->setValue(__("THEN").': ');
		$myForm->addElement($elementDescription);
		
		$elementThen = tao_helpers_form_FormFactory::getElement("then_assignment", 'Textarea');
		$elementThen->setDescription(__('Assignment').': ');
		$then = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_INFERENCERULES_THEN));
		if(!is_null($then)){
			if($then instanceof core_kernel_classes_Resource){
				$elementThen->setValue($then->getLabel());
			}
		}
		$myForm->addElement($elementThen);
		
		//ELSE: (optional)
		$elementDescription = tao_helpers_form_FormFactory::getElement('else_description', 'Free');
		$elementDescription->setValue(__("ELSE").': ');
		$myForm->addElement($elementDescription);
		
		//type: inference rule or assignment:
		$elementChoice = tao_helpers_form_FormFactory::getElement('else_choice', 'Radiobox');
		$elementChoice->setDescription(' ');
		$options = array(
			"assignment" => __("Assignment"),
			"inference" => __("Another Inference Rule"),
			"none" => __("No thanks")
		);
		$elementChoice->setOptions($options);
		
		$elementElse = tao_helpers_form_FormFactory::getElement("else_assignment", 'Textarea');
		$elementElse->setDescription(__('Assignment').': ');
		$else = $inferenceRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE));
		
		if(!is_null($else) && $else instanceof core_kernel_classes_Resource){
			//is it an assignment or another inferenceRule?
			$classUri = $else->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE))->uriResource;
			
			if($classUri == CLASS_ASSIGNMENT){
				$elementElse->setValue($else->getLabel());
				$elementChoice->setValue("assignment");
			}elseif($classUri == CLASS_INFERENCERULES){
				$elementChoice->setValue("inference");
			}else{
				throw new Exception("wrong type in the else of the inference rule");
			}
		}else{
			$elementChoice->setValue("none");
		}
		$myForm->addElement($elementChoice);
		$myForm->addElement($elementElse);
		
        return $myForm;
	}
	
	public function consistencyRuleEditor(core_kernel_classes_Resource $consistencyRule, $formName='consistencyRuleForm'){
		
		$myForm = null;
		$myForm = tao_helpers_form_FormFactory::getForm($formName, array());
		$myForm->setActions(array(), 'bottom');//delete the default 'save' and 'revert' buttons
		
		//add a hidden input to post the uri of the call of service that is being edited
		$elementConsistencyRuleUri = tao_helpers_form_FormFactory::getElement('consistencyRuleUri', 'Hidden');
		$elementConsistencyRuleUri->setValue(tao_helpers_Uri::encode($consistencyRule->uriResource));
		$myForm->addElement($elementConsistencyRuleUri);
		
		//add label input: authorize elementInferenceRule label editing or not?
		// $elementLabel = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
		// $elementLabel->setDescription(__('Label'));
		// $elementLabel->setValue($callOfService->getLabel());
		// $myForm->addElement($elementLabel);
		
		//the if condition:
		$myForm->addElement(self::getConditionElement($consistencyRule));
		
		//involved activity: checkbox, range:current activity
		$activities = array(); //array of resource
		//get activity:
		$activityCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_ACTIVITIES_CONSISTENCYRULE , $consistencyRule->uriResource);
		$currentActivity = null;
		if(!$activityCollection->isEmpty()){
			$currentActivity = $activityCollection->get(0);
			//get process:
			$processCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_PROCESS_ACTIVITIES , $currentActivity->uriResource);
			if(!$processCollection->isEmpty()){
				$currentProcess = $processCollection->get(0);
				
				//get all activities of the process:
				$authoringService = new wfEngine_models_classes_ProcessAuthoringService();
				$activities = $authoringService->getActivitiesByProcess($currentProcess);
			}
		}
		
		$activityOptions = array();
		foreach($activities as $rangeInstance){
			$activityOptions[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
		}
		$elementActivities = self::getChoiceElement($consistencyRule, new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_INVOLVEDACTIVITIES), $activityOptions, 'Checkbox');
		$myForm->addElement($elementActivities);
		
		//suppressable : boolean
		$booleanOptions = array();
		$booleanClass = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		foreach($booleanClass->getInstances(true) as $rangeInstance){
			$booleanOptions[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
		}
		$elementSuppressable = self::getChoiceElement($consistencyRule, new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_SUPPRESSABLE), $booleanOptions, 'Radiobox');
		$myForm->addElement($elementSuppressable);
		
		//notification: textarea
		$notificationProp = new core_kernel_classes_Property(PROPERTY_CONSISTENCYRULES_NOTIFICATION);
		$elementNotification = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_CONSISTENCYRULES_NOTIFICATION), 'Textarea');
		$elementNotification->setDescription($notificationProp->getLabel());
		$notification = $consistencyRule->getOnePropertyValue($notificationProp);
		if(!is_null($notification)){
			if($notification instanceof core_kernel_classes_Literal){
				$elementNotification->setValue($notification->literal);
			}
		}
		$myForm->addElement($elementNotification);
			
        return $myForm;
	}
	
	public static function getChoiceElement(core_kernel_classes_Resource $instance, core_kernel_classes_Property $property, $options, $widget='Radiobox'){
		
		$elementChoice = null;
		$elementChoice = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode($property->uriResource),  $widget);
		$elementChoice->setDescription($property->getLabel());
		
		//check the validity of the widget type
		if(!in_array($widget, array('Radiobox', 'Checkbox', 'Combobox'))){
			return $elementChoice;
		}
		
		//set the options:
		$elementChoice->setOptions($options);
		
		//set the value:
		$propertyValuesCollection = $instance->getPropertyValuesCollection($property);
		foreach($propertyValuesCollection->getIterator() as $propertyValue){
			if($propertyValue instanceof core_kernel_classes_Resource){
				$elementChoice->setValue($propertyValue->uriResource);
			}elseif($propertyValue instanceof core_kernel_classes_Literal){
				$elementChoice->setValue($propertyValue->literal);
			}
		}
		
		return $elementChoice;
	}
	
	//@param core_kernel_classes_Resource or null $rule
	public static function getConditionElement($rule){
	
		$elementCondition = null;
		$elementCondition = tao_helpers_form_FormFactory::getElement("if", 'Textarea');
		$elementCondition->setDescription(__('IF'));
		if(!is_null($rule) && $rule instanceof core_kernel_classes_Resource){
			$if = $rule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			
			if(!is_null($if) && $if instanceof core_kernel_classes_Resource){
				$elementCondition->setValue($if->getLabel());
			}
		}
		return $elementCondition;
		
	}
	
	
	public function nextActivityEditor(core_kernel_classes_Resource $connector, $type, $formName='nextActivityEditor'){
		if(!in_array($type, array('next', 'then', 'else'))){
			throw new Exception('unknown type of next activity');
		}
		$myForm = tao_helpers_form_FormFactory::getForm($formName, array());
		$myForm->setActions(array(), 'bottom');//delete the default 'save' and 'revert' buttons
		
		$elements = $this->nextActivityElements($connector, $type);
		foreach($elements as $element){
			$myForm->addElement($element);
		}
		
        return $myForm;
	}
	
	public function nextActivityElements(core_kernel_classes_Resource $connector, $type, $allowCreation = true, $includeConnectors = true, $optionsWidget = 'Combobox'){
		
		$returnValue = array();
		
		$authorizedOptionsWidget = array('Combobox','Checkbox');
		if(!in_array($optionsWidget, $authorizedOptionsWidget)){
			throw new Exception('Wrong type of widget');
			return $returnValue;
		}
		
		$idPrefix = '';
		$nextActivity = null;
		$propTransitionRule = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE);
		$propNextActivities = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
		//find the next activity if available
		switch(strtolower($type)){
			case 'next':
					
				$nextActivityCollection = $connector->getPropertyValuesCollection($propNextActivities);
				foreach($nextActivityCollection->getIterator() as $activity){
					if($activity instanceof core_kernel_classes_Resource){
						$nextActivity = $activity;//we take the last one...(note: there should be only one though)
					}
				}
				$idPrefix = 'next';
				break;
			case 'then':
				$transitionRuleCollection = $connector->getPropertyValuesCollection($propTransitionRule);
				foreach($transitionRuleCollection->getIterator() as $transitionRule){
					if($transitionRule instanceof core_kernel_classes_Resource){
						foreach($transitionRule->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->getIterator() as $then){
							if($then instanceof core_kernel_classes_Resource){
								$nextActivity = $then;
							}
						}
					}
				}
				$idPrefix = 'then';
				break;
			case 'else':
				$transitionRuleCollection = $connector->getPropertyValuesCollection($propTransitionRule);
				foreach($transitionRuleCollection->getIterator() as $transitionRule){
					if($transitionRule instanceof core_kernel_classes_Resource){
						foreach($transitionRule->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->getIterator() as $else){
							if($else instanceof core_kernel_classes_Resource){
								$nextActivity = $else;
							}
						};
					}
				}
				$idPrefix = 'else';
				break;
			case 'parallel':{
				$nextActivity = array();
				$nextActivityCollection = $connector->getPropertyValuesCollection($propNextActivities);
				foreach($nextActivityCollection->getIterator() as $activity){
					if($activity instanceof core_kernel_classes_Resource){
						$nextActivity[] = $activity;
					}
				}
				$idPrefix = 'parallel';
				break;
			}
			case 'join':
				// $transitionRule = $connector->getOnePropertyValue($propTransitionRule);
				// if(!is_null($transitionRule) && $transitionRule instanceof core_kernel_classes_Resource){
					// $then = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN));//note: 'else' doesn't matter
					// if($then instanceof core_kernel_classes_Resource){
						// $nextActivity = $then;
					// }
				// }
				$nextActivity = $connector->getOnePropertyValue($propNextActivities, true);
				$idPrefix = $type;
				break;
			default:
				throw new Exception("unknown type for the next activity");
		}
			
		$activityOptions = array();
		$connectorOptions = array();
		
		if($allowCreation){
			//create the activity label element (used only in case of new activity craetion)
			$elementActivityLabel = tao_helpers_form_FormFactory::getElement($idPrefix."_activityLabel", 'Textbox');
			$elementActivityLabel->setDescription(__('Label'));
		
			//add the "creating" option
			$activityOptions["newActivity"] = __("create new activity");
			$connectorOptions["newConnector"] = __("create new connector");
		}
		
		//the activity associated to the connector:
		$parallelActivityCount = array();//used only in case of a parallel connector
		$referencedActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));//mandatory property value, initiated at the connector creation
		if($referencedActivity instanceof core_kernel_classes_Resource){
			$processCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_PROCESS_ACTIVITIES, $referencedActivity->uriResource);
			if($processCollection->count()>0){
				$process = $processCollection->get(0);
				if(!empty($process)){
					//get list of activities and connectors for the current process:
					
					$processAuthoringService = new wfEngine_models_classes_ProcessAuthoringService();
					$activities = $processAuthoringService->getActivitiesByProcess($process);
					
					foreach($activities as $activityTemp){
						
						//include activities options:
						$activityOptions[ tao_helpers_Uri::encode($activityTemp->uriResource) ] = $activityTemp->getLabel();
						$parallelActivityCount[$activityTemp->uriResource] = 0;//initialize the number of each activity to 0
						
						//include connectors options:
						if($includeConnectors){
							$connectorCollection = core_kernel_impl_ApiModelOO::getSubject(PROPERTY_CONNECTORS_ACTIVITYREFERENCE, $activityTemp->uriResource);
							foreach($connectorCollection->getIterator() as $connectorTemp){
								if( $connector->uriResource!=$connectorTemp->uriResource){
									$connectorOptions[ tao_helpers_Uri::encode($connectorTemp->uriResource) ] = $connectorTemp->getLabel();
								}
							}
						}
						
					}
				}
			}
		}
		
		//create the description element
		$elementDescription = tao_helpers_form_FormFactory::getElement($idPrefix, 'Free');
		$elementDescription->setValue(strtoupper($type).' :');
		
		//create the activity select element:
		$elementActivities = tao_helpers_form_FormFactory::getElement($idPrefix."_activityUri", $optionsWidget);
		$elementActivities->setDescription(__('Activity'));
		$elementActivities->setOptions($activityOptions);
		
		$elementChoice = null;
		$elementConnectors = null;
		if($includeConnectors){
			//the default radio button to select between the 3 possibilities:
			$elementChoice = tao_helpers_form_FormFactory::getElement($idPrefix."_activityOrConnector", 'Radiobox');
			$elementChoice->setDescription(__('Activity or Connector'));
			$options = array(
				"activity" => __("Activity"),
				"connector" => __("Connector")
			);
			$elementChoice->setOptions($options);
			
			//create the connector select element:
			$elementConnectors = tao_helpers_form_FormFactory::getElement($idPrefix."_connectorUri", $optionsWidget);
			$elementConnectors->setDescription(__('Connector'));
			$elementConnectors->setOptions($connectorOptions);
		}
		
		if(!empty($nextActivity)){
		
			if(is_array($nextActivity) && $optionsWidget == 'Checkbox'){
				
				foreach($nextActivity as $activity){
					$elementActivities->setValue($activity->uriResource);//no need for tao_helpers_Uri::encode
					
					//determine the number for each activity:
					$parallelActivityCount[$activity->uriResource] += 1;
				}
				
				
				
			}elseif($nextActivity instanceof core_kernel_classes_Resource){
				if(wfEngine_models_classes_ProcessAuthoringService::isActivity($nextActivity)){
					if($includeConnectors) $elementChoice->setValue("activity");
					$elementActivities->setValue($nextActivity->uriResource);//no need for tao_helpers_Uri::encode
				}
				if(wfEngine_models_classes_ProcessAuthoringService::isConnector($nextActivity) && $includeConnectors){
					$elementChoice->setValue("connector");
					$elementConnectors->setValue($nextActivity->uriResource);
				}
			}
		}
		
		//if is parallel: TODO: clean that!!
		if(strtolower($type)=='parallel'){
			foreach($parallelActivityCount as $activityUri=>$number){
				//create customized hidden field with the number for each activity
				$encodedUri = tao_helpers_Uri::encode($activityUri);
				
				$elementHidden = null;
				$elementHidden = tao_helpers_form_FormFactory::getElement("{$encodedUri}_num_hidden", 'Hidden');
				$elementHidden->setValue($number);
				
				$returnValue[$idPrefix.'_'.$activityUri] = $elementHidden;
			}
		}
		
		//put all elements in the return value:
		$returnValue[$idPrefix.'_description'] = $elementDescription;
		if($includeConnectors) $returnValue[$idPrefix.'_choice'] = $elementChoice;
		$returnValue[$idPrefix.'_activities'] = $elementActivities;
		if($allowCreation) $returnValue[$idPrefix.'_label'] = $elementActivityLabel;
		if($includeConnectors) $returnValue[$idPrefix.'_connectors'] = $elementConnectors;
		
		return $returnValue;
	}
    
} /* end of class wfEngine_helpers_ProcessFormFactory */

?>