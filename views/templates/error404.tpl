<?php
header("HTTP/1.0 503 Internal Error");
?>
<html>
<head>
	<title>503 Internal Error</title>
</head>
<body>
	<p><?php echo __("An error occured during request processing") ?>.</p>
</body>
</html>