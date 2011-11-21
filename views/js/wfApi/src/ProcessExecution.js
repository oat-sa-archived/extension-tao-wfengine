/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi.ProcessExecution
 * 
 * This file provide functions to drive the processes execution from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  //////////////////////////////////////
 // WF Process Execution Controls    //
//////////////////////////////////////


/**  
 * @namespace wfApi.ProcessExecution
 */
wfApi.ProcessExecution = {};

/**
 * Delete a process execution
 * @param {String} processExecutionUri The process execution to delete
 */
wfApi.ProcessExecution.delete = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'delete', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Pause a process execution 
 * @param {String} processExecutionUri The process execution to pause
 */
wfApi.ProcessExecution.pause = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'pause', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Cancel a process execution
 * @param {String} processExecutionUri The process execution to cancel
 */
wfApi.ProcessExecution.cancel = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'cancel', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Resule a process execution
 * @param {String} processExecutionUri The process execution to resume
 */
wfApi.ProcessExecution.resume = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'resume', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Drive the process execution to the next activity
 * @param {String} processExecutionUri The process execution to drive
 */
wfApi.ProcessExecution.next = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'next', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};

/**
 * Drive the process execution to the previous activity
 * @param {String} processExecutionUri The process execution to drive
 */
wfApi.ProcessExecution.previous = function(processExecutionUri, successCallback, errorCallback, options)
{
	wfApi.request(wfApi.ProcessExecutionControler, 'previous', {processExecutionUri:processExecutionUri}, successCallback, errorCallback, options);
};
