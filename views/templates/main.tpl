<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo __("WorkflowEngine Process Browser "); ?></title>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="<?echo BASE_WWW; ?>/js/wfEngine.js"/></script>
		<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>/css/custom-theme/jquery-ui-1.8.custom.css" />
		<style media="screen">
			@import url(<?echo BASE_WWW; ?>/css/main.css);
		</style>
	</head>
	
	<body>
		<div id="process_view"></div>
		
		<ul id="control">
			


			    
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User name:"); ?> <span id="username"><?php echo $userViewData['username']; ?></span> </span><span class="separator" />
        	</li>
        	
         	
         	<li>
         		<a class="action icon" id="logout" href="<?php echo BASE_URL;?>/Authentication/logout"><?php echo __("Logout"); ?></a>
         	</li>
		</ul>
		
		<div id="content" class='ui-corner-bottom'>
			<h1 id="welcome_message"><img src="<?=BASE_WWW?>/img/wf_engine_logo.png" /><?php echo __("Welcome to TAO Process Engine"); ?></h1>	
			<div id="business">
				<h2 class="section_title"><?php echo __("Active Process"); ?></h2>
				<table id="active_processes">
					<thead>
						<tr>
							<th><?php echo __("Status"); ?></th>
							<th><?php echo __("Process"); ?></th>
							<th><?php echo __("Start/Resume the case"); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($processViewData as $procData): ?>
						<tr>
							<td class="status"><img src="<?php echo BASE_WWW;?>/<?php echo GUIHelper::buildStatusImageURI($procData['status']); ?>"/></td>
							
							
							<td class="label"><?php echo GUIHelper::sanitizeGenerisString($procData['label']); ?></td>
			
							<td class="join">
								<?php if ($procData['status'] != 'Finished'): ?>
									<?php foreach ($procData['activities'] as $activity): ?>
										<?php if ($activity['may_participate']): ?>
											<a href="<?php echo BASE_URL;?>/ProcessBrowser/index?processUri=<?php echo urlencode($procData['uri']);?>&activityUri=<?php echo urlencode($activity['uri']);?>"><?php echo $activity['label']; ?></a>
										<?php elseif (!$activity['allowed'] || $activity['activityEnded']): ?>
											<span class="activity-denied"><?php echo $activity['label']; ?></span>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php else: ?>
									<span><?php echo __("Finished Process"); ?></span>
								<?php endif; ?>
							</td>
							<!--<td class="situation"><a href="#"><img onclick="openProcess('../../../WorkFlowEngine/index.php?do=processInstance&param1=<?php echo urlencode($procData['uri']); ?>')" src="<?php echo BASE_WWW;?>/<?php echo $GLOBALS['dir_theme']; ?>img/open_process_view.png"/></a></td>-->
						</tr>
						<?php endforeach;  ?>
					</tbody>
				</table>
				<!-- End of Active Processes -->
				
				
				<h2 class="section_title"><?php echo __("Initialize new Process"); ?></h2>
				<div id="new_process">
					<?php foreach($availableProcessDefinition as $procDef) : ?>
						<li>
							<a href="<?php echo BASE_URL;?>/Processes/authoring?processDefinitionUri=<?php echo urlencode($procDef->uriResource); ?>">
							<?php echo GUIHelper::sanitizeGenerisString($procDef->getLabel()); ?></a>
						</li>
					<?php endforeach;  ?>
				</div>	
				
					
				<h2 class="section_title"><?php echo __("My roles"); ?></h2>
				<ul id="roles">
					<?php foreach ($userViewData['roles'] as $role): ?>
						<li><?php echo $role['label']; ?></li>
					<?php endforeach; ?>
		
				</ul>
				<!-- End of Roles -->
				</div>
			
		</div>
		<!-- End of content -->
		<div id="footer">
			TAO<sup>&reg;</sup> - <?=date('Y')?> - A joint initiative of CRP Henri Tudor and the University of Luxembourg
		</div>
	</body>
</html>