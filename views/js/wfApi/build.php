<?php 
include_once("../../../../tao/lib/jstools/jsmin.php");

$files = array ();
$files[] = "./src/constants.js";
$files[] = "./src/context.js";
$files[] = "./src/api.js";

minify_files ($files, "wfApi.min.js");

exit(0);
?>
