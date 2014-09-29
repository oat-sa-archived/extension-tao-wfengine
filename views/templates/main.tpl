<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=__("WorkflowEngine Process Browser ")?></title>
		<script type="text/javascript" src="<?=Template::js('lib/jquery-1.8.0.min.js', 'tao')?>"></script>
		<link rel="stylesheet" type="text/css" href="<?=Template::css('custom-theme/jquery-ui-1.8.22.custom.css', 'tao')?>" />
		<style media="screen">
			@import url(<?=BASE_WWW?>/css/main.css);
		</style>
	</head>

	<body>
  <div class="content-wrap">
		<div id="process_view"></div>
		<ul id="control">
        	<li>
        		<span id="connecteduser" class="icon"><?=__("User name:")?> <span id="username"><?=$userViewData['username']?></span> </span><span class="separator"></span>
        	</li>
         	<li>
         		<a class="action icon" id="logout" href="<?= _url('logout', 'Authentication') ?>"><?=__("Logout")?></a>
         	</li>
		</ul>

		<div id="content" class='ui-corner-bottom'>
			<h1 id="welcome_message"><img src="<?=BASE_WWW?>/img/wf_engine_logo.png" /><?=__("Welcome to TAO Process Engine")?></h1>
			<div id="business">
				<h2 class="section_title"><?=__("Active Process")?></h2>
				<table id="active_processes">
					<thead>
						<tr>
							<th><?=__("Status")?></th>
							<th><?=__("Processes")?></th>
							<th><?=__("Start/Resume the case")?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($processViewData as $procData): ?>
						<tr>
							<td class="status"><img src="<?=BASE_WWW?>/<?=wfEngine_helpers_GUIHelper::buildStatusImageURI($procData['status'])?>"/></td>


							<td class="label"><?=wfEngine_helpers_GUIHelper::sanitizeGenerisString($procData['label'])?></td>

							<td class="join">
								<?php if($procData['status'] != 'Finished'): ?>
									<?php foreach ($procData['activities'] as $activity): ?>
                                        <?php if($activity['may_participate']):?>
											<a href="<?= _url('index', 'ProcessBrowser', null, array('processUri' => $procData['uri'], 'activityUri' => $activity['uri'])) ?>"><?=$activity['label']?></a>
										<?php elseif (!$activity['allowed'] && !$activity['finished']):?>
											<span class="activity-denied"><?=$activity['label']?></span>
                                        <?php elseif ( $activity['finished']):?>
                                             <span class=""><?=__("Process Finished")?></span>
										<?php endif;?>
									<?php endforeach;?>
								<?php else:?>
									<span><?=__("Finished Process");?></span>
								<?php endif;?>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<!-- End of Active Processes -->


				<h2 class="section_title"><?=__("Initialize new Process")?></h2>
				<div id="new_process">
					<?php foreach($availableProcessDefinition as $procDef):?>
						<li>
							<a href="<?=_url('authoring', 'ProcessInstanciation', null, array('processDefinitionUri' => $procDef->getUri()))?>">
							<?=wfEngine_helpers_GUIHelper::sanitizeGenerisString($procDef->getLabel())?></a>
						</li>
					<?php endforeach; ?>
				</div>


				<h2 class="section_title"><?=__("My roles")?></h2>
				<ul id="roles">
					<?php foreach ($userViewData['roles'] as $role):?>
						<li><?=$role['label']?></li>
					<?php endforeach;?>

				</ul>
				<!-- End of Roles -->
				</div>

		</div>
		<!-- End of content -->
		</div>
<!-- /content-wrap -->
<?php
Template::inc('layout_footer.tpl', 'tao')
?>
</body>
</html>