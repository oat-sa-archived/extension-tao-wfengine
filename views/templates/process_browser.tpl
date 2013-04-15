<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>

		<script type="text/javascript" src="<?=TAOBASE_WWW?>js/jquery-1.8.0.min.js"></script>
		<script type="text/javascript" src="<?=TAOBASE_WWW?>js/jquery-ui-1.8.23.custom.min.js"></script>
		<script type="text/javascript" src="<?=BASE_WWW?>js/jquery.json.js"></script>

		<script type="text/javascript">
			window.processUri = '<?=urlencode($browserViewData['processUri'])?>';
			window.activityUri = '<?=urlencode($activity->uriResource)?>';
			window.activeResources = <?=$browserViewData['active_Resource']?>;
			window.activityExecutionUri = '<?=urlencode($browserViewData['activityExecutionUri'])?>';
			//window.processExecutionUri = '<?=urlencode($browserViewData['processUri'])?>';

			function goToPage(page_str){
				$("#loader").css('display', 'block');
				$("#tools").empty();
				window.location.href = page_str;
			}

		    $(document).ready(function (){

		    	$("#loader").css('display', 'none');

		       // Back and next function bindings for the ProcessBrowser.
		       $("#back").click(function(){
					goToPage('<?=BASE_URL?>ProcessBrowser/back?processUri=<?=urlencode($browserViewData['processUri'])?>&activityUri=<?=urlencode($browserViewData['activityExecutionUri'])?>&nc=<?=$browserViewData['activityExecutionNonce']?>');
					$(this).unbind('click');
					$("#next").unbind('click');
				});


			   $("#next").click(function(){
			       	goToPage('<?=BASE_URL?>ProcessBrowser/next?processUri=<?=urlencode($browserViewData['processUri'])?>&activityUri=<?=urlencode($browserViewData['activityExecutionUri'])?>&nc=<?=$browserViewData['activityExecutionNonce']?>');
			       	$(this).unbind('click');
			       	$("#back").unbind('click');
				});

				//buil services iframes
				<?foreach($services as $service):?>
				var $aFrame = $('<iframe class="toolframe" frameborder="0" style="<?=$service['style']?>" src="<?=BASE_URL?>ProcessBrowser/loading"></iframe>').appendTo('#tools');
				$aFrame.unbind('load').load(function(){
					$(this).attr('src', "<?=$service['callUrl']?>");
					$(this).unbind('load');
				});
				<?endforeach;?>

			   <?if(get_data('debugWidget')):?>

				$("#debug").click(function(){
					$("#debugWindow").toggle('slow');
				});

				<?endif?>
		    });

		</script>

		<style media="screen">
			@import url(<?=BASE_WWW?>css/process_browser.css);
		</style>

	</head>

	<body>
		<div id="loader"><img src="<?=BASE_WWW?>img/ajax-loader.gif" /> <?=__('Loading next activity...')?></div>
		<div id="process_view"></div>

		<ul id="control">


        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo $userViewData['username']; ?></span></span> <span class="separator"></span>
        	</li>


         	<li>
         		<a id="pause" class="action icon" href="<?=_url('pause', 'ProcessBrowser', null, array('processUri' => $browserViewData['processUri']))?>"><?php echo __("Pause"); ?></a> <span class="separator"></span>
         	</li>


         <?if(get_data('debugWidget')):?>
			<li>
				<a id="debug" class="action icon" href="#">Debug</a> <span class="separator"></span>
			</li>
        <?endif?>


         	<li>
         		<a id="logout" class="action icon" href="<?=_url('logout', 'Authentication')?>"><?php echo __("Logout"); ?></a>
         	</li>

		</ul>

		  <?if(get_data('debugWidget')):?>
				<div id="debugWindow" style="display:none;">
					<?foreach(get_data('debugData') as $debugSection => $debugObj):?>
					<fieldset>
						<legend><?=$debugSection?></legend>
						<pre>
							<?print_r($debugObj);?>
						</pre>
					</fieldset>
					<?endforeach?>
				</div>
		  <?endif?>

		<div id="content">
			<div id="business">

				<div id="navigation">
					<?if (USE_PREVIOUS): ?>
						<?if($browserViewData['controls']['backward']):?>
							<input type="button" id="back" value="<?= __("Back")?>"/>
						<?else:?>
							<input type="button" id="back" value="" style="display:none;"/>
						<?endif?>
					<?endif?>

					<?if ($browserViewData['controls']['forward']): ?>
						<input type="button" id="next" value="<?= __("Forward")?>"/>
					<?else:?>
						<input type="button" id="next" value="" style="display:none;"/>
					<?endif?>
				</div>



				<div id="tools">
				</div>

			</div>

			<br class="clear" />
  		</div>
		<? include TAO_TPL_PATH .'footer/layout_footer_'.TAO_RELEASE_STATUS.'.tpl' ?>