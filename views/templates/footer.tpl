<script type="text/javascript">
var ctx_extension 	= "<?=get_data('extension')?>";
var ctx_module 		= "<?=get_data('module')?>";
var ctx_action 		= "<?=get_data('action')?>";
$(function(){
	
	<?if(get_data('uri') && get_data('classUri')):?>
		updateTabUrl(UiBootstrap.tabs, 'process_authoring', "<?=_url('authoring', 'Process', 'wfEngine', array('uri' => get_data('uri'), 'classUri' => get_data('classUri') ))?>");
	<?else:?>
		UiBootstrap.tabs.tabs('disable', getTabIndexByName('process_authoring'));
	<?endif?>
	
	<?if(get_data('reload')):?>
		uiBootstrap.initTrees();
	<?endif?>

});
</script>