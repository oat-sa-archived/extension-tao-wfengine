<?include(BASE_PATH.'/'.DIR_VIEWS.$GLOBALS['dir_theme'].'header.tpl')?>

<?if(get_data('error')):?>

	<div class="main-container">
		<div class="ui-state-error ui-corner-all" style="padding:5px;">
			<?if(get_data('extension')=='taoDelivery'){
				echo __('Please select a delivery before authoring it');
			}else{//==wfEngine
				echo __('Please select a process before authoring it');
			}?>
			<br/>
			<?=get_data('errorMessage')?>
		</div>
		<br />
		<span class="ui-widget ui-state-default ui-corner-all" style="padding:5px;">
			<?if(get_data('extension')=='taoDelivery'):?>
				<a href="#" onclick="selectTabByName('manage_deliveries');"><?=__('Back')?></a>
			<?else://==wfEngine?>
				<a href="#" onclick="selectTabByName('manage_process');"><?=__('Back')?></a>
			<?endif;?>
		</span>
	</div>
	
<?else:?>
	<link rel="stylesheet" type="text/css" href="<?=PROCESS_BASE_WWW?>css/process_authoring_tool.css" />
	
	<script type="text/javascript">
		//constants:
		RDFS_LABEL = "<?=tao_helpers_Uri::encode(RDFS_LABEL)?>";
		PROPERTY_CONNECTORS_TYPE = "<?=tao_helpers_Uri::encode(PROPERTY_CONNECTORS_TYPE)?>";
		INSTANCE_TYPEOFCONNECTORS_SEQUENCE = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_SEQUENCE)?>";
		INSTANCE_TYPEOFCONNECTORS_SPLIT = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_SPLIT)?>";
		INSTANCE_TYPEOFCONNECTORS_PARALLEL = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_PARALLEL)?>";
		INSTANCE_TYPEOFCONNECTORS_JOIN = "<?=tao_helpers_Uri::encode(INSTANCE_TYPEOFCONNECTORS_JOIN)?>";
	</script>
	
	<script type="text/javascript" src="<?=BASE_WWW.'js/authoring/'?>authoringConfig.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>json2.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>util.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>arrows.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>activityDiagram.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeController.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeInitial.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeActivityLabel.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeActivityMenu.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeArrowLink.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeActivityMove.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeConnectorMove.js"></script>
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>modeArrowEdit.js"></script>
	
	<script type="text/javascript">
	//init:
	var processUri = "<?=get_data("processUri")?>";
	ActivityDiagramClass.canvas = "#process_diagram_container";
	ActivityDiagramClass.localNameSpace = "<?=tao_helpers_Uri::encode(core_kernel_classes_Session::singleton()->getNameSpace().'#')?>";
	
	
	
	$(function() {
		$(ActivityDiagramClass.canvas).scroll(function(){
			//TODO: set a more cross-browser way to retrieve scroll left and top values:
			ActivityDiagramClass.scrollLeft = this.scrollLeft;
			ActivityDiagramClass.scrollTop = this.scrollTop;
		});
		
		try{
			ActivityDiagramClass.loadDiagram();
		}
		catch(err){
			// console.log('feed&draw diagram exception', err);
		}
		
	});

	</script>

	<div class="main-container" style="display:none;"></div>
	<div id="authoring-container" class="ui-helper-reset">
	<div id="process_center_panel">
		<div id="process_diagram_feedback" ></div>
		<div id="process_diagram_container" >
			<div id="status"/>
		</div>
	</div>
	<div id="accordion1" style="font-size:0.8em;">
		<h3><a href="#"><?=__('Service Definition')?></a></h3>
		<div>
			<div id="serviceDefinition_tree"/>
			<div id="serviceDefinition_form"/>
		</div>
		<h3><a href="#"><?=__('Formal Parameter')?></a></h3>
		<div>
			<div id="formalParameter_tree"/>
			<div id="formalParameter_form"/>
		</div>
		<h3><a href="#"><?=__('Role')?></a></h3>
		<div>
			<div id="role_tree"/>
			<div id="role_form"/>
		</div>
		<h3><a href="#"><?=__('Process Variables')?></a></h3>
		<div>
			<div id="variable_tree"/>
			<div id="variable_form"/>
		</div>
	</div><!--end accordion -->
	
	<div id="accordion_container_2">
	<div id="accordion2" style="font-size:0.8em;">
		<h3><a href="#"><?=__('Activity Editor')?></a></h3>
		<div>
			<div id="activity_tree"/>
			<div id="activity_form"/>
		</div>
		<h3><a href="#"><?=__('Process Property')?></a></h3>
		<div>
			<div id="process_form"><?=__('loading...')?></div>
		</div>
		<?if(get_data('extension')=='taoDelivery'):?>
		<h3><a href="#"><?=__('Compilation')?></a></h3>
		<div>
			<div id="compile_info"><?=__('loading...')?></div>
			<div id="compile_form"></div>
		</div>
		<?endif;?>
	</div><!--end accordion -->
	</div><!--end accordion_container_2 -->
	
	</div><!--end authoring-container -->
	
	<script type="text/javascript" src="<?=PROCESS_SCRIPT_URL?>activity.tree.js"></script>
	<script type="text/javascript">
	
	$(function(){
		EventMgr.unbind();
		
		EventMgr.bind('activityAdded', function(event, response){
			
			try{
				var activity = ActivityDiagramClass.feedActivity({
					"data": response.label,
					"attributes": {"id": response.uri}
				});
				
				//draw activity with the default positionning:
				ActivityDiagramClass.drawActivity(activity.id);
				ActivityDiagramClass.setActivityMenuHandler(activity.id);
				
				//draw arrow if need be (i.e. in case of adding an activity with a connector)
				if(response.previousConnectorUri && response.port>=0){
					//should be a connector:
					var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousConnectorUri);
					var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);
					
					var activityId = ActivityDiagramClass.getActivityId('container', activity.id);
					ActivityDiagramClass.positionNewActivity($('#'+originEltId), $('#'+activityId));
					// ActivityDiagramClass.setActivityMenuHandler(activityId);
					
					//create and draw arrow:
					var activityTopId = ActivityDiagramClass.getActivityId('activity', activity.id, 'top');
					ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+activityTopId), 'top', new Array(), false);
					ArrowClass.drawArrow(arrowId, {
						container: ActivityDiagramClass.canvas,
						arrowWidth: 2
					});
					
					//save diagram:
					ActivityDiagramClass.saveDiagram();
				}
			}catch(ex){
				// console.log('activityAdded exception:', ex);
			}
		});
		
		EventMgr.bind('connectorAdded', function(event, response){
			try{
				//a connector is always added throught the "linked mode"
				var previousObjectId = ActivityDiagramClass.getIdFromUri(response.previousActivityUri);
				if(response.previousIsActivity){
					var originEltId = ActivityDiagramClass.getActivityId('activity', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('activity', previousObjectId, 'bottom');
					
					var activityRefId = previousObjectId;
				}else{
					//should be a connector:
					var originEltId = ActivityDiagramClass.getActivityId('connector', previousObjectId);
					var arrowId = ActivityDiagramClass.getActivityId('connector', previousObjectId, 'bottom', response.port);
					if(ActivityDiagramClass.connectors[previousObjectId]){
						var activityRefId = ActivityDiagramClass.connectors[previousObjectId].activityRef;
						
						//update the local datastore on the previous activity:
						ActivityDiagramClass.connectors[previousObjectId].port[response.port].targetId = ActivityDiagramClass.getIdFromUri(response.uri);
						//update multiplicity here?
					}else{
						throw 'the connector does not exist in the connectors array';
					}
					
				}
				
				var connector = ActivityDiagramClass.feedConnector(
					{
						"data": response.label,
						"attributes": {"id": response.uri},
						"type": response.type
					},
					null,
					previousObjectId,
					null,
					activityRefId
				);
				
				//draw connector and reposition it:
				var connectorId = ActivityDiagramClass.getActivityId('connector', connector.id);
				var connectorTopId = ActivityDiagramClass.getActivityId('connector', connector.id, 'top');
				
				ActivityDiagramClass.drawConnector(connector.id);
				ActivityDiagramClass.positionNewActivity($('#'+originEltId), $('#'+connectorId));
				ActivityDiagramClass.setConnectorMenuHandler(connector.id);
				
				//create and draw arrow:
				ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+connectorTopId), 'top', new Array(), false);
				ArrowClass.drawArrow(arrowId, {
					container: ActivityDiagramClass.canvas,
					arrowWidth: 2
				});
				
				//save diagram:
				ActivityDiagramClass.saveDiagram();
			}catch(ex){
				// console.log('connectorAdded exception:', ex);
				// console.log('connector', connector);
				// console.log('originEltId', originEltId);
				// console.log('connectorId', connectorId);
				// console.log('arrowId', arrowId);
			}
				
		});
		
		EventMgr.bind('connectorSaved', function(event, response){
			var added = false
			if(response.newActivities && response.previousConnectorUri){
				if(response.newActivities.length > 0){
					var activityAddedResponse = response.newActivities[0];//currently, the first one is enough
					activityAddedResponse.previousConnectorUri = response.previousConnectorUri;
					EventMgr.trigger('activityAdded', activityAddedResponse);
					added = true;
				}
			}
			
			if(response.newConnectors && response.previousConnectorUri){
				if(response.newConnectors.length > 0){
					var connectorAddedResponse = response.newConnectors[0];//currently, the first one is enough
					connectorAddedResponse.previousActivityUri = response.previousConnectorUri;
					connectorAddedResponse.previousIsActivity = false;//the previous activity is obviously a connector here
					EventMgr.trigger('connectorAdded', connectorAddedResponse);
					added = true;
				}
			}
			
			if(!added){
				//reload the tree:
				ActivityDiagramClass.refreshRelatedTree();
				ActivityDiagramClass.loadDiagram();
			}
			
		});
		
		
		EventMgr.bind('activityPropertiesSaved', function(event, response){
			//simply reload the tree:
			ActivityDiagramClass.refreshRelatedTree();
		});
		
		EventMgr.bind('activityDeleted', function(event, response){
			ActivityDiagramClass.reloadDiagram();
		});
		
		EventMgr.bind('connectorDeleted', function(event, response){
			ActivityDiagramClass.reloadDiagram();
		});
		
		$(ActivityDiagramClass.canvas).click(function(evt){
			if (evt.target == evt.currentTarget) {
				ModeController.setMode('ModeInitial');
			}
		});
		
		
		$("#accordion1").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: false,
			active: 0,
			icons: { 'header': 'ui-icon-circle-triangle-s', 'headerSelected': 'ui-icon-circle-triangle-e' }
		});
		
		//load activity tree:
		loadActivityTree();
		
		
		//load the trees:
		loadSectionTree("serviceDefinition");//use get_value instead to get the uriResource of the service definition class and make
		loadSectionTree("formalParameter");
		loadSectionTree("role");
		loadSectionTree("variable");
		
		processProperty();
		
		<?if(get_data('extension')=='taoDelivery'):?>
		loadCompilationForm();
		<?endif;?>
	});
	
	$(function(){
		$("#accordion2").accordion({
			fillSpace: false,
			autoHeight: false,
			collapsible: false,
			icons: { 'header': 'ui-icon-circle-triangle-s', 'headerSelected': 'ui-icon-circle-triangle-e' }
		});
		
		//load the trees:
		
	});
	
	function processProperty(){
		_load("#process_form", 
			authoringControllerPath+"editProcessProperty", 
			{processUri: processUri}
		);
	}
	
	function loadSectionTree(section){
	//section in [serviceDefinition, formalParameter, role]
		$.ajax({
			url: authoringControllerPath+'getSectionTrees',
			type: "POST",
			data: {section: section},
			dataType: 'html',
			success: function(response){
				$('#'+section+'_tree').html(response);
			}
		});
	}
	
	function loadActivityTree(){
		$.ajax({
			url: authoringControllerPath+'getActivityTree',
			type: "POST",
			data: {section: "activity"},
			dataType: 'html',
			success: function(response){
				$('#activity_tree').html(response);
			}
		});
	}
	
	function loadCompilationForm(){
		$.ajax({
			url: authoringControllerPath+'compileView',
			type: "POST",
			data: {processUri: processUri},
			dataType: 'html',
			success: function(response){
				$('#compile_info').html(response);
			}
		});
	}
	</script>
	
<?endif;?>

<?include(BASE_PATH.'/'.DIR_VIEWS.$GLOBALS['dir_theme'].'footer.tpl')?>
