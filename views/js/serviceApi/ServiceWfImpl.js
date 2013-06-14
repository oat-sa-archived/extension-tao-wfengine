function ServiceWfImpl(activityExecutionUri) {
	this.activityExecutionUri = activityExecutionUri;
}

ServiceWfImpl.prototype.connect = function(frame){
	if (typeof(frame.contentWindow.onServiceApiReady) == "function") {
		frame.contentWindow.onServiceApiReady(this);
		return true;
	} else {
		return false;
	}
}

//Context
ServiceWfImpl.prototype.getServiceCallId = function(){
	return this.activityExecutionUri;
}

//Variables 
ServiceWfImpl.prototype.getParameter = function(identifier){
}

// //return execution and return variables to the service caller
ServiceWfImpl.prototype.finish = function(valueArray) {
	if (valueArray != null && typeof(valueArray) == 'object') {
		//store the values
	}
	//return execution to service caller
};

// Helper functions, variable service 
ServiceWfImpl.prototype.setVariable = function(identifier){
}

ServiceWfImpl.prototype.getVariable = function(identifier){
}

