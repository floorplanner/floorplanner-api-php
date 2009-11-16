<?php
require "inc/floorplanner.php";

$pid = isset($_GET["pid"]) ? $_GET["pid"] : -1;
$act = isset($_GET["act"]) ? $_GET["act"] : "show";

$fp = new Floorplanner(API_URL, API_KEY);

if ($act == "delete" && $pid > 0) {
	$fp->deleteProject($pid);
	header("Location: projects.php");
	die("");
}

$project = $pid > 0 ? $fp->getProject($pid) : NULL;
$token = "";
if ($project) {
	$token = $fp->getToken($project->user_id);
}
$form = "";
?>
<html>
	<head>
		<title>Floorplanner API - Project</title>
	</head>
	<body>
		<?php
			if ($act == "show") {
				print "<a href=\"project.php?pid={$pid}&act=delete\">delete project</a> | ";
				print "<a href=\"projects.php\">cancel</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$form = $project->buildForm();
				$form .= "<pre>" . htmlentities($project->toXml()) . "</pre>";
			} else if ($act == "new") {
				print "<a href=\"project.php?act=save\">save project</a> | ";
				print "<a href=\"projects.php\">cancel</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$project = new FloorplannerProject(NULL);
				$form = $project->buildForm();
				
			} else if ($act == "delete") {

			}
		?>
		<?=$form;?>
	</body>
</html>
