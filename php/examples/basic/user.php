<?php
require "../../inc/floorplanner.php";

$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$act = isset($_GET["act"]) ? $_GET["act"] : "show";

$fp = new Floorplanner(API_URL, API_KEY);

if ($act == "delete" && $id > 0) {
	$user = $fp->getUser($id);
	$fp->deleteUser($user);
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

$user = $id > 0 ? $fp->getUser($id) : NULL;
$form = "";
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
				print "<a href=\"user.php?act=new\">create user</a> | ";
				print "<a href=\"user.php?id={$id}&act=delete\">delete user</a> | ";
				print "<a href=\"users.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$form = $fp->buildForm($user, $fp->userFields);
				$form .= "<input type=\"hidden\" name=\"act\" value=\"update\"></input>";
				$form .= "<input type=\"submit\" value=\"save\"></input>";
			} else if ($act == "new") {
				print "<a href=\"users.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$form = $fp->buildForm(array(), $fp->userFields, false);
				$form .= "<input type=\"hidden\" name=\"act\" value=\"save\"></input>";
				$form .= "<input type=\"submit\" value=\"save\"></input>";
			} 
		?>
		<form action="user.php" method="get">
		<?=$form;?>
		</form>
	</body>
</html>
