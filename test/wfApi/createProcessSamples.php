<?php

require_once 'ProcessSampleCreator.php';



$create = isset($_REQUEST['create'])?(bool)$_REQUEST['create']:false;
$clean = isset($_REQUEST['clean'])?(bool)$_REQUEST['clean']:false;

$output = array();

if($create){
	
	$processFactory = new ProcessSampleCreator();
	$processSequence = $processFactory->createSimpleSequenceProcess();
	
	$output[] = $processSequence->uriResource;
	
}else if($clean){
	$output['succes'] = ProcessSampleCreator::clean();
}

echo json_encode($output);
?>
