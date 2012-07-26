<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo __("TAO - An Open and Versatile Computer-Based Assessment Platform"); ?></title>

		<script type="text/javascript" src="<?=BASE_WWW?>js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?=BASE_WWW?>js/jquery-ui-1.8.22.custom.min.js"></script>
		<script type="text/javascript" src="<?=BASE_WWW?>js/jquery.json.js"></script>
		<script type="text/javascript" src="<?=BASE_WWW?>js/wfEngine.js"></script>
		<script type="text/javascript" src="<?=BASE_WWW?>js/process_browser.js"></script>

		<script type="text/javascript">
			$(function(){
				var counter = 10;
				var t = null;
				var timer = null;
				timer = function(){
					$('#counter').text(counter);
					if(counter > 0){
						counter--;
						t = setTimeout(function(){
							timer();
						}, 1000);
					}
				}
				timer();
			});
		</script>

		<style media="screen">
			@import url(<?=BASE_WWW?>css/process_browser.css);
		</style>

		<meta http-equiv="refresh" content="10;url=<?php echo BASE_URL;?>/ProcessBrowser/index?processUri=<?=get_data('processExecutionUri')?>"/>

	</head>

	<body>
		<div id="loader"><img src="<?=BASE_WWW?>img/ajax-loader.gif" /> <?=__('Loading next activity...')?></div>
		<div id="process_view"></div>

		<ul id="control">

        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo $userViewData['username']; ?></span></span> <span class="separator"></span>
        	</li>

         	<li>
         		<a id="logout" class="action icon" href="<?php echo BASE_URL;?>/Authentication/logout"><?php echo __("Logout"); ?></a>
         	</li>

		</ul>

		<div id="content">
			<div id="business">
				<?=__("The activity provided in the url is no longer the up-to-date.")?><br/>
				<?=__("The link may be outdated.")?><br/><br/>

				<?=__("You will be redirected to the current activity if you are allowed to.")?><br/>
				<?=__("If there is no available activity or there are more than one. You will be redirected to the process main page.")?><br/><br/>

				<?=__("Redirection in ")?><span id="counter"></span> <?=__("seconds")?>.<br/>
				<a href="<?php echo BASE_URL;?>/ProcessBrowser/index?processUri=<?=get_data('processExecutionUri')?>"><?=__('Redirect immediately')?></a>
			</div>

			<br class="clear" />
  		</div>


		<div id="footer">
			TAO<sup>&reg;</sup> - <?=date('Y')?> - A joint initiative of CRP Henri Tudor and the University of Luxembourg
		</div>
	</body>
</html>