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
    public static function buildDiagramData( core_kernel_classes_Resource $process)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006001 begin
		
		common_Logger::i("Building diagram for ".$process->getLabel());
		
        $authoringService = wfEngine_models_classes_ProcessAuthoringService::singleton();
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$activityCardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		$activities = $authoringService->getActivitiesByProcess($process);
		
		$todo = array();
		foreach ($activities as $activity) {
			if ($activityService->isInitial($activity)) {
				$todo[] = $activity;
			}
		}
		
		$currentLevel = 0;
		$diagram = new wfEngine_models_classes_ProcessDiagram();
		$done = array();
		while (!empty($todo)) {
		
			$nextLevel = array();
			$posOnLevel = 0;
			foreach ($todo as $item) {
				
				$next = array();
				
				if ($activityService->isActivity($item)) {
					// add this activity
					$diagram->addActivity($item, 54 + (200 * $posOnLevel) + (10*$currentLevel), 35 + (80 * $currentLevel));
					$next = array_merge($next, $activityService->getNextConnectors($item));
				} elseif ($connectorService->isConnector($item)) {
					// add this connector
					$diagram->addConnector($item, 100 + (200 * $posOnLevel) + (10*$currentLevel), 40 + (80 * $currentLevel));
					$next = array_merge($next,$connectorService->getNextActivities($item));
				} else {
					common_Logger::w('unexpected ressource in process '.$item->getUri());
				}
				
				//replace cardinalities
				foreach ($next as $key => $destination) {
					if ($activityCardinalityService->isCardinality($destination)) {
						// not represented on diagram
						$next[$key] = $activityCardinalityService->getDestination($destination);
					}
				}	
				
				//add arrows
				foreach ($next as $destination) {
					$diagram->addArrow($item, $destination);
				}
		
				$posOnLevel++;
				$nextLevel = array_merge($nextLevel, $next);
			}
			$done = array_merge($done, $todo);
			$todo = array_diff($nextLevel, $done);
			$currentLevel++;
		}
		$returnValue = $diagram->toJSON();
        // section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006001 end

        return (string) $returnValue;
    }

} /* end of class wfEngine_helpers_ProcessDiagramFactory */

?>