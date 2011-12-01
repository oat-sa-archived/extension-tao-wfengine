<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/helpers/Monitoring/class.ActivityPropertiesAdapter.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.11.2011, 11:06:27 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_Cell_Adapter
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.Adapter.php');

/* user defined includes */
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfEngine_helpers_Monitoring_ActivityPropertiesAdapter
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334F begin
		if (isset($this->data[$rowId])) {

			//return values:
			if (isset($this->data[$rowId][$columnId])) {
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		} else {

//			'PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY' => NS_WFENGINE . '#PropertyActivityExecutionsContextRecovery',
//			'PROPERTY_ACTIVITY_EXECUTION_VARIABLES' => NS_WFENGINE .'#PropertyActivityExecutionsHasVariables',
//			'PROPERTY_ACTIVITY_EXECUTION_PREVIOUS' => NS_WFENGINE .'#PropertyActivityExecutionsPreviousActivityExecutions',
//			'PROPERTY_ACTIVITY_EXECUTION_FOLLOWING' => NS_WFENGINE .'#PropertyActivityExecutionsFollowingActivityExecutions',
//			'PROPERTY_ACTIVITY_EXECUTION_NONCE' => NS_WFENGINE . '#PropertyActivityExecutionsNonce',

			if (common_Utils::isUri($rowId)) {

				$excludedProperties = $this->excludedProperties;
				$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
				$activityExecution = new core_kernel_classes_Resource($rowId);
				
				$this->data[$rowId] = array();

				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY, $excludedProperties)) {
					$activityExecutionOf = $activityExecutionService->getExecutionOf($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_ACTIVITY] = $activityExecutionOf->getLabel();
				}

				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_STATUS, $excludedProperties)) {
					$status = $activityExecutionService->getStatus($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_STATUS] = is_null($status) ? null : $status->getLabel();
				}
				
				$timeProperties = array(
					PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED,
					PROPERTY_ACTIVITY_EXECUTION_TIME_STARTED,
					PROPERTY_ACTIVITY_EXECUTION_TIME_LASTACCESS
				);
				foreach($timeProperties as $timeProperty){
					if (!in_array($timeProperty, $excludedProperties)) {
						$time = (string) $activityExecution->getOnePropertyValue(new core_kernel_classes_Property($timeProperty));
						$this->data[$rowId][$timeProperty] = !empty($time)?date('d-m-Y G:i:s', $time):'n/a';
					}
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER, $excludedProperties)){
					$user = $activityExecutionService->getActivityExecutionUser($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER] = (is_null($user))?'n/a':$user->getLabel();
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_ACL_MODE, $excludedProperties)){
					$aclMode = $activityExecutionService->getAclMode($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_ACL_MODE] = (is_null($aclMode))?'n/a':$aclMode->getLabel();
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER, $excludedProperties)){
					$restricedRole = $activityExecutionService->getRestrictedRole($activityExecution);
					$restrictedTo = !is_null($restricedRole) ? $restricedRole : $activityExecutionService->getRestrictedUser($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER] = (is_null($restrictedTo))?'n/a':$restrictedTo->getLabel();
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION, $excludedProperties)){
					$processExecution = $activityExecutionService->getRelatedProcessExecution($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION] = (is_null($processExecution))?'n/a':$processExecution->uriResource;
				}
				
				
				if (isset($this->data[$rowId][$columnId])) {
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
		}
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334F end

        return $returnValue;
    }

} /* end of class wfEngine_helpers_Monitoring_ActivityPropertiesAdapter */

?>