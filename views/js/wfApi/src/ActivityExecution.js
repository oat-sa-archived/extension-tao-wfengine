/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi.ActivityExecution
 * 
 * This file provide functions to drive the activities execution from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  ////////////////////////////////////////
 // WF Activity Execution Controls    //
///////////////////////////////////////


/**  
 * @namespace wfApi.ActivityExecution
 */
wfApi.ActivityExecution = {};

/**
 * Assign an activity execution to a user
 * @param {String} activityExecutionUri The activity execution to assign
 * @param {String} userUri The user to drive to the activity execution
 */
wfApi.ActivityExecution.assign = function(activityExecutionUri, userUri, successCallback, errorCallback)
{
	return wfApi.request(wfApi.ActivityExecutionControler, 'assign', {activityExecutionUri:activityExecutionUri, userUri:userUri}, successCallback, errorCallback);
};
