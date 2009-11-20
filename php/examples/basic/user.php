<?php
require "../../inc/floorplanner.php";

$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$act = isset($_GET["act"]) ? $_GET["act"] : "show";
$debug = isset($_GET["debug"]) ? $_GET["debug"] : 0;

$fp = new Floorplanner(API_URL, API_KEY);

if ($act == "delete" && $id > 0) {
	$fp->deleteUser($id);
	header("Location: users.php");
	die("");
} else if ($act == "save") {
	$user = array();
	foreach ($_GET as $key=>$val) {
		if ($key == "act") continue;
		$user[$key] = $val;
	}
	$fp->createUser($user);
	header("Location: users.php");
	die("");	
} else if ($act == "update") {
	$user = array();
	foreach ($_GET as $key=>$val) {
		if ($key == "act") continue;
		$user[$key] = $val;
	}
	$fp->updateUser($user);
	header("Location: users.php");
	die("");
}

$form = "";
$projects = NULL;

// retreive user info
$user = $id > 0 ? $fp->getUser($id) : NULL;

// retreive user's projects
$projects = $id > 0 ? $fp->getUserProjects($id) : NULL;
?>
<html>
	<head>
		<title>Floorplanner API - User</title>
		<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<?php
			if ($user) {
				print "<h3>user \"" . $user["username"] . "\"</h3>"; 
			}
			
			if ($act == "show") {
				// build menu
				print "<a href=\"user.php?act=new\">create user</a> | ";
				print "<a href=\"user.php?id={$id}&act=delete\">delete user</a> | ";
				if (isset($projects)) {
					print "<a href=\"projects.php?user_id={$id}\">user projects</a> | ";
				}
				print "<a href=\"project.php?user_id={$id}&act=new\">create user project</a> | ";
				print "<a href=\"users.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				
				// build a form for this user
				$form = $fp->buildForm($user, $fp->userFields);
				$form .= "<input type=\"hidden\" name=\"act\" value=\"update\"></input>";
				$form .= "<input type=\"submit\" value=\"save\"></input>";
			} else if ($act == "new") {
				// build menu
				print "<a href=\"users.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				
				// build a default form for a new user
				$form = $fp->buildForm(array(), $fp->userFields, false);
				$form .= "<input type=\"hidden\" name=\"act\" value=\"save\"></input>";
				$form .= "<input type=\"submit\" value=\"save\"></input>";
			} 
		?>
		<form action="user.php" method="get">
		<?=$form;?>
		</form>
		<?php
			
			if ($debug && $user) {
				print "<hr/><pre>" . var_export($user, 1) . "</pre>";
			}
		?>
	</body>
</html>
