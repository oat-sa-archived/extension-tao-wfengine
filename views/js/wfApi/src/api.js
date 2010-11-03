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

var wfApi = {
	'context': window.top.document 
};

/**
 * 
 */
function forward(){
	wfApi.context.getElementById('next').click();
}

/**
 * 
 */
function backward(){
	wfApi.context.getElementById('back').click();
}

/**
 * 
 */
function pause(){
	wfApi.context.getElementById('pause').click();
}

if(typeof(finish) != 'function'){

	/**
	* Define the item's state as finished.
	* This state can have some consequences.
	* 
	* @function
	* @namespace wfApi
	*/
	function finish(){
		$(window).trigger(STATE.ITEM.FINISHED);
	}
	
	/**
	* Add a callback that will be executed on finish state.
	* 
	* @function
	* @namespace wfApi
	* @param {function} callback
	*/
	function onFinish(callback){
		$(window).bind(STATE.ITEM.FINISHED, callback);
	}
	
}

/*
 * By default, the variables are pushed when the item is finished (STATE.ITEM.FINISHED)
 */
$(window).bind(STATE.ITEM.FINISHED, forward);

/**
 * @type RecoveryContext
 */
var recoveryCtx = new RecoveryContext();

/**
 * 
 * @param source
 * @param destination
 */
function initRecoveryContext(source, destination){
	
	recoveryCtx.initSourceService(source);
	recoveryCtx.initDestinationService(destination);
}

/**
 * 
 * @param key
 * @return
 */
function getRecoveryContext(key){
	return recoveryCtx.getContext(key);
}

/**
 * 
 * @param key
 * @param data
 * @return
 */
function setRecoveryContext(key, data){
	recoveryCtx.setContext(key, data);
	recoveryCtx.saveContext();
}

function deleteRecoveryContext(key){
	setRecoveryContext(key, null);
}
