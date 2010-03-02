<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.Service.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatically generated on 03.02.2010, 16:01:04 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
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
 * Short description of class Service
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Service
    extends WfResource
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd : 

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
     * @param  string uri
     * @param  ActivityExecution activityExecution
     * @return void
     */
    public function __construct($uri,  ActivityExecution $activityExecution = null)
    {
        // section 10-13-1--31--23da6e5c:11a2ac14500:-8000:00000000000009B3 begin
        parent::__construct($uri);
		$this->activityexecution = $activityExecution;
				
		// Get service definitions
		$serviceDefProp = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION);
		$serviceDefinition = $this->resource->getOnePropertyValue($serviceDefProp);

		// Get service url for call
		$serviceDefinitionUrlProp = new core_kernel_classes_Property(PROPERTY_SERVICEDEFINITIONS_URL);
		$serviceDefinitionUrl = $serviceDefinition->getPropertyValues($serviceDefinitionUrlProp);

		$this->url = $serviceDefinitionUrl[0]."";
		
		$inParametersPorp = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);
		$inParameters = $this->resource->getOnePropertyValue($inParametersPorp);
        
		$this->input 	= array();
		$this->output	= array();
		
		foreach ($inParameters as $inParameter)
		{
			$inParametersProcessVariableProp = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);
			$inParametersProcessVariable = $inParameter->getOnePropertyValue($inParametersProcessVariableProp);
			
			$inParametersQualityMetricProp = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_QUALITYMETRIC);
			$inParametersQualityMetric = $inParameter->getOnePropertyValue($inParametersQualityMetricProp);
			
			$inParametersConstantProp = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE);
			$inParametersConstant = $inParameter->getOnePropertyValue($inParametersConstantProp);
			
			$formalParametersProp = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER);
			$formalParameters = $inParameter->getOnePropertyValue($formalParametersProp);
			
			if (!(is_null($this->activityexecution))){
				$formalParameterName = $formalParameters->getLabel();
				
				if($inParametersProcessVariable != null) {
					$prop = new core_kernel_classes_Property($inParametersProcessVariable);
					$paramValue = $this->activityExecution->resource->getOnePropertyValue($prop);
					$paramType 	= 'processvar'; 
				}
				else if ($inParametersQualityMetrics != null) {
					$paramType 	= 'metric';
					$paramvalue = $inParametersQualityMetric;
				}
				else {
					$paramType 	= 'metric';
					$paramvalue = $inParametersQualityMetric;
				}
				$this->input[common_Utils::fullTrim($formalParameterName)] = array('type' => $paramType, 'value' => $paramValue);
				
			}
			else{
				$this->input[common_Utils::fullTrim($formalParameterName)] = array('type' => null, 'value' => null);
			}
		}
										

														
		// section 10-13-1--31--23da6e5c:11a2ac14500:-8000:00000000000009B3 end
    }

    /**
     * Short description of method getCallUrl
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variables
     * @return string
     */
    public function getCallUrl($variables = array())
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

} /* end of class Service */

?>