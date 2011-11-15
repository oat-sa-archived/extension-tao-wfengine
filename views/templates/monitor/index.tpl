<!-- <script type="text/javascript" src="<?=ROOT_URL?>/taoItems/models/ext/itemAuthoring/waterphenix/lib/wuib/wuib.min.js"></script> -->
<style> 
	#filter-container { width:19%;  height:561px; }
	.main-container { height:584px; padding:0; margin:0; overflow:auto !important; }
	#monitoring-processes-container, #process-details-container { padding:0; height:50%; overflow:auto; }
	.current_activities_container { height:100%; }
	#process-details-tabs { height:269px; }
	.tabs-bottom { position: relative; } 
	.tabs-bottom .ui-tabs-panel { height:100%; overflow: auto; }
	.tabs-bottom .ui-tabs-nav { position: absolute !important; left: 0; bottom: 0; right:0; padding: 0 0.2em 0.2em 0; } 
	.tabs-bottom .ui-tabs-nav li { margin-top: -2px !important; margin-bottom: 1px !important; border-top: none; border-bottom-width: 1px; }
	.ui-tabs-selected { margin-top: -3px !important; }
</style>

<div id="filter-container" class="data-container tabs-bottom">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Filter')?>
	</div>
	<ul class="4ui-helper-hidden-accessible">	
		<li><a href="#tabs-1">Query</a></li>
		<li><a href="#tabs-2">Text</a></li>
		<li><a href="#tabs-3">Facet</a></li>
	</ul>
	<div id="tabs-1">
		<div id="facet-filter"/>
	</div>
	<div id="tabs-2">tabs-2
		<? //include() ?>
	</div>
	<div id="tabs-3">tabs-3
		<? //include() ?>
	</div>
</div>

<div class="main-container">
	<div id="monitoring-processes-container">
		<table id="monitoring-processes-grid">
		</table>
	</div>
	<div id="process-details-container" class="tabs-bottom">
		<div id="process-details-tabs">
			<ul class="4ui-helper-hidden-accessible">	
				<li><a href="#current_activities_container">Current Activities</a></li>
				<li><a href="#history_process_container">History</a></li>
			</ul>
			<div id="current_activities_container">
				<table id="current-activities-grid">
				</table>
			</div>
			<div id="history_process_container">
				<table id="history-process-grid">
				</table>
			</div>
		</div>
	</div>	
</div>

<script type="text/javascript">
$(function(){

	var filterTabs = new TaoTabsClass('#filter-container', {'position':'bottom'});
	var processDetailsTabs = new TaoTabsClass('#process-details-tabs', {'position':'bottom'});
	var monitoringData = new Array();

	/*
	 * instantiate the facet based filter widget
	 */

	var getUrl = root_url + '/wfEngine/Monitor/getFilteredInstancesPropertiesValues';
	//the facet filter options
	var facetFilterOptions = {
		'template' : 'accordion',
		'callback' : {
			'onFilter' : function(filter, filterNodesOpt){
				//refreshResult(filter, filterNodesOptions);
				//console.log('yeah filter');
				var formatedFilter = {};
				for(var filterNodeId in filter){
					var propertyUri = filterNodesOpt[filterNodeId]['propertyUri'];
					typeof(formatedFilter[propertyUri])=='undefined'?formatedFilter[propertyUri]=new Array():null;
					for(var i in filter[filterNodeId]){
						formatedFilter[propertyUri].push(filter[filterNodeId][i]);
					}
				}
				loadMonitoringGrid(formatedFilter);
			}
		}
	};
	//set the filter nodes
	var filterNodes = [
		<?foreach($properties as $property):?>
		{ 
			id					: '<?=md5($property->uriResource)?>'
			, label				: '<?=$property->getLabel()?>'
			, url				: getUrl
			, options 			: 
			{ 
				'propertyUri' 	: '<?= $property->uriResource ?>'
				, 'classUri' 	: '<?= $clazz->uriResource ?>'
			}
		},
		<?endforeach;?>
	];
	//instantiate the facet filter
	var facetFilter = new GenerisFacetFilterClass('#facet-filter', filterNodes, facetFilterOptions);


	/*
	 * instantiate the dynamic grid
	 */

	function loadMonitoringGrid(filter){
		$.getJSON (root_url+'/wfEngine/Monitor/monitorProcess'
			,{
				'filter':filter
			}
			, function (DATA) {
				monitoringData = DATA;
				monitoringGrid.empty();
				monitoringGrid.add(DATA);
			}
		);
	}
	 
	//the grid model
	var model = <?=$model?>;
	//the monitoring grid options
	var monitoringGridOptions = {
		'height' : $('#monitoring-processes-grid').parent().height()
		, 'title' 	: __('Monitoring processes')
		, 'callback' : {
			'onSelectRow' : function(id)
			{
				currentActivitiesGrid.empty();
				currentActivitiesGrid.add(monitoringData[id]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']);

				$.getJSON (root_url+'/wfEngine/Monitor/processHistory'
					,{
						'uri':id
					}
					, function (DATA) {
						historyProcessGrid.empty();
						historyProcessGrid.add(DATA);
					}
				);
				
			}
		}
	};
	//instantiate the grid widget
	var monitoringGrid = new TaoGridClass('#monitoring-processes-grid', model, '', monitoringGridOptions);
	//load monitoring grid
	loadMonitoringGrid(null);

	/**
	 * Instantiate the details area
	 */
	//the grid model
	var currentActivitiesModel = model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']['subgrids'];
	//the current activities grid options
	
	 var currentActivitiesOptions = {
		'height' : $('#current_activities_container').height()-50,
		'width' 	: $('#process_history_container').width()-50,
		'title'  : __('Current activities')
	};
	//instantiate the grid widget
	var currentActivitiesGrid = new TaoGridClass('#current-activities-grid', currentActivitiesModel, '', currentActivitiesOptions);

	
	/**
	 * Instantiate the history area
	 */
	//the grid model
	var historyProcessModel = <?=$historyProcessModel?>;
	//the history grid options	
	 var historyProcessOptions = {
		'height' 	: $('#current_activities_container').height()-50,
		'width' 	: $('#process_history_container').width()-50,
		'title' 	: __('Process History')
	};
	//instantiate the grid widget
	var historyProcessGrid = new TaoGridClass('#history-process-grid', historyProcessModel, '', historyProcessOptions);
	
});
</script>
