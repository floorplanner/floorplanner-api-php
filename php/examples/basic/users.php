<?php
require "../../inc/floorplanner.php";

$fp = new Floorplanner(API_URL, API_KEY);

$debug = isset($_GET["debug"]) ? $_GET["debug"] : 0;

$page = 1;
$per_page = 100;

$users = $fp->getUsers();

?>
<html>
	<head>
		<title>Floorplanner API - Users</title>
		<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div>
			<a href="user.php?act=new">create user</a> | <a href="index.php">home</a>
			<hr />
		</div>
		
		<table>
		<?php
			if ($users && count($users) > 0) {
				foreach($users as $user) {
					$name = $user["username"];
					$name = strlen($name) ? $name : "No Username?!";
					print "<tr><td><a href=\"user.php?id={$user["id"]}\">{$name}</td></tr>";
				}
			} else {
				print "<div style=\"color:red\">No users found.</div>";
			}
		?>
		</table>
		<?php
			if ($debug) {
				print "<pre>" . var_export($users, 1);
				print "\nresponse headers:\n\n";
				var_export($fp->responseHeaders);
				print "</pre>";
			}
		?>
	</body>
</html>