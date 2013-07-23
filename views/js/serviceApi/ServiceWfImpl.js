function ServiceWfImpl(activityExecutionUri, processUri, nonce) {
	this.activityExecutionUri = activityExecutionUri;
	this.processUri = processUri;
	this.nonce = nonce;
	
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
	return this.activityExecutionUri;
};

//Variables 
ServiceWfImpl.prototype.getParameter = function(identifier){
};

// //return execution and return variables to the service caller
ServiceWfImpl.prototype.finish = function(valueArray) {
	if (valueArray != null && typeof(valueArray) == 'object') {
		//store the values
	}
	
	//go to next
	var url = this.processBrowserModule + 'next'
		+ '?processUri=' + encodeURIComponent(this.processUri)
		+ '&activityUri=' + encodeURIComponent(this.activityExecutionUri)
		+ '&nc=' + encodeURIComponent(this.nonce)
	goToPage(url);
};