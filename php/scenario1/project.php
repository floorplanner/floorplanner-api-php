<?php

require '../classes/Floorplanner.php';
require 'config.php';
session_start();

if (empty($_SESSION['account_token'])) {
	header('Location: index.php');
}

$fp = Floorplanner::connect($config['api_key']);
$project  = $fp->getProject($_GET['project_id']);

if (empty($_SESSION['account_id']) || $_SESSION['account_id'] != $_GET['account_id']) {
	// Cache authentication tokens to spare our servers
	$_SESSION['account_id'] = $_GET['account_id'];
	$_SESSION['account_token'] = $project->getToken();
}

?>
<html>
<head>
	<title> Floorplanner integration </title>
	<?= $fp->javascriptIncludes(); ?>
</head>
<body>
<h1> Project: <?=htmlspecialchars($project->name)?> </h1>
<div id="floorplanner" style="width: 900px; height: 600px;">
	Floorplanner will load here
</div>

<script type="text/javascript">
<!--
<?= $project->embedScript('floorplanner', $_SESSION['account_token']); ?>
-->
</script>

</body>
</html>
	
