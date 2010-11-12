<?php 
include_once("../../../../tao/lib/jstools/minify.php");

$files = array ();
$files[] = "./src/constants.js";
$files[] = "./src/context.js";
$files[] = "./src/api.js";

minifyJSFiles ($files, "wfApi.min.js");

exit(0);
?>
