// alert("gtw loaded");
var GatewayProcessAuthoring = {name: 'process authoring ontology gateway'};

GatewayProcessAuthoring.addActivity = function(url, processUri){
	// console.log('url', url);	
	// console.log('processUri', processUri);
	
	$.ajax({
		url: url,
		type: "POST",
		data: {processUri: processUri, type: 'activity'},
		dataType: 'json',
		success: function(response){
			// console.log(response);
			if (response.uri) {
				EventMgr.trigger('activityAdded', response);
			}else{
				console.log('error in adding an activity');
			}
		}
	});
	
}

GatewayProcessAuthoring.addInteractiveService = function(url, activityUri, serviceDefinitionUri){
	// console.log('url', url);	
	// console.log('processUri', processUri);
	data = {activityUri: activityUri, type: 'interactive-service'};
	if(serviceDefinitionUri){
		data.serviceDefinitionUri = serviceDefinitionUri;
	}
	
	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.uri) {
				EventMgr.trigger('interactiveServiceAdded', response);
				
			}
		}
	});
	
}

GatewayProcessAuthoring.addConnector = function(url, prevActivityUri,typeOfConnector){
	
	// prevActivityUri of either a connector or an activity
	
	$.ajax({
		url: url,
		type: "POST",
		data: {"uri": prevActivityUri, "type":typeOfConnector},
		dataType: 'json',
		success: function(response){
			// console.log(response);
			if (response.uri) {
				EventMgr.trigger('connectorAdded', response);
			}else{
				console.log('error in adding a connector');
			}
		}
	});
	
}