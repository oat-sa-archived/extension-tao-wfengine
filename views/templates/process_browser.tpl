<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=__("TAO - An Open and Versatile Computer-Based Assessment Platform")?></title>
                <link rel="stylesheet" type="text/css" href="<?=Template::css('custom-theme/jquery-ui-1.8.22.custom.css', 'tao')?>" media="screen" />
                <link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>css/process_browser.css" media="screen" />
		
                <script type="text/javascript" src="<?= Template::js('lib/require.js', 'tao')?>" ></script>
                <script type="text/javascript">
                (function(){
                    require(['<?=get_data('client_config_url')?>'], function(){

                        require(['jquery', 'wfEngine/controller/processBrowser', 'serviceApi/ServiceApi', 'serviceApi/StateStorage', 'serviceApi/UserInfoService','wfEngine/wfApi/wfApi.min'], 
                            function($, ProcessBrowser, ServiceApi, StateStorage, UserInfoService){
                            
                            var services = [
                            <?php foreach($services as $i => $service):?>
                            {
                               frameId  : 'tool-frame-<?=$i?>',
                               api      : <?=$service['api']?>, 
                               style    : <?=json_encode($service['style'])?>
                            } <?=($i < count($services) - 1) ? ',' : '' ?>
                            <?php endforeach;?>  
                            ];
                            
                            ProcessBrowser.start({
                                activityExecutionUri    : '<?=get_data('activityExecutionUri')?>',
                                processUri              : '<?=get_data('processUri')?>',
                                activityExecutionNonce  : '<?=get_data('activityExecutionNonce')?>',
                                services                : services
                            });

                        });
                    });
                }());
                </script>
	</head>

	<body>
  <div class="content-wrap">
		<div id="runner">
		<div id="process_view"></div>
        <?php if(!tao_helpers_Context::check('STANDALONE_MODE') && !has_data('allowControl') || get_data('allowControl')):?>
			<ul id="control">
	        	<li>
	        		<span id="connecteduser" class="icon"><?=__("User name:")?> <span id="username"><?=$userViewData['username']?></span></span>
	        		<span class="separator"></span>
	        	</li>
	
	
	         	<li>
	         		<a id="pause" class="action icon" href="<?= _url('pause', 'ProcessBrowser', null, array('processUri' => $browserViewData['processUri']))?>"><?=__("Pause")?></a> <span class="separator"></span>
	         	</li>
	
	         	<?php if(get_data('debugWidget')):?>
				<li>
					<a id="debug" class="action icon" href="#">Debug</a> <span class="separator"></span>
				</li>
	        	<?php endif?>
	
	         	<li>
	         		<a id="logout" class="action icon" href="<?=_url('logout', 'Main')?>"><?=__("Logout")?></a>
	         	</li>
	
			</ul>
			
			<?php if(get_data('debugWidget')):?>
					<div id="debugWindow" style="display:none;">
						<?php foreach(get_data('debugData') as $debugSection => $debugObj):?>
						<fieldset>
							<legend><?=$debugSection?></legend>
							<pre>
								<?print_r($debugObj)?>
							</pre>
						</fieldset>
						<?php endforeach?>
					</div>
			<?php endif?>
		<?php endif?>

		<div id="content">
			<div id="business">
				<div id="navigation">
					<?php if($browserViewData['controls']['backward']):?>
						<input type="button" id="back" value="<?= __("Back")?>"/>
					<?php else:?>
						<input type="button" id="back" value="" style="display:none;"/>
					<?php endif?>

					<?php if($browserViewData['controls']['forward']): ?>
						<input type="button" id="next" value="<?= __("Forward")?>"/>
					<?php else:?>
						<input type="button" id="next" value="" style="display:none;"/>
					<?php endif?>
				</div>

				<div id="tools">
                                    <?php foreach($services as $i => $service):?>
                                    <iframe id="tool-frame-<?=$i?>" class="toolframe" frameborder="0" scrolling="no" ></iframe>
                                    <?php endforeach;?>  
				</div>
			</div>
			<br class="clear" />
  		</div>
	</div>
    </div>
<!-- /content-wrap -->
<?php
if (!tao_helpers_Context::check('STANDALONE_MODE')) {
    Template::inc('footer.tpl', 'tao');
}
?>
</body>
</html>
