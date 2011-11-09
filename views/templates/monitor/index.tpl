<!-- <script type="text/javascript" src="<?=ROOT_URL?>/taoItems/models/ext/itemAuthoring/waterphenix/lib/wuib/wuib.min.js"></script> -->
<style> 
	#filter-container {height:100%;}
	.tabs-bottom { position: relative; } 
	.tabs-bottom .ui-tabs-panel { height:450px; overflow: auto; }
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
	<div id="tabs-4">tabs-4
	</div>
</div>

<div class="main-container" style="border:2px dashed gray">
	<table id="monitoring-processes-grid">
	</table>	
</div>

<script type="text/javascript">
$(function(){

	filterTabs = new TaoTabsClass('#filter-container');
	
	/*
	 * instantiate the filter nodes widget
	 */

	var getUrl = root_url + '/wfEngine/Monitor/getFilteredInstancesPropertiesValues';
	//the facet filter options
	var facetFilterOptions = {
		'template' : 'accordion',
		'callback' : {
			'onFilter' : function(filter, filterNodesOptions){
				console.log(filter, filterNodesOptions);
				//refreshResult(filter, filterNodesOptions);
				//console.log('yeah filter');
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

	//define jqgrid column
	var properties = [
	<?foreach($columns as $column):?>
		 '<?=$column->getTitle()?>',
	<?endforeach?>
		<?php //,__('Actions')?>
	];
	//properties = ['test'];

    <?foreach($columns as $column):?>
		 //console.log('<?= $column->getId()?>');
	<?endforeach;?>
	
	//define jqgrid model
	var model = [
    <?foreach($columns as $column):?>
		 {name:'<?=$column->getId()?>',index:'<?=$column->getId()?>'},
	<?endforeach;?>
		<?php //{name:'actions',index:'actions', align:"center", sortable: false}, ?>
	];

	//model = [{name:'test',index:'test'}];
	//instantiate jqgrid
	$("#monitoring-processes-grid").jqGrid({
		//url			:'http://tao.local/wfEngine/Monitor/monitorProcess',
	    datatype	: "json",
	    mtype		: 'GET',
		colNames	: properties, 
		colModel	: model, 
		//width		: parseInt($("#result-list").parent().width()) - 15, 
		//sortname	: 'id', 
		//sortorder	: "asc", 
		caption		: __("Monitoring Processes"),
		jsonReader: {
			repeatitems : false,
			id: "0"
		}
	});

	$.ajax();
});
</script>

	 <? //var_dump($processExecutions) ?>
	 <? //var_dump($data) ?>
	 