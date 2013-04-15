<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.InteractiveServiceService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 30.09.2011, 17:11:39 with ArgoUML PHP module 
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

/* user defined includes */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-includes begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-includes end

/* user defined constants */
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-constants begin
// section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E95-constants end

/**
 * Short description of class wfEngine_models_classes_InteractiveServiceService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_InteractiveServiceService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


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
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E97 begin
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E97 end
    }

    /**
     * Short description of method getCallUrl
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource interactiveService
     * @param  Resource activityExecution
     * @param  array variables
     * @return string
     */
    public function getCallUrl( core_kernel_classes_Resource $interactiveService,  core_kernel_classes_Resource $activityExecution = null, $variables = array())
    {
        $returnValue = (string) '';

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E99 begin
		$serviceDefinition = $interactiveService->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
		
		$serviceUrl = '';
		$serviceDefinitionUrl = $serviceDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));
		if($serviceDefinitionUrl instanceof core_kernel_classes_Literal){
			$serviceUrl = $serviceDefinitionUrl->literal;
		}else if($serviceDefinitionUrl instanceof core_kernel_classes_Resource){
			$serviceUrl = $serviceDefinitionUrl->uriResource;
		}
		// Remove the parameters because they are only for show, and they are actualy encoded in the variables
		$urlPart = explode('?',$serviceUrl);
		$returnValue = $urlPart[0];
		$returnValue .= '?';
		if(preg_match('/^\//i', $returnValue)){
			//create absolute url (prevent issue when TAO installed on a subfolder
			$returnValue = ROOT_URL.ltrim($returnValue, '/');
		}
		
		$input 	= $this->getInputValues($interactiveService, $activityExecution);
		$output	= array();//for later use
		
		foreach ($input as $name => $value){
		
			$actualValue = $value['value'];
			
			if($value['type'] == 'processVar'){
				//check if the same is passed in args to overwrite the current value:
				if(array_key_exists($value['uri'], $variables)){
					$actualValue = $variables[ $value['uri'] ];//set the actual value as the one given in parameter
				}
			}
			
        	$returnValue .= urlencode(trim($name)) . '=' . urlencode(trim($actualValue)) . '&';
        
		}
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E99 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getStyle
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource interactiveService
     * @return string
     */
    public function getStyle( core_kernel_classes_Resource $interactiveService)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002E9D begin
		
		$styleData = array();
		
		//get the style information (size and position)
		$width = $interactiveService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_WIDTH));
		if($width != null && $width instanceof core_kernel_classes_Literal){
			if(intval($width->literal)){
				//do not allow width="0"
				$styleData['width'] = intval($width->literal).'%';
			}
		}//in the future, allow percentage
		
		$height = $interactiveService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_HEIGHT));
		if($height != null && $height instanceof core_kernel_classes_Literal){
			if(intval($height->literal)){
				//do not allow height="0"
				$styleData['height'] = intval($height->literal).'%';
			}
		}
		
		$top = $interactiveService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_TOP));
		if($top != null && $top instanceof core_kernel_classes_Literal){
			$styleData['top'] = (0+intval($top->literal)).'%';//used to be +30px
		}
		
		$left = $interactiveService->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_LEFT));
		if($left != null && $left instanceof core_kernel_classes_Literal){
			$styleData['left'] = intval($left->literal).'%';
		}
		
		$returnValue = "position:absolute;";
		if(isset($styleData['left'])) $returnValue .= "left:".$styleData['left'].";";
		if(isset($styleData['top'])) $returnValue .= "top:".$styleData['top'].";";
		if(isset($styleData['width'])) $returnValue .= "width:".$styleData['width'].";";
		if(isset($styleData['height'])) $returnValue .= "height:".$styleData['height'].";";
		
        // section 127-0-1-1-4ecae359:132158f9a4c:-8000:0000000000002E9D end

        return (string) $returnValue;
    }

    /**
     * Short description of method isInteractiveService
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource interactiveService
     * @return boolean
     */
    public function isInteractiveService( core_kernel_classes_Resource $interactiveService)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EC1 begin
        if(!is_null($interactiveService)){
			$returnValue = $interactiveService->hasType( new core_kernel_classes_Class(CLASS_CALLOFSERVICES));
		}
        // section 127-0-1-1-52a9110:13219ee179c:-8000:0000000000002EC1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getInputValues
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource interactiveService
     * @param  Resource activityExecution
     * @return array
     */
    public function getInputValues( core_kernel_classes_Resource $interactiveService,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = array();

        // section 127-0-1-1-511681e7:13291a3c527:-8000:000000000000303E begin
		
		$inParameterCollection = $interactiveService->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN));
		
		$propActualParamProcessVariable = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);
		$propActualParamConstantValue = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE);
		$propActualParamFormalParam = new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER);
		$propFormalParamName = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME);
		
		foreach ($inParameterCollection->getIterator() as $inParameter){
			
			$inParameterProcessVariable = $inParameter->getOnePropertyValue($propActualParamProcessVariable);//a resource
			$inParameterConstant = $inParameter->getOnePropertyValue($propActualParamConstantValue);
			
			$formalParameter = $inParameter->getUniquePropertyValue($propActualParamFormalParam);
			$formalParameterName = $formalParameter->getUniquePropertyValue($propFormalParamName);
			
			//the current activity execution contains the current context of execution:
			if (!is_null($activityExecution)){
				
				if(!is_null($inParameterProcessVariable)){
					
					if(!($inParameterProcessVariable instanceof core_kernel_classes_Resource)){
						throw new Exception("the process variable set as the value of the parameter 'in' is not a resource");
					}
					
					$paramType 	= 'processvar'; 
					$paramValue = '';
					
					//use the current and unique token to get the process variable value:
					$paramValueResourceArray = $activityExecution->getPropertyValues(new core_kernel_classes_Property($inParameterProcessVariable->uriResource));
					
					$paramValue = '';
					if(sizeof($paramValueResourceArray)){
						if(count($paramValueResourceArray)>1){
							//allowing multiple values to process variable in service input:
							$paramValue = serialize($paramValueResourceArray);
						}else{
							if (trim(strip_tags($paramValueResourceArray[0])) != ""){
								$paramValue = trim($paramValueResourceArray[0]);
							}
						}
					}
			
					$returnValue[common_Utils::fullTrim($formalParameterName)] = array(
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
					
					$returnValue[common_Utils::fullTrim($formalParameterName)] = array(
						'type' => $paramType,
						'value' => $paramValue
						);
				}else{
				
				}
				
				
			}else{
				$returnValue[common_Utils::fullTrim($formalParameterName)] = array('type' => null, 'value' => null);
			}
			
		}
		
        // section 127-0-1-1-511681e7:13291a3c527:-8000:000000000000303E end

        return (array) $returnValue;
    }

    /**
     * Short description of method getOutputValues
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource interactiveService
     * @param  Resource activityExecution
     * @return array
     */
    public function getOutputValues( core_kernel_classes_Resource $interactiveService,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = array();

        // section 127-0-1-1-511681e7:13291a3c527:-8000:0000000000003042 begin
        // section 127-0-1-1-511681e7:13291a3c527:-8000:0000000000003042 end

        return (array) $returnValue;
    }

    /**
     * Short description of method deleteInteractiveService
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource interactiveService
     * @return boolean
     */
    public function deleteInteractiveService( core_kernel_classes_Resource $interactiveService)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003086 begin
		
		$propActualParamIn = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);
		$propActualParamOut = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT);
		
		foreach($interactiveService->getPropertyValuesCollection($propActualParamIn)->getIterator() as $actualParam){
			$actualParam->delete();
			//no need to remove reference here, since the resource that uses them is going to be deleted by the end of the method
		}
		foreach($interactiveService->getPropertyValuesCollection($propActualParamOut)->getIterator() as $actualParam){
			$actualParam->delete();
		}
		
		$returnValue = $interactiveService->delete(true);
		
        // section 127-0-1-1-8ae8e2e:132ba7fdd5a:-8000:0000000000003086 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_InteractiveServiceService */

?>