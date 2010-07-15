<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
	<head>
		<title><?php echo __("PIAAC Background Questionnaire"); ?></title>
	
		<style media="screen">
			@import url(../../views/<?php echo $GLOBALS['dir_theme']; ?>css/process_authoring.css);
		</style>
		
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/process_authoring.js"></script>
	</head>
	<body>
		<ul id="control">
			<li>
				<span id="uiLanguages" class="icon"><?php echo __("Languages"); ?> :</span> 
				<?php foreach ($uiLanguages as $lg): ?>
				<a class="language internalLink" href="../../index.php/preferences/switchUiLanguage?lang=<?php echo $lg; ?>"><?php echo strtoupper(substr($lg,3)); ?></a> 
				<?php endforeach; ?> <span class="separator" />
			</li>
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User Id."); ?> <span id="username"><?php echo $userViewData['username']; ?></span></span> <span class="separator" />
        	</li>
         	<li>
         		<a class="action icon" id="home" href="../../index.php/main/index"><?php echo __("Home"); ?></a> <span class="separator" />
         	</li>
         	<li>
         		<a class="action icon" id="logout" href="../../index.php/authentication/logout"><?php echo __("Logout"); ?></a>
         	</li>
		</ul>
		
		<div id="content">
			<h1 id="authoring_title"><?php echo __("Interview initialization"); ?></h1>	
			
			<div id="business">
				<h2 id="authoring_subtitle"><?php echo __($processAuthoringData['processLabel']); ?></h2>
				<form id="authoring_form" action="../../index.php/processes/add" method="get" >
				<input type="hidden" name="posted[executionOf]" value="<?php echo urlencode($processAuthoringData['processUri']); ?>"/>
					
					<table id="authoring_table">
						<tbody>							
							<?php foreach ($caseIdViewData as $caseIdData): ?>	
								<tr>					
									<td id="" colspan="2">
										<input type="radio"  value="<?php echo GUIHelper::sanitizeGenerisString($caseIdData['id']); ?>" name="posted[caseId]" >  
											<?php echo GUIHelper::sanitizeGenerisString($caseIdData['id']); ?>
										</input>
									</td>
								</tr>					
							<?php endforeach;  ?>
						</tbody>
						<tfoot>
								<tr>
									<td id="authoring_submit" colspan="2">
										<?php if(!empty($caseIdViewData)) :?>
											<input id="submit_process" type="submit" name="posted[new]" value="<?php echo __("Import Interview"); ?>"/>
										<?php endif; ?>
										<input id="submit_process" type="submit" name="posted[new]" value="<?php echo __("Create New Interview"); ?>"/>
										
									</td>

								</tr>
						</tfoot>					
					</table>
				</form>

			</div>
			
		</div>
	</body>
</html>