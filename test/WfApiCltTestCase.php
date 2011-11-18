<?php

require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';
define ('PATH_SAMPLE', dirname(__FILE__).'/samples/');
?>

<!DOCTYPE html>
<html>
<head>
	<title>QUnit Test Suite</title>
	<link rel="stylesheet" href="../../tao/test/qunit/qunit.css" type="text/css" media="screen">
	<!--<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>-->
	<script type="application/javascript" src='../../tao/views/js/jquery-1.4.2.min.js'></script>
    <script type="application/javascript" src="../../tao/test/qunit/qunit.js"></script>
    
    <script type="application/javascript" src="../views/js/wfApi/src/constants.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/context.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/api.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/wfApi.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/ProcessExecution.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/ActivityExecution.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/Variable.js"></script>
    <script type="application/javascript" src="../views/js/wfApi/src/RecoveryContext.js"></script>

	<!-- -------------------------------------------------------------------------
	QTI DATA
	--------------------------------------------------------------------------->
	
	<script type="application/javascript">
        var testToRun = '*';
        //var testToRun = "Remote Parsing / Client Matching : Select Point";
        
        var testUnitFct = test;
        var asynctestUnitFct = asyncTest;
        test = function (label, func) {
            if (testToRun == "*"){
                testUnitFct (label, func);
            } else if (testToRun == label){
                testUnitFct (label, func);
            }
        }
        asyncTest = function (label, func) {
            if (testToRun == "*"){
                asynctestUnitFct (label, func);
            } else if (testToRun == label){
                asynctestUnitFct (label, func);
            }
        }

		test("Test the VariableFactory (integer)", function() {
			console.log('sync test');
		});
        
        // REMOTE MATCHING CHOICE.XML
        asyncTest ('Remote Matching : Choice.xml', function () {
            wfApi.request(wfApi.ProcessExecutionControler, '');
        });

	</script>
	
</head>
<body>
	<h1 id="qunit-header">QUnit Test Suite</h1>
	<h2 id="qunit-banner"></h2>
	<div id="qunit-testrunner-toolbar"></div>
	<h2 id="qunit-userAgent"></h2>
	<ol id="qunit-tests"></ol>
	<div id="qunit-fixture">test markup</div>
</body>
</html>
