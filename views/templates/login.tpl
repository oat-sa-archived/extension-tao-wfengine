<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
	<head>
		<title><?php echo PROCESS_BROWSER_TITLE; ?></title>
		
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>/<?php echo $GLOBALS['dir_theme']; ?>/css/login.css);
		</style>
	</head>
	
	<body>
		<ul id="control">    
			<li></li>
		</ul>
		<div id="content">
			<div id="business">
				<form id="login_form" method="post" action=".<?php echo BASE_URL;?>/authentication/login">
					<?php if ($indexViewData['route']): ?>
					<input name="route" type="hidden" value="true"/>
					<input name="from" type="hidden" value="<?php echo $indexViewData['from']; ?>"/>
					<input name="fromQuery" type="hidden" value="<?php echo $indexViewData['fromQuery']; ?>"/>
					<?php endif; ?>
					
					<div class="item">
						<label for="login"><?php echo __("User Id.") ?> :</label>
						<input type="text" class="input" id="login" name="in_login" maxlength="20"/>
					</div>
					
					<div class="item">
						<label for="password"><?php echo __("Password"); ?> :</label>
						<input type="password" class="input" id="password" name="in_password" maxlength="8"/>
					</div>
					
					<div class="validation">
						<input type="submit" class="confirm" name="form_login_connect_button" value="<?php echo __("Login"); ?>"/>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>