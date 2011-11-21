/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * @namespace wfApi
 * 
 * This file provide functions to drive the workflow engine from a service
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @version 0.2
 */

  ////////////////////
 // WF Controls    //
////////////////////


/**  
 * @namespace wfApi
 */
if(typeof wfApi == 'undefined'){
	wfApi = {};
}

/**
 * The wfengine controlers name
 */
wfApi.ProcessDefinitionControler 	= 'WfApiProcessDefinition';
wfApi.ProcessExecutionControler 	= 'WfApiProcessExecution';
wfApi.ActivityExecutionControler 	= 'WfApiActivityExecution';
wfApi.VariableControler 			= 'WfApiVariable';
wfApi.RecoveryContextControler 		= 'RecoveryContext';

/**
 * Request the workflow engine API on the server
 * @param {String} controler The controler to request
 * @param {String} action The action to request
 * @param {Array} options The options to send to the action
 */
wfApi.request = function(controler, action, parameters, successCallback, errorCallback, options)
{
	var options = typeof options != ('undefined') ? options : new Array();
	var async = typeof options.async != ('undefined') ? options.async : true;
	var url = root_url+'/wfEngine/'+controler+'/'+action;
	console.log(url);
	$.ajax({
		'url'			: url
		, 'type' 		: 'GET'
		, 'dataType'	: 'json'
		, 'data'		: parameters
		, 'async'		: async
		, 'success'		: function(response){
			if(response.success){
				if(typeof successCallback != 'undefined'){
					successCallback(response.data);
				}
			}
			else{
				if(typeof errorCallback != 'undefined'){
					errorCallback(response.data);
				}
			}
		}
		, 'error' 		: function(){
			errorCallback();
		}
	});
}

