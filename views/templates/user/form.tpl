<?if(get_data('exit')):?>
	<script type="text/javascript">
		// window.location = "<?=_url('index', 'Users', 'wfEngine', array('message' => get_data('message')))?>";
		//redirect to user page:
		$("#list_users").click();
	</script>
<?else:?>
	<?if(get_data('message')):?>
		<div id="info-box" class="ui-widget-header ui-corner-all auto-slide">
			<span><?=get_data('message')?></span>
		</div>
	<?endif?>
	<div class="main-container">
	
		<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
			<?=get_data('formTitle')?>
		</div>
		<div id="form-container" class="ui-widget-content ui-corner-bottom">
			<?=get_data('myForm')?>
		</div>
	</div>
	<br />
	<script type="text/javascript">
	var ctx_extension 	= "<?=get_data('extension')?>";
	var ctx_module 		= "<?=get_data('module')?>";
	var ctx_action 		= "<?=get_data('action')?>";
	$(document).ready(function(){
	
		<?if(get_data('action') == 'add'):?>
			UiBootstrap.tabs.tabs('disable', getTabIndexByName('edit_user'));

			if($("input[id='<?=get_data('loginUri')?>']")){
				$("input[id='<?=get_data('loginUri')?>']").blur(function(){
					var elt = $(this);
					value = elt.val().replace(' ', '');
					if(value == ''){
						$('.login-info').remove();
					}
					else{
						$.postJson(
							"<?=_url('checkLogin', 'Users')?>",
							{login: value},
							function(data){
								$('.login-info').remove();
								if(data.available){
									elt.after("<span class='login-info'><img src='<?=BASE_WWW?>img/tick.png' /></span>");
								}
								else{
									elt.after("<span class='login-info ui-state-error'><img src='<?=BASE_WWW?>img/exclamation.png' class='icon' /><?=__('Login not available')?></span>");
								}
							}
						);
					}
				});
			}
			
		<?endif?>
		
		
	
		
	});
	</script>
<?endif?>