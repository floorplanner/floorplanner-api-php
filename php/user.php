<?php
require "inc/floorplanner.php";

$uid = isset($_GET["uid"]) ? $_GET["uid"] : -1;
$act = isset($_GET["act"]) ? $_GET["act"] : "show";

$fp = new Floorplanner(API_URL, API_KEY);
$user = $uid > 0 ? $fp->getUser($uid) : NULL;
$form = "";
?>
<html>
	<head>
		<title>Floorplanner API - User</title>
	</head>
	<body>
		<?php
			if ($act == "show") {
				print "<a href=\"user.php?act=new\">create user</a> | ";
				print "<a href=\"user.php?uid=<?=$uid;?>&act=delete\">delete user</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$form = $user->buildForm();
			} else if ($act == "new") {
				print "<a href=\"user.php?act=save\">save user</a> | ";
				print "<a href=\"users.php\">cancel</a> | ";
				print "<a href=\"index.php\">home</a>";
				print "<hr />";
				$user = new FloorplannerUser(NULL);
				$form = $user->buildForm();
			} else if ($act == "delete") {

			}
		?>
		<?=$form;?>
	</body>
</html>
