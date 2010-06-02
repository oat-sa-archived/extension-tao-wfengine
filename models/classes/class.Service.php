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
	
	
	public $styleWidth = 1024;
	public $styleHeight = 800;
	public $styleTop = 0;
	public $styleLeft = 0;
	
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
		$this->activityExecution = $activityExecution;
		

	
		// Get service definitions
		$serviceDefinition = $this->resource->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));

		// Get service url for call
		$serviceUrl = '';
		$serviceDefinitionUrl = $serviceDefinition->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_SERVICEDEFINITIONS_URL));
		if($serviceDefinitionUrl instanceof core_kernel_classes_Literal){
			$serviceUrl = $serviceDefinitionUrl->literal;
		}else if($serviceDefinitionUrl instanceof core_kernel_classes_Resource){
			$serviceUrl = $serviceDefinitionUrl->uriResource;
		}
		$urlPart = explode('?',$serviceUrl);
		$this->url = $urlPart[0];
		
		$inParameterCollection = $this->resource->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN));
        
		$this->input 	= array();
		$this->output	= array();
		// var_dump($this->resource, $inParameterCollection);
		foreach ($inParameterCollection->getIterator() as $inParameter)
		{
			$inParameterProcessVariable = $inParameter->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE));//a resource
			$inParameterConstant = $inParameter->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE));
			
			//quality metric no longer used:
			// $inParametersQualityMetricProp = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_QUALITYMETRIC);
			// $inParametersQualityMetric = $inParameter->getOnePropertyValue($inParametersQualityMetricProp);
			
			$formalParameter = $inParameter->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER));
				
			if (!(is_null($this->activityExecution))){
				
				$formalParameterName = $formalParameter->getUniquePropertyValue( new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME));
				
				// var_dump($inParameter, $formalParameter, $inParameterProcessVariable, $inParameterConstant);
				
				if(!is_null($inParameterProcessVariable)){
					
					if(!($inParameterProcessVariable instanceof core_kernel_classes_Resource)){
						throw new Exception("the process variable set as the value of the parameter 'in' is not a resource");
					}
					
					
					$paramType 	= 'processvar'; 
					$paramValue = '';
					
					
					$prop = new core_kernel_classes_Property($inParameterProcessVariable->uriResource);
					$paramValueResourceArray = $this->activityExecution->processExecution->resource->getPropertyValues($prop);
					
					// var_dump($inParameterProcessVariable,$paramValueResource);
					
					// if($paramValueResource instanceof core_kernel_classes_Literal){
						// $paramValue = $paramValueResource->literal;
					// }else if($paramValueResource instanceof core_kernel_classes_Resource){
						// $paramValue = $paramValueResource->uriResource;//encode??
					// }
					
					// $count = $paramValueResourceCollection->count();
					// if($count>0){
						// if($count>1){
							
						// }else{
							// $paramValue = (string) $paramValueResourceCollection->get(0);
						// }
					
					// }
					
					
					$paramValue = '';
					if(sizeof($paramValueResourceArray)){					
						if(count($paramValueResourceArray)>1){
							$paramValue = serialize($paramValueResourceArray);
						}else{
							if (trim(strip_tags($paramValueResourceArray[0])) != "")
							{
								$paramValue = trim($paramValueResourceArray[0]);
								
							}
						}
					}
					
			
					$this->input[common_Utils::fullTrim($formalParameterName)] = array(
						'type' => $paramType, 
						'value' => $paramValue, 
						'uri' => $inParameterProcessVariable->uriResource
						);
				}else if(!is_null($inParameterConstant)){
					
					$paramType 	= 'constant';
					$paramValue = '';
					
					if($inParameterConstant instanceof core_kernel_classes_Literal){
						$paramValue = $inParameterConstant->literal;
					}else if($inParameterConstant instanceof core_kernel_classes_Resource){
						$paramValue = $inParameterConstant->uriResource;//encode??
					}
					
					// var_dump($inParameterConstant, $paramValue);
					
					$this->input[common_Utils::fullTrim($formalParameterName)] = array(
						'type' => $paramType,
						'value' => $paramValue
						);
				}else{
				
				}
				
				
			}else{
				$this->input[common_Utils::fullTrim($formalParameterName)] = array('type' => null, 'value' => null);
			}
			
		}
		
		//get the style information (size and position)
		$width = $this->resource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH));
		if($width != null && $width instanceof core_kernel_classes_Literal){
			if(intval($width)){
				//do not allow width="0"
				$this->styleWidth = intval($width->literal).'%';
			}
		}//in the future, allow percentage
		
		$height = $this->resource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT));
		if($height != null && $height instanceof core_kernel_classes_Literal){
			if(intval($height->literal)){
				//do not allow height="0"
				$this->styleHeight = intval($height->literal).'%';
			}
		}
		
		$top = $this->resource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP));
		if($top != null && $top instanceof core_kernel_classes_Literal){
			$this->styleTop = (0+intval($top->literal)).'%';//used to be +30px
		}
		
		$left = $this->resource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT));
		if($left != null && $left instanceof core_kernel_classes_Literal){
			$this->styleLeft = intval($left->literal).'%';
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
		$returnValue = $this->url;
		
		$returnValue .= '?';
        // var_dump($this->input);
		foreach ($this->input as $name => $value){
		
			$actualValue = $value['value'];
			
			if($value['type']=='processVar'){
				//check if the same is passed in param:
				if(array_key_exists($value['uri'], $variables)){
					$actualValue = $variables[ $value['uri'] ];//set the actual value as the one given in parameter
				}
			}
			
        	$returnValue .= urlencode(trim($name)) . '=' . urlencode(trim($actualValue)) . '&';
        
		}

        // section 10-13-1-85-453ada87:11c2dedd780:-8000:0000000000000A1C end
		
        return (string) $returnValue;
    }
	
	public function getStyle(){
	
		$style = "position:absolute;";
		
		if(!empty($this->styleLeft)) $style .= "left:{$this->styleLeft};";
		if(!empty($this->styleTop)) $style .= "top:{$this->styleTop};";
		if(!empty($this->styleWidth)) $style .= "width:{$this->styleWidth};";
		if(!empty($this->styleHeight))	$style .= "height:{$this->styleHeight};";
		
		return $style;
		
	}

} /* end of class Service */

?>