/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi.ProcessDefinition
 * 
 * This file provide functions to drive the processes definition from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  //////////////////////////////////////
 // WF Process Definition Controls    //
//////////////////////////////////////


/**  
 * @namespace wfApi.ProcessDefinition
 */
wfApi.ProcessDefinition = {};

/**
 * 
 * @param {String}
 */
wfApi.ProcessDefinition.initExecution = function(processDefinitionUri, successCallback, errorCallback)
{
	wfApi.request(wfApi.ProcessDefinitionControler, 'initExecution', {processDefinitionUri:processDefinitionUri}, successCallback, errorCallback);
};

/**
 * 
 * @param {String}
 */
wfApi.ProcessDefinition.getName = function(processDefinitionUri, successCallback, errorCallback)
{
	wfApi.request(wfApi.ProcessDefinitionControler, 'getName', {processDefinitionUri:processDefinitionUri}, successCallback, errorCallback);
};
