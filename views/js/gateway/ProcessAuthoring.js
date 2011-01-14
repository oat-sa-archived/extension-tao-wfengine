// alert("gtw loaded");
var GatewayProcessAuthoring = {name: 'process authoring ontology gateway'};

GatewayProcessAuthoring.addActivity = function(url, processUri){
	$.ajax({
		url: url,
		type: "POST",
		data: {processUri: processUri, type: 'activity'},
		dataType: 'json',
		success: function(response){
			// CL(response);
			if (response.uri) {
				EventMgr.trigger('activityAdded', response);
			}else{
				CL('error in adding an activity');
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
				//console.log('error in adding a connector');
			}
		}
	});
	
}

GatewayProcessAuthoring.saveActivityProperties = function(url, activityUri, serializedProperties){
	
	// prevActivityUri of either a connector or an activity
	if(serializedProperties.substr(0,1) == '&'){
		serializedProperties = serializedProperties.substr(1);
	}
	
	var data = '';
	data += 'activityUri='+activityUri;
	data += '&' + serializedProperties;
	
	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.saved) {
				EventMgr.trigger('activityPropertiesSaved', response);
			}else{
				// console.log(response);
				throw 'error in saving activity properties';
			}
		}
	});
	
}

GatewayProcessAuthoring.deleteActivity = function(url, activityUri){
	if(confirm(__("Please confirm the deletion of the activity"))){
		$.ajax({
			url: url,
			type: "POST",
			data: {"activityUri": activityUri},
			dataType: 'json',
			success: function(response){
				if(response.deleted){
					EventMgr.trigger('activityDeleted', response);
				}else{
					// console.log(response);
					throw 'error in deleteing the activity';
				}
			}
		});
	}
}

GatewayProcessAuthoring.deleteConnector = function(url, connectorUri){
	
	$.ajax({
		url: url,
		type: "POST",
		data: {"connectorUri": connectorUri},
		dataType: 'json',
		success: function(response){
			if(response.deleted){
				EventMgr.trigger('connectorDeleted', response);
				
			}else{
				throw 'error in deleteing the connector';
			}
		}
	});
	
}

GatewayProcessAuthoring.saveConnector = function(url, connectorUri, prevActivityUri, serializedProperties){
	if(serializedProperties.substr(0,1) == '&'){
		serializedProperties = serializedProperties.substr(1);
	}
	
	var data = '';
	data += 'connectorUri=' + connectorUri;
	data += '&activityUri=' + prevActivityUri;
	data += '&' + serializedProperties;
	
	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.saved) {
				EventMgr.trigger('connectorSaved', response);
			}else{
				// console.log(response);
				throw 'error in saving connector';
			}
		}
	});
	
}