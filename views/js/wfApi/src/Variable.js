/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi.Variable
 * 
 * This file provide functions to drive the variables from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  //////////////////////////////
 // WF Variables Controls    //
//////////////////////////////


/**  
 * @namespace wfApi.Variable
 */
wfApi.Variable = {};

/**
 * Get the variable's value
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to get
 */
wfApi.Variable.get = function(activityExecutionUri, code, successCallback, errorCallback)
{
	return wfApi.request(wfApi.VariableControler, 'get', {activityExecutionUri:activityExecutionUri, code:code}, successCallback, errorCallback);
};

/**
 * Push a variable
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to push
 * @param {String} value The value of the variable to push
 */
wfApi.Variable.push = function(activityExecutionUri, code, value, successCallback, errorCallback)
{
	return wfApi.request(wfApi.VariableControler, 'push', {activityExecutionUri:activityExecutionUri, code:code, value:value}, successCallback, errorCallback);
};

/**
 * Set the variable's value
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to set
 * @param {String} value The value of the variable to set
 */
wfApi.Variable.edit = function(activityExecutionUri, code, value, successCallback, errorCallback)
{
	return wfApi.request(wfApi.VariableControler, 'edit', {activityExecutionUri:activityExecutionUri, code:code, value:value}, successCallback, errorCallback);
};

/**
 * Remove a variable
 * @param {String} activityExecutionUri The target activity exectuion 
 * @param {String} code The code of the variable to remove
 */
wfApi.Variable.remove = function(activityExecutionUri, code, successCallback, errorCallback)
{
	return wfApi.request(wfApi.VariableControler, 'remove', {activityExecutionUri:activityExecutionUri, code:code}, successCallback, errorCallback);
};
