<?php

error_reporting(E_ALL);

/**
 * Factory for creating Process Disagrams
 *
 * @author Joel Bout
 * @package wfEngine
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-includes begin
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-includes end

/* user defined constants */
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-constants begin
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-constants end

/**
 * Factory for creating Process Disagrams
 *
 * @access public
 * @author Joel Bout
 * @package wfEngine
 * @subpackage helpers
 */
class wfEngine_helpers_ProcessDiagramFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Builds a simple Diagram of the Process
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return string
     */
    public function buildDiagramData( core_kernel_classes_Resource $process)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006001 begin
        common_Logger::t('build DiagramData called');
        common_Logger::disable();
        common_Logger::t('build DiagramData called2');
        common_Logger::enable();
        common_Logger::t('build DiagramData called3');
        common_Logger::disable();
        common_Logger::t('build DiagramData called4');
        common_Logger::t('build DiagramData called5');
        common_Logger::restore();
        common_Logger::restore();
        common_Logger::restore();
        common_Logger::restore();
        common_Logger::restore();
        /*
        $authoringService = wfEngine_models_classes_ProcessAuthoringService::singleton();
        $activityService = wfEngine_models_classes_ActivityService::singleton();
        
        $activities = $authoringService->getActivitiesByProcess($process);
        
        $start = array();
        
        $up = array();
        $down = array();
        
        foreach ($activities as $activity) {
        	if ($activityService->isInitial($activity))
        		$start[] = $activity->uriResource;
        	
         	$connectors = $authoringService->getConnectorsByActivity($activity);
         	foreach ($connectors['prev'] as $connector) {
         		$down = $connector->uriResource;
         	}
        }
        
        common_Logger::t('start activities: '.implode(',', $start));
        
        
        
        // former code




        $arrowData		= array();
        $positionData	= array();
        
        //array of ressources
        $activities = $process->getAllPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES));
        
        
        $connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
        
        $labels = array();
        $attachedActivities = array();
        $attachedConnectors	= array();
        $follows = array('c' => array(), 'a' => array());
        $todo = array();
        $connectorPorts = array();
        foreach ($activities as $activity) {
        	$id = substr($activity->uriResource, strrpos($activity->uriResource, 'i'));
        	$initial = $activity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
        	if (!is_null($initial) && $initial->uriResource == GENERIS_TRUE)
        		$todo[] = $id;
        	else {
        		$connectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_NEXTACTIVITIES =>$activity->uriResource), array('like'=>false));
        		foreach ($connectors as $connector) {
        			$previousActivities = $connector->getAllPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES));
        			$cid = substr($connector->uriResource, strrpos($connector->uriResource, 'i'));
        
        			foreach ($previousActivities as $prevAct) {
        				$pid = substr($prevAct->uriResource, strrpos($prevAct->uriResource, 'i'));
        				$attachedActivities[$pid][] = $id;
        				if (!isset($connectorPorts[$cid])) {
        					$attachedConnectors[$pid][] = $cid;
        					$arrowData[] = array("id" => "activity_".$pid."_pos_bottom","targetObject"=> $cid,"type" => "top", "flex" => array(null,0));
        				}
        			}
        
        			// unsure about port nr
        			$connectorPorts[$cid] = isset($connectorPorts[$cid]) ? $connectorPorts[$cid] + 1 : 0;
        			$arrowData[] = array("id" => "connector_".$cid."_pos_bottom_port_".$connectorPorts[$cid],"targetObject"=> $id,"type" => "top", "flex" => array(null,0));
        		}
        	}
        }
        
        $level = 0;
        $done = array();
        while (!empty($todo)) {
        	$next = array();
        	$pos = 0;
        	$connectors = array();
        	foreach ($todo as $id) {
        		// do the positioning;
        		$labels[] = $id.'('.$level.')';
        		$positionData[] = array('id' => $id, 'left' => 54 + (200 * $pos), 'top' => 35 + (125 * $level));
        
        		if (isset($attachedActivities[$id])) {
        			$next = array_merge($next, $attachedActivities[$id]);
        			$connectors = array_merge($connectors, $attachedConnectors[$id]);
        		}
        		$done[] = $id;
        		$pos++;
        	}
        	$pos = 0;
        	foreach ($connectors as $id) {
        		// do the positioning;
        		$labels[] = $id.'['.$level.']';
        		$positionData[] = array('id' => $id, 'left' => 100 + (200 * $pos), 'top' => 105 + (125 * $level));
        		$pos++;
        	}
        	$todo = array_diff(array_unique($next), $done);
        	$level++;
        }
        
        $returnValue = json_encode(array("arrowData" => $arrowData, "positionData" => $positionData));
        /**/
        $diagramData = json_encode(array(
        		"arrowData" => array(),
        		"positionData" => array()
        ));
        $returnValue = $diagramData;
        /**/
        // section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006001 end

        return (string) $returnValue;
    }

} /* end of class wfEngine_helpers_ProcessDiagramFactory */

?>