<div id="user-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Assign User to role')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="user-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-user" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(document).ready(function(){
	if(ctx_extension){
		url = root_url + '/' + ctx_extension + '/' + ctx_module + '/';
	}
	getUrl = url + 'getUsers';
	setUrl = url + 'saveUsers';
	new GenerisTreeFormClass('#user-tree', getUrl, {
		actionId: 'user',
		saveUrl : setUrl,
		checkedNodes : <?=get_data('users')?>
	});
});
</script>