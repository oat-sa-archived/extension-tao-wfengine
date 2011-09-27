<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once dirname(__FILE__). '/../tao/includes/class.Bootstrap.php';

//use a different session name when we execute a wf process
$modules = array(
	'Authentication', 
	'Main', 
	'ProcessBrowser',
	'ProcessInstanciation',
	'ItemDelivery', 
	'ResultDelivery',
	'RecoveryContext'
);
$options = array();
foreach($modules as $module){
	if(tao_helpers_Request::contains('module', $module)){
		$options['session_name'] = 'TAO_WORKFLOW_SESSION';
		break;
	}
}

$bootStrap = new BootStrap('wfEngine', $options);
$bootStrap->start();
$bootStrap->dispatch();
?>