function ServiceWfImpl(callId, wfRunner) {
	this.wfRunner = wfRunner;
	this.callId = callId;

	this.beforeClose = new Array();	
	
	this.processBrowserModule = window.location.href.replace(/^(.*\/)[^/]*/, "$1");
}

ServiceWfImpl.prototype.connect = function(frame){
	if (typeof(frame.contentWindow.onServiceApiReady) == "function") {
		frame.contentWindow.onServiceApiReady(this);
		return true;
	} else {
		return false;
	}
};

//Context
ServiceWfImpl.prototype.getServiceCallId = function(){
	return this.callId;
};

//Variables 
ServiceWfImpl.prototype.getParameter = function(identifier){
};

ServiceWfImpl.prototype.beforeClose = function(callback){
	console.log('beforeClose received by ServiceWfImpl');
	this.beforeClose.push(callback);
};

// //return execution and return variables to the service caller
ServiceWfImpl.prototype.finish = function(valueArray) {
	if (valueArray != null && typeof(valueArray) == 'object') {
		//store the values
	}
	this.wfRunner.forward();
};