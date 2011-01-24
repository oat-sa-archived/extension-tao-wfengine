<?php
/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 */
require_once dirname(__FILE__).'/../includes/raw_start.php';

set_time_limit(0);

//connec to the api
core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS, DATABASE_NAME);
		
$notificationService = tao_models_classes_ServiceFactory::get("wfEngine_models_classes_NotificationService");
$sent = $notificationService->sendNotifications(new tao_helpers_transfert_MailAdapter());

print "\n$sent sent notifications\n";
?>
