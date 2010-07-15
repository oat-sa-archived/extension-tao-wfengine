<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
	<head>
		<title><?php echo __("Workflow Engine"); ?></title>
	
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>/css/process_authoring.css);
		</style>
		
		<script type="text/javascript" src="<?echo BASE_WWW; ?>/js/jquery.js"></script>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>/js/process_authoring.js"></script>
	</head>
	<body>
		<ul id="control">
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User Id."); ?> <span id="username"><?php echo $userViewData['username']; ?></span> </span><span class="separator" />
        	</li>
         	<li>
         		<a class="action icon" id="home" href="<?php echo BASE_URL;?>/main/index"><?php echo __("Home"); ?></a> <span class="separator" />
         	</li>
         	<li>
         		<a class="action icon" id="logout" href="<?php echo BASE_URL;?>/authentication/logout"><?php echo __("Logout"); ?></a>
         	</li>
		</ul>
		
		<div id="content">
			<h1 id="authoring_title"><?php echo __("Process initialization"); ?></h1>	
			
			<div id="business">
				<h2 id="authoring_subtitle"><?php echo $processAuthoringData['processLabel']; ?></h2>
				<form id="authoring_form" action="<?php echo BASE_URL;?>/processes/add" method="post" >
				<input type="hidden" name="posted[executionOf]" value="<?php echo urlencode($processAuthoringData['processUri']); ?>"/>
					<table id="authoring_table">
						<tbody>
							<?php foreach ($processAuthoringData['variables'] as $var): ?>
							<tr>
								<td class="variable_name"><?php echo $var['name']; ?> :</td>
								<td><input type="text" size="50" name="posted[variables][<?php echo $var['key']; ?>]" value=""/></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<td id="authoring_submit" colspan="2"><input id="submit_process" type="submit" name="posted[new]" value="<?php echo __("Launch Process"); ?>"/></td>
							</tr>
						</tfoot>
					</table>
					
				</form>
			</div>
			
		</div>
	</body>
</html>