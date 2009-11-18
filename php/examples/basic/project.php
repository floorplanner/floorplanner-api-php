<?php
require "../../inc/floorplanner.php";

$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$act = isset($_GET["act"]) ? $_GET["act"] : "show";

$fp = new Floorplanner(API_URL, API_KEY);

if ($act == "delete" && $id > 0) {
	$fp->deleteProject($id);
	header("Location: projects.php");
	die("");
} else if ($act == "save") {
	$project = array();
	foreach ($_GET as $key=>$val) {
		if ($key == "act") continue;
		$project[$key] = $val;
	}
	$fp->createProject($project);
	header("Location: projects.php");
	die("");
} else if ($act == "update") {
	$project = array();
	foreach ($_GET as $key=>$val) {
		if ($key == "act") continue;
		$project[$key] = $val;
	}
	$fp->updateProject($project);
	header("Location: projects.php");
	die("");
}

$project = $id > 0 ? $fp->getProject($id) : NULL;
$token = "";
if ($project) {
	$token = $fp->getToken($project["user-id"]);
}
$form = "";
?>
<html>
	<head>
		<title>Floorplanner API - Project</title>
		<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<?php
			if ($project) {
				print "<h3>project \"" . $project["name"] . "\"</h3>"; 
			}
			
			if ($act == "show") {
				print "<a href=\"project.php?id={$id}&act=delete\">delete project</a> | ";
				print "<a href=\"project.php?id={$id}&act=edit\">edit project</a> | ";
				
				print "<a href=\"projects.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
			} else if ($act == "new") {
				print "<a href=\"projects.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$form = $fp->buildForm(array(), $fp->projectFields, false);
				$form .= "<input type=\"hidden\" name=\"act\" value=\"save\"></input>";
				$form .= "<input type=\"submit\" value=\"save\"></input>";
			} else if ($act == "edit") {
				print "<a href=\"projects.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$form = $fp->buildForm($project, $fp->projectFields);
				$form .= "<input type=\"hidden\" name=\"act\" value=\"update\"></input>";
				$form .= "<input type=\"hidden\" name=\"id\" value=\"{$project["id"]}\"></input>";
				$form .= "<input type=\"submit\" value=\"save\"></input>";
			}
		?>
		<form action="project.php" method="get">
		<?=$form;?>
		</form>
		
		<?php
			if ($project && array_key_exists("floors", $project)) {
				print "<ol>";
				foreach($project["floors"] as $floor) {
					print "<li>" . $floor["name"];
					if (count($floor["designs"])) {
						print "<ul>";
						foreach ($floor["designs"] as $design) {
							$viewUrl = "design.php?id={$design["id"]}&token={$token}";
							$editUrl = $viewUrl . "&edit=1";
							print "<li>" . $design["name"] . " ";
							print "<a href=\"{$viewUrl}\">view</a> | ";
							print "<a href=\"{$editUrl}\">edit</a>";
							print "</li>";
						}
						print "</ul>";
					} 
					print "</li>";
				}
				print "</ol>";
			}
		?>
	</body>
</html>
