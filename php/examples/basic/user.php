<?php
require "../../inc/floorplanner.php";

$uid = isset($_GET["uid"]) ? $_GET["uid"] : -1;
$act = isset($_GET["act"]) ? $_GET["act"] : "show";

$fp = new Floorplanner(API_URL, API_KEY);

if ($act == "delete" && $uid > 0) {
	$fp->deleteUser($uid);
	header("Location: users.php");
	die("");
} else if ($act == "save") {
	$user = new FloorplannerUser(NULL);
	foreach ($_GET as $key=>$val) {
		if ($key == "act") continue;
		$user->data[$key] = $val;
	}
	$fp->createUser($user);
	header("Location: users.php");
	die("");	
} else if ($act == "update") {
	$user = new FloorplannerUser(NULL);
	foreach ($_GET as $key=>$val) {
		if ($key == "act") continue;
		$user->data[$key] = $val;
	}
	$fp->updateUsers($project);
	header("Location: users.php");
	die("");
}

$user = $uid > 0 ? $fp->getUser($uid) : NULL;
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
				print "<h3>user \"" . $user->username . "\"</h3>"; 
			}
			
			if ($act == "show") {
				print "<a href=\"user.php?act=new\">create user</a> | ";
				print "<a href=\"user.php?uid={$uid}&act=delete\">delete user</a> | ";
				print "<a href=\"users.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$form = $user->buildForm();
			} else if ($act == "new") {
				print "<a href=\"users.php\">back</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$user = new FloorplannerUser(NULL);
				$form = $user->buildForm();
				$form .= "<input type=\"hidden\" name=\"act\" value=\"save\"></input>";
			} else if ($act == "delete") {

			}
		?>
		<form action="user.php" method="get">
		<?=$form;?>
		</form>
	</body>
</html>
