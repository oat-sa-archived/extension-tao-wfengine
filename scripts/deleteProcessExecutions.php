<?
/*
two parameters accepted via GET:
- processExecutionUri	: the url encoded uri of the process execution resource to be deleted (encoded with the static method 'tao_helpers_Uri::encode'). 
						'*' or 'all' targets ALL process executions in the ontology (default: empty)
- finishedOnly			: if set to true, removes the targeted executions only if they are in the 'finished' state (default: false)

exemples:
To delete all process intance resources
http://localhost/wfEngine/scripts/deleteProcessExecutions.php?processExecutionUri=*

To delete a single instance, which must be finished:
http://localhost/wfEngine/scripts/deleteProcessExecutions.php?processExecutionUri=http%3A%2F%2Flocalhost%2Fmytao__rdf%23i1302606160013308900&finishedOnly=1
*/
require_once dirname(__FILE__).'/../includes/raw_start.php';

core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS, DATABASE_NAME);

$processExecutionUri = '';
if(isset($_GET['processExecutionUri'])){
	$processExecutionUri = tao_helpers_Uri::decode($_GET['processExecutionUri']);
}

$finishedOnly = false;
if(isset($_GET['finishedOnly'])){
	if($_GET['finishedOnly'] != 'false'){
		$finishedOnly = (bool) $_GET['finishedOnly'];
	}
}

$deleteDeliveryHistory = false;
if(isset($_GET['deliveryHistory'])){
	if($_GET['deliveryHistory'] != 'false'){
		$finishedOnly = (bool) $_GET['deliveryHistory'];
	}
}

$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
$result = false;
if(!empty($processExecutionUri)){
	if($processExecutionUri=='all' || $processExecutionUri='*'){
		$result = $processExecutionService->deleteProcessExecutions(array(), $finishedOnly);
                
                if($deleteDeliveryHistory){
                        //delete all delivery history:
                        $deliveryHistoryClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAODelivery.rdf#History');
                        foreach($deliveryHistoryClass->getInstances() as $history){
                                $history->delete();
                        }
                }
	}else{
		$processExecution = new core_kernel_classes_Resource($processExecutionUri);
		$result = $processExecutionService->deleteProcessExecution($processExecution, $finishedOnly);
	}
}


if($result === true){
	echo 'deletion completed';
}else{
	echo 'deletion failed';
}
?>