// alert("gtw loaded");
var GatewayProcessAuthoring = {name: 'process authoring ontology gateway'};

GatewayProcessAuthoring.addActivity = function(url, processUri){
	$.ajax({
		url: url,
		type: "POST",
		data: {processUri: processUri, type: 'activity'},
		dataType: 'json',
		success: function(response){
			if (response.uri) {
				eventMgr.trigger('activityAdded', response);
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
				eventMgr.trigger('interactiveServiceAdded', response);
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
			if (response.uri) {
				eventMgr.trigger('connectorAdded', response);
			}else{
				throw 'error in adding a connector';
			}
		}
	});

}

GatewayProcessAuthoring.saveActivityProperties = function(url, activityUri, propertiesValues){

	var data = propertiesValues;
	data.activityUri = activityUri;

	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.saved) {
				eventMgr.trigger('activityPropertiesSaved', response);
			}else{
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
					eventMgr.trigger('activityDeleted', response);
				}else{
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
				eventMgr.trigger('connectorDeleted', response);
			}else{
				throw 'error in deleteing the connector';
			}
		}
	});

}

GatewayProcessAuthoring.saveConnector = function(url, connectorUri, prevActivityUri, propertiesValues){

	var data = propertiesValues;
	data.connectorUri = connectorUri;
	data.activityUri = prevActivityUri;

	$.ajax({
		url: url,
		type: "POST",
		data: data,
		dataType: 'json',
		success: function(response){
			if (response.saved){
				eventMgr.trigger('connectorSaved', response);
			}else{
				throw 'error in saving connector';
			}
		}
	});

}

GatewayProcessAuthoring.selectElement = function(elementUri){
	eventMgr.trigger('elementSelected', response);
}