/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * 
 * This file provide functions to drive the workflow engine from a service
 * 
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @version 0.1
 */ 


  ////////////////////
 // WF Controls    //
////////////////////

var wfApi = { context : window.top.document };


/**
 * Trigger the forward control:
 * go to the next activity
 * 
 * @function
 * @namespace wfApi
 */
function forward(){
	wfApi.context.getElementById('next').click();
}

/**
 * Trigger the backward control:
 * load to the previous activity
 * 
 * @function
 * @namespace wfApi
 */
function backward(){
	wfApi.context.getElementById('back').click();
}

/**
 * Trigger the pause control:
 * suspend the current activity and go back to the process control panel
 * 
 * @function
 * @namespace wfApi
 */
function pause(){
	wfApi.context.getElementById('pause').click();
}


  /////////////
 // STATES  //
/////////////

if(typeof(finish) != 'function'){

	//get the highest context 
	var _stateContext = window.top || window;
	
	/**
	 * Define the item's state as finished.
	 * This state can have some consequences.
	 * 
	 * @function
	 * @namespace wfApi
	 */
	function finish(){
		$(_stateContext).trigger(STATE.ITEM.PRE_FINISHED);
		$(_stateContext).trigger(STATE.ITEM.FINISHED);
		$(_stateContext).trigger(STATE.ITEM.POST_FINISHED);
	}
	
	/**
	 * Add a callback that will be executed on finish state.
	 * 
	 * @function
	 * @namespace wfApi
	 * @param {function} callback
	 */
	function onFinish(callback){
		$(_stateContext).bind(STATE.ITEM.FINISHED, callback);
	}
	
	/**
	 * Add a callback that will be executed on finish but before the other callbacks  
	 * 
	 * @function
	 * @namespace wfApi
	 * @param {function} callback
	 */
	function beforeFinish(callback){
		$(_stateContext).bind(STATE.ITEM.PRE_FINISHED, callback);
	}
	
	/**
	 * Add a callback that will be executed on finish but after the other callbacks  
	 * 
	 * @function
	 * @namespace wfApi
	 * @param {function} callback
	 */
	function afterFinish(callback){
		$(_stateContext).bind(STATE.ITEM.POST_FINISHED, callback);
	}

}

/*
 * By default, the variables are pushed when the item is finished
 */
afterFinish(forward);


  ///////////////
 // RECOVERY  //
///////////////

/**
 * instantiate the RecoveryContext
 * 
 * @function
 * @namespace wfApi
 * @type RecoveryContext
 */
var recoveryCtx = new RecoveryContext();

/**
 * Initialize the interfaces communication for the context recovery.
 * The source service defines where and how we retrieve contexts.
 * The destination service defines where and how we send/save the contexts.
 * 
 * @function
 * @namespace wfApi
 * 
 * @param {Object} source 
 * @param {String} [source.type = "sync"] the type of source <b>(sync|async|manual)</b>
 * @param {Object} [source.data] For the <i>manual</i> source type, set direclty the context data structure
 * @param {String} [source.url = "/wfEngine/Context/retrieve"] For the <i>sync</i> and <i>async</i> source type, the URL of the remote service
 * @param {Object} [source.params] the parameters to send to the remote service
 * @param {String} [source.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [source.method = "post"] HTTP method of the sync service <b>(get|post)</b>
 * 
 * @param {Object} destination
 * @param {String} [destination.type = "async"] the type of source <b>(sync|async)</b>
 * @param {String} [destination.url = "/wfEngine/Context/save"] the URL of the remote service
 * @param {Object} [destination.params] the common parameters to send to the service
 * @param {String} [destination.format = "json"] the data format. <i>Only json is supported in the current version</i> 
 * @param {String} [destination.method = "post"] HTTP method of the service <b>(get|post)</b>
 */
function initRecoveryContext(source, destination){
	recoveryCtx.initSourceService(source);
	recoveryCtx.initDestinationService(destination);
}

/**
 * Retrieve the context identified by the key 
 * 
 * @function
 * @namespace wfApi
 * 
 * @param {String} key
 * @return {Object}
 */
function getRecoveryContext(key){
	return recoveryCtx.getContext(key);
}

/**
 * Set, send and save a context that could be recovered 
 * 
 * @function
 * @namespace wfApi
 * 
 * @param {String} key
 * @param {Object} data
 */
function setRecoveryContext(key, data){
	recoveryCtx.setContext(key, data);
	recoveryCtx.saveContext();
}

/**
 * Remove a context (once you don't need you to recover it anymore) 
 * 
 * @function
 * @namespace wfApi
 * 
 * @param {String} key
 */
function deleteRecoveryContext(key){
	setRecoveryContext(key, null);
}
