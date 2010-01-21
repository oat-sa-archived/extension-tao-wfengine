<?php 
header('Location: '.$services[0]->getCallUrl($variablesViewData).'');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
	<head>
		<title><?php echo PROCESS_BROWSER_TITLE; ?></title>
	
		<style media="screen">
			@import url(../../views/<?php echo $GLOBALS['dir_theme']; ?>css/process_browser_full_screen.css);
		</style>
		
		<script type="text/javascript" src="../../js/jquery.js"/></script>
		<script type="text/javascript" src="../../js/process_browser.js"/></script>
	</head>
	<body>
		<iframe frameborder="0" id="tools" src="<?php echo $services[0]->getCallUrl($variablesViewData); ?>"/></iframe>
	</body>
</html>