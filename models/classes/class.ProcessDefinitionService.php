<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.ProcessDefinitionService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.09.2011, 13:44:11 with ArgoUML PHP module 
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
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-includes begin
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-includes end

/* user defined constants */
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-constants begin
// section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEA-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessDefinitionService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessDefinitionService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processActivitiesProp
     *
     * @access public
     * @var Property
     */
    public $processActivitiesProp = null;

    /**
     * Short description of attribute activitiesIsInitialProp
     *
     * @access public
     * @var Property
     */
    public $activitiesIsInitialProp = null;

    /**
     * Short description of attribute processVariablesProp
     *
     * @access public
     * @var Property
     */
    public $processVariablesProp = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getRootActivities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getRootActivities( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEB begin
		
		$activities = $processDefinition->getPropertyValuesCollection($this->processActivitiesProp);
		
		// Unfortunately at this time we have no other possibility than iterate on the
		// activities because the association between PROCESSES (1) and ACTIVITIES (2) is directed
		// from (1) to (2).
		//solution:
		//1 - could be replaced by a Class::searchInstance() by adding the asociation (2) to (1)
		//2 - add a property to class PROCESSES: initialActivities (would then need to update all the related methods)
		
		foreach ($activities->getIterator() as $activity)
		{
			$isInitialCollection = $activity->getOnePropertyValue($this->activitiesIsInitialProp);
		
			if ($isInitialCollection!= null && $isInitialCollection->uriResource == GENERIS_TRUE)
			{
				//new: return array of Resource insteand of Activity
				$returnValue[] = $activity;
			}
		}
		
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EEB end

        return (array) $returnValue;
    }

    /**
     * Short description of method getAllActivities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getAllActivities( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF0 begin
		foreach ($processDefinition->getPropertyValuesCollection($this->processActivitiesProp)->getIterator() as $activity){
			if($activity instanceof core_kernel_classes_Resource){
				$returnValue[$activity->uriResource] = $activity;
			}
		}
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProcessVars
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getProcessVars( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF3 begin
		
		$rangeProp = new core_kernel_classes_Property(RDFS_RANGE);
		$widgetProp = new core_kernel_classes_Property(PROPERTY_WIDGET);
		
		$variables = $processDefinition->getPropertyValuesCollection($this->processVariablesProp);
		
		$returnValue[RDFS_LABEL] = array(
			'name' => "Name", 
			'widgets' => WIDGET_FTE,
			'range' => RDFS_LITERAL
		);

		foreach ($variables->getIterator() as $variable){
			
			$widgets = $variable->getPropertyValues($widgetProp);
			$label = $variable->getLabel();
			$range = $variable->getPropertyValues($rangeProp);

			$returnValue[$variable->uriResource] = array(
				'name' => trim(strip_tags($label)), 
				'widgets' => $widgets,
				'range' => $range
			);
			
		}

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002EF3 end

        return (array) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F01 begin
		
		$this->processVariablesProp = new core_kernel_classes_Property(PROPERTY_PROCESS_VARIABLES);
		$this->processActivitiesProp = new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES);
		$this->activitiesIsInitialProp = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
		
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F01 end
    }

    /**
     * Short description of method setProcessVariable
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @param  string processVariable
     * @return boolean
     */
    public function setProcessVariable( core_kernel_classes_Resource $processDefinition, $processVariable)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F06 begin
		if(is_string($processVariable) && !empty ($processVariable)){
			//is a code:
			$variableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
			$processVariableResource = $variableService->getProcessVariable($processVariable);
			if(!is_null($processVariableResource) && $processVariableResource instanceof core_kernel_classes_Resource){
				$returnValue = $processDefinition->setPropertyValue($this->processVariablesProp, $processVariableResource->uriResource);
			}
		}elseif($processVariable instanceof core_kernel_classes_Resource){
			$returnValue = $processDefinition->setPropertyValue($this->processVariablesProp, $processVariable->uriResource);
		}
		
        // section 127-0-1-1--6e15d8e:132297dc60d:-8000:0000000000002F06 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessDefinitionService */

?>