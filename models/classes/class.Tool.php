<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.Tool.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 04.09.2008, 17:24:37
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include Activity
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Activity.php');

/**
 * include ActivityExecution
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ActivityExecution.php');

/**
 * include WfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.WfResource.php');

/* user defined includes */
// section 10-13-1--31--23da6e5c:11a2ac14500:-8000:000000000000099B-includes begin
// section 10-13-1--31--23da6e5c:11a2ac14500:-8000:000000000000099B-includes end

/* user defined constants */
// section 10-13-1--31--23da6e5c:11a2ac14500:-8000:000000000000099B-constants begin
// section 10-13-1--31--23da6e5c:11a2ac14500:-8000:000000000000099B-constants end

/**
 * Short description of class Tool
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Tool
    extends WfResource
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute url
     *
     * @access public
     * @var string
     */
    public $url = '';

    /**
     * Short description of attribute input
     *
     * @access public
     * @var array
     */
    public $input = array();

    /**
     * Short description of attribute output
     *
     * @access public
     * @var array
     */
    public $output = array();

    /**
     * Short description of attribute activityExecution
     *
     * @access public
     * @var ActivityExecution
     */
    public $activityExecution = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @param ActivityExecution
     * @return void
     */
    public function __construct($uri,  ActivityExecution $activityExecution = null)
    {
        // section 10-13-1--31--23da6e5c:11a2ac14500:-8000:00000000000009B3 begin
		parent::__construct($uri);

		$this->activityexecution = $activityExecution;

		// Get service definitions
		
		$serviceDefinitionCallOfServiceProp = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION);
		$serviceDefinition = $this->resource->getUniquePropertyValue($serviceDefinitionCallOfServiceProp);


		// Get service url for call
		$serviceDefinitionUrlProp = new core_kernel_classes_Property(PROPERTY_SERVICEDEFINITIONS_URL);
		$serviceDefinitionUrl 	= 
		
		getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
																		array($serviceDefinition[0]),
																		array(PROPERTY_SERVICEDEFINITIONS_URL),
																		array(""));																		
		$interactiveToolUrl		= $serviceDefinitionUrl[0]."";

		// We get the input parameters for the tool.
		$inParameters = getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
												  array($uri),
												  array(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN),
												  array(''));
					
		$this->input 	= array();
		$this->output	= array();

		foreach ($inParameters as $inParameter)
		{
			// We retrieve param labels -> their names.
			$inParameterlabelcomment = getlabelcomment(WfEngine::singleton()->sessionGeneris,
													   $inParameter,
													   array(''));
			
			// Actual parameters can be filled in by ONE process variable OR a quality metric OR a constant value.
			// So we consider a XOR between processVariable, constantValue and qualityMetric.
			$inParametersProcessVariables 	= getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
																	    array($inParameter),
																	    array(PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE),
																	    array(''));
																	  
			$inParametersQualityMetrics		= getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
																		array($inParameter),
																		array(PROPERTY_ACTUALPARAMETER_QUALITYMETRIC),
																		array(''));
																		
			$inParametersConstantValues		= getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
																		array($inParameter),
																		array(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE),
																		array(''));

			// We also need the label of the formal parameters for the call URL.
			$formalParameters				= getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
																		array($inParameter),
																		array(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER),
																		array(''));
																		
			if (!(is_null($this->activityexecution)))
			{
				// We find the parameter's formal name.
				$formalParameterName 					= getlabelcomment(WfEngine::singleton()->sessionGeneris,
																		  $formalParameters[0],
																		  array(''));
			
				// We fill the parameter values list. We get it from generis and put them in an array.
				if (count($inParametersProcessVariables))
				{
					// Process variable case.
					$inParametersProcessVariablesValues = getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,
																					array($this->activityexecution->processExecution->uri),
																					array($inParametersProcessVariables[0]),
																					array(''));
					$paramType 	= 'processvar'; 																
					$paramValue = $inParametersProcessVariables[0];
				}
				else if (count($inParametersQualityMetrics))
				{
					// Quality Metric case.
					$paramType 	= 'metric';
					$paramvalue = $inParametersQualityMetrics[0];	
				}
				else
				{
					// Constant case.
					$paramType = 'constant';
					$paramValue = $inParametersConstantValues[0];
				}
				error_reporting("^E_NOTICE");
				// We fill the input variable table.
				$this->input[common_Utils::fullTrim($formalParameterName['label'])] = array('type' => $paramType, 'value' => $paramValue);
			}
			else
			{	error_reporting("^E_NOTICE");
				// We only provide the name with no param value
				$this->input[common_Utils::fullTrim($formalParameterName['label'])] = array('type' => $paramType, 'value' => null);
			}
		}
		
//		$outParameters = getInstancePropertyValues(WfEngine::singleton()->sessionGeneris,array($uri),array(PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT),array(""));
//		$outParametersDescription="[output&nbsp;parameters]";
//		foreach ($outParameters as $outParameter)
//		{
//			$outParameterlabelcomment=getlabelcomment(WfEngine::singleton()->sessionGeneris,$outParameter,array(""));
//			
//			$outParametersDescription.="&".$outParameterlabelcomment["label"]."=";
//		}
		$this->url		= $interactiveToolUrl;
		
        // section 10-13-1--31--23da6e5c:11a2ac14500:-8000:00000000000009B3 end
    }

    /**
     * Short description of method getCallUrl
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */

	public function getCallUrl($variables=array())
    {
        $returnValue = (string) '';

        // section 10-13-1-85-453ada87:11c2dedd780:-8000:0000000000000A1C begin
        
		$activeLiteral = new core_kernel_classes_ActiveLiteral($this->url);
		$activeUrl = "".$activeLiteral->getDisplayedCode($variables)."";
		
		$returnValue = $activeUrl;
        
        foreach ($this->input as $name => $value)
        {
        	$returnValue .= '&' . urlencode(trim($name)) . '=' . urlencode(trim($value['value']));
        }
        // section 10-13-1-85-453ada87:11c2dedd780:-8000:0000000000000A1C end

        return (string) $returnValue;
    }

} /* end of class Tool */

?>