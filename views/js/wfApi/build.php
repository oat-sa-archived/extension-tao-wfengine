<?php 
include_once("../../../../tao/lib/jstools/minify.php");

$files = array ();
$files[] = "./src/constants.js";
$files[] = "./src/context.js";
$files[] = "./src/api.js";
$files[] = "./src/wfApi.js";
$files[] = "./src/ProcessExecution.js";
$files[] = "./src/ActivityExecution.js";
$files[] = "./src/Variable.js";
$files[] = "./src/RecoveryContext.js";

minifyJSFiles($files, "wfApi.min.js");

exit(0);
?>
