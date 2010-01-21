<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
	<head>
		<title><?php echo PROCESS_BROWSER_TITLE; ?></title>
		
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquery.ui.js"></script>
		<script type="text/javascript" src="../../js/jquery.json.js"></script>	
		<script type="text/javascript" src="../../js/jquery.ui.taoqualDialog.js"></script>
		<script type="text/javascript" src="../../js/wfEngine.js"></script>
		<script type="text/javascript" src="../../js/process_browser.js"></script>
		<script type="text/javascript" src="../../index.php/I18n/translations"></script>
		<script type="text/javascript" src="../../../../api/javascript/keyboard.js"></script>
		<script type="text/javascript" src="../../../../api/javascript/jquery.json.js"></script>	
		<script type="text/javascript" src="../../../../../piaac/JS/piaac_event_logger.js"></script>
		<script type="text/javascript">
			window.processUri = '<?php echo urlencode($processUri); ?>';
			window.intervieweeUri = '<?php echo urlencode($hyperViewData[1]['hyper_object']); ?>';
			window.hyperObject = '<?php echo urlencode($hyperViewData[0]['hyper_object']); ?>';
			window.activityUri = '<?php echo urlencode($activity->uri); ?>';
			window.activeResources = <?php echo $browserViewData['active_Resource']; ?>;
			window.uiLanguage = '<?php echo $browserViewData['uiLanguage']; ?>';
			<?php if ($browserViewData['isBreakOffable']): ?>
			window.breakable = true;
			<?php else: ?>
			window.breakable = false;
			<?php endif; ?>
			
			
			function goToPage(page_str){
				window.location.href = page_str;
		    }
		
		    $(document).ready(function (){

		    	<?php if (USE_KEYBOARD) : ?>
				// If keyboard is enabled, we build the JS Code needed
				// to use it. The GUIHelper will build an array of
				// objects containing key combinations relevant.
				shortcuts = <?php echo GUIHelper::buildKeyboardShortcuts($GLOBALS['lang']); ?>;
				<?php endif; ?>
				
				// Bind logs.
				bindGUILogs();
				
				
		    	
		       // Back and next function bindings for the ProcessBrowser.
		       $("#back").click(function(){
		       		if (!freezeGUI && !isHyperViewInTransaction())
		       		{
		       			// Log the event.
		       			$('#xul_question').get(0).contentWindow.submitHyperView(function() {
		       				$('#xul_question').get(0).contentWindow.freezeGUI();
		       			
		       				$('#xul_question').get(0).contentWindow.logFTEValidation();
			       			logBusinessEvent('MOVE_BACKWARD', getCurrentItemId(), 'Moved backward', undefined, function() {
			       				goToPage('../../../../../taoqual/plugins/UserFrontend/index.php/processBrowser/back?processUri=<?php echo urlencode($processUri); ?>');
		       					freezeGUI = true;
			       			});
		       			});
		       		}
		       	});
		       
		       if ($('#back_floating').length)
		       {
		       		$('#back_floating').click(function(){
			       		if (!freezeGUI && !isHyperViewInTransaction())
			       		{
			       			$('#xul_question').get(0).contentWindow.submitHyperView(function() {
			       				$('#xul_question').get(0).contentWindow.freezeGUI();
			       				
			       				// Log the event.
			       				$('#xul_question').get(0).contentWindow.logFTEValidation();
			       				logBusinessEvent('MOVE_BACKWARD', getCurrentItemId(), 'Moved backward', undefined, function() {
			       					goToPage('../../../../../taoqual/plugins/UserFrontend/index.php/processBrowser/back?processUri=<?php echo urlencode($processUri); ?>');
		       						freezeGUI = true;
			       				});
		       				});
			       		}
			       	});
		       }	
		       	
			   $("#next").click(function(){
			   		if (!this.freezeGUI  && $('#xul_question').get(0).contentWindow.isFinished() && !isHyperViewInTransaction())
			   		{
			   			$('#xul_question').get(0).contentWindow.submitHyperView(function () {
			   				$('#xul_question').get(0).contentWindow.freezeGUI();
			   				
			   				// Log the event.
			   				$('#xul_question').get(0).contentWindow.logFTEValidation();
			       			logBusinessEvent('MOVE_FORWARD', getCurrentItemId(), 'Moved forward', undefined, function() {
			       				goToPage('../../../../../taoqual/plugins/UserFrontend/index.php/processBrowser/next?processUri=<?php echo urlencode($processUri); ?>');
			   					freezeGUI = true;
			       			});
			   			});
			   		}
			   	});
			   	
			   if ($('#next_floating').length)
		       {
		       		$('#next_floating').click(function(){
			       		if (!freezeGUI && $('#xul_question').get(0).contentWindow.isFinished() && !isHyperViewInTransaction())
			       		{
			       			$('#xul_question').get(0).contentWindow.submitHyperView(function () {
			       				$('#xul_question').get(0).contentWindow.freezeGUI();
			       				
			       				// Log the event.
			       				$('#xul_question').get(0).contentWindow.logFTEValidation();
			       				logBusinessEvent('MOVE_FORWARD', getCurrentItemId(), 'Moved forward', undefined, function() {
			       					goToPage('../../../../../taoqual/plugins/UserFrontend/index.php/processBrowser/next?processUri=<?php echo urlencode($processUri); ?>');
			   						freezeGUI = true;
			       				});
			   				});
			       		}
			       	});
		       }	
		
		
			   // Next and back functions triggered from a tool avoiding HTTP loops (Search more information about this issue).
			   goNextFromService = function() {
			   		if (<?php echo (!$browserViewData['isNextable']) ? 'false' : 'true'; ?>)
			   		{

				   			// Log the event.
				   			$('#xul_question').get(0).contentWindow.freezeGUI();
				       		logBusinessEvent('MOVE_FORWARD', getCurrentItemId(), 'Moved forward', undefined, function() {
				       			goToPage('../../../../../taoqual/plugins/UserFrontend/index.php/processBrowser/next?processUri=<?php echo urlencode($processUri); ?>');
				       		});

			   		}
			   };
			   
			   goBackFromService = function() {
			   		if (<?php echo (!$browserViewData['isBackable']) ? 'false' : 'true'; ?>) 
			   		{ 

			   				// Log the event.
			   				$('#xul_question').get(0).contentWindow.freezeGUI();
			       			logBusinessEvent('MOVE_BACKWARD', getCurrentItemId(), 'Moved backward', undefined, function() {
			       				goToPage('../../../../../taoqual/plugins/UserFrontend/index.php/processBrowser/back?processUri=<?php echo urlencode($processUri); ?>');
			       			});
			   		}
			   
			   };

			   window.addEventListener('click', mouseclickHandler, true);	  
		    });
		    
		    <?php if ($browserViewData['isHyperView']): ?>
		    // ! HyperViews only !
		    // The browser tries to register to some events fired by the services.
		    $(window).load(function(e){			
					
					// Registering to the onSuccess event of the hyperviews
					registerToServiceEvent(function() {submitActivity('../../../../../taoqual/plugins/UserFrontend/index.php/processBrowser/next?processUri=<?php echo urlencode($processUri); ?>')}, 
										   'onSuccess',
										   'xul_question');
					
					// Registering to the onKeypress event of the hyperviews
					registerToServiceEvent(keyboardHandler, 'onKeypress', 'xul_question');
					
					// Registering to the onHelppress event of the hyperviews.
					registerToServiceEvent(helpHandler, 'onHelppress', 'xul_question');
					
					// Registering to onRangeError event of the hyperviews.
					registerToServiceEvent(openRangeErrorDialog, 'onRangeError', 'xul_question');
					
					// Registering to onEvent event of the hyperviews (for logs purpose)
					registerToServiceEvent(logBusinessEvent, 'onEvent', 'xul_question');
					
					// Registering to onError event of the hyperviews.
					registerToServiceEvent(openErrorDialog, 'onError', 'xul_question');
					
					// tools height.
					$('#xul_question').css('height', getToolHeight('#xul_question') + 'px');
					$('#lifeEventCalendar').css('height', getToolHeight('#lifeEventCalendar') + 'px');
					
					// Insert shortcuts list in tool
					insertShortcutsInTool(1);
					
					// Execute service.afterLoad
					$('#xul_question').get(0).contentWindow.afterLoad();
			});
			
			$(window).load(function(){
			   adjustFloatingButtons();
			
			   <?php if (!$consistencyViewData['isConsistent']): 
			   // Consistency checking.
			   ?>
			   openConsistencyDialog('<?php echo $consistencyViewData['processExecutionUri']; ?>', 
			   						 '<?php echo $consistencyViewData['activityUri']; ?>', 
			   						 <?php echo GUIHelper::buildActivitiesList($consistencyViewData['involvedActivities']); ?>, 
			   						 '<?php echo addslashes($consistencyViewData['notification']); ?>',  
			   						 <?php echo ($consistencyViewData['suppressable']) ? 'true' : 'false'; ?>);
			   <?php endif; ?>
			});
			
			<?php endif; ?>
		</script>
		
		<style media="screen">
			@import url(../../views/<?php echo $GLOBALS['dir_theme']; ?>css/process_browser.css);
		</style>

	</head>
	
	<body>
		<div id="process_view"></div>
		<ul id="control">
			<?php if (ENABLE_UI_LANGUAGES): ?>
			<li>
				<span id="uiLanguages" class="icon"></span> 
				<?php foreach ($browserViewData['uiLanguages'] as $lg): ?>
				<a class="language internalLink" href="../../index.php/preferences/switchUiLanguage?lang=<?php echo str_replace("EN_EN","EN",$lg); ?>&from=<?php echo urlencode($browserViewData['processUri']); ?>"><?php echo I18nUtil::extractLgLabelFromLgCode($lg); ?></a> 
				<?php endforeach; ?> <span class="separator" />
			</li>
			<?php endif; ?>
			
			<?php if (ENABLE_CONTENT_LANGUAGES): ?>
			<li >
				<span id="activityContentLanguages" class="icon"><?php echo __("Languages"); ?> </span>: 
				<?php foreach ($browserViewData['activityContentLanguages'] as $lg): ?>
				<a id="toggle_language" class="language internalLink" href="../../index.php/preferences/switchServiceContentLanguage?lang=<?php echo $lg; ?>&from=<?php echo urlencode($browserViewData['processUri']); ?>"><?php echo I18nUtil::extractLgLabelFromLgCode($lg); ?></a> 
				<?php endforeach; ?> <span class="separator" /> 
			</li>    
			<?php endif; ?>
        	<li>
        		<span id="connecteduser" class="icon"><?php echo __("User Id."); ?> <span id="username"><?php echo $userViewData['username']; ?></span></span> <span class="separator" />
        	</li>
         	
         	<?php if (ENABLE_PAUSE_BUTTON): ?>
         	<li>
         		<a id="pause" class="action icon" href="../../index.php/processBrowser/pause?processUri=<?php echo urlencode($browserViewData['processUri']); ?>"><?php echo __("Pause"); ?></a> <span class="separator" />
         	</li>
         	<?php endif; ?>
         	
			<?php if ($browserViewData['isBreakOffable'] && ENABLE_BREAKOFF_BUTTON): ?>
			<li>
         		<a id="breakOff" class="action icon" href="../../index.php/processBrowser/breakOff?processUri=<?php echo urlencode($browserViewData['processUri']); ?>"><?php echo __("Breakoff"); ?></a> <span class="separator" />
         	</li>
        	<?php endif; ?>
        	
        	<?php if (ENABLE_EXPORT_BUTTON): ?>
			<li>
         		<a class="action icon" id="export" href="#" onclick="document.body.style.cursor = 'wait';jQuery.get('../../../../../piaac/Exchange/Export/index.php','',function (data, textStatus) { alert('<?php echo __("All cases exported !");?>');document.body.style.cursor = 'default';});" ><?php echo __("Export"); ?></a> <span class="separator" />
         	</li>
         	<?php endif; ?>
         	
         	<?php if (ENABLE_LOGOUT_BUTTON): ?>
         	<li>
         		<a id="logout" class="action icon" href="../../index.php/authentication/logout"><?php echo __("Logout"); ?></a>
         	</li>
         	<?php endif; ?>
		</ul>

		<div id="content">
			<div id="business">
				<ul id="toolbox" <?php if (!ENABLE_TOOLBOX): ?>class="nodisplay" <?php endif; ?>>
					<li>
						<!-- Remark tool suppressed for field.  
						<a id="remarkAction" href="#" onclick="javascript:annotate(classification,resources,window.activeResources)"><?php echo __("Write a remark (F4)"); ?> </a>
						-->
					</li>
					<li>
						<a id="calendarAction" href="#" onclick="javascript:showCalendar();"><?php echo __("View calendar (F7)"); ?> </a>
					</li>
				</ul>
			
				<div id="navigation">
					<?php if ($browserViewData['isBackable'] && USE_PREVIOUS): ?>
					<input type="button" id="back" value="<?php echo __("Back"); ?>"/>
					<?php endif; ?>
										
					<input type="button" id="next" style="<?php if (!$browserViewData['isNextable']) echo 'display:none;' ?>" <?php echo ($browserViewData['forceNext']) ? '' : 'disabled="disabled"';  ?>" value="<?php echo __("Forward"); ?>"/>
				</div>
			
				<?php if (ENABLE_FLOATING_NAVIGATION): ?>
				<div id="navigation_floating">
					<?php if ($browserViewData['isBackable'] && USE_PREVIOUS): ?>
						<input type="button" id="back_floating" value="&lt;&lt;"/>
					<?php endif; ?>
					<input type="button" id="next_floating" style="<?php if (!$browserViewData['isNextable']) echo 'display:none;' ?>" <?php echo ($browserViewData['forceNext']) ? '' : 'disabled="disabled"';  ?>" value="&gt;&gt;"/>
				</div>
				<?php endif; ?>
				
				<script>
				var classification = <?php echo GUIHelper::jsonIfY($browserViewData['annotationsClassificationsJsArray']);?>;
				var resources = <?php echo GUIHelper::jsonIfY($browserViewData['annotationsResourcesJsArray']);?>;
				</script>
			
				<div id="tools">
					<?php if ($browserViewData['isHyperView']): ?>
					
						<?php if ($variablesViewData['Var_Interviewee']): ?>
							<?php $i=1; ?>
							<iframe frameborder="0" id="xul_question" src="../../../../../generis/core/view/generis_HyperView.php?hclUri=<?php echo urlencode($hyperViewData[$i]['hyper_class']); ?>&hcoUri=<?php echo urlencode($hyperViewData[$i]['hyper_object']); ?>&language=<?php echo GUIHelper::buildContentLanguage($browserViewData['contentlanguage']); ?>&executionContext=wfengine&processExecution=<?php echo urlencode($browserViewData['processUri']); ?>"></iframe>	
							<?php //endfor; ?>
						<?php endif; ?>
						
					<?php endif; ?>
				</div>

			</div>
			
			<br class="clear" />
  		</div>
 
  		<div id="help" title="<?php echo __("Question help"); ?>"></div>
  		<div id="consistency" title="<?php echo ((!$consistencyViewData['isConsistent']) ? $consistencyViewData['source'] : '') . ' ' . __("Edit error"); ?>"></div>
		<div id="annotationBox" title="<?php echo __("Write a remark"); ?>"></div>
		<div id="ranges" title="<?php echo __("Value range error"); ?>"></div>
		<div id="errors" title="<?php echo __("Edit error"); ?>"></div>
		<div id="calendar" title="<?php echo __("Calendar"); ?>"></div>
		<div id="watch" title="<?php echo __("Watch"); ?>"></div>
		<div id="testing" title="<?php echo __("Testing"); ?>"></div>
	</body>

</html>