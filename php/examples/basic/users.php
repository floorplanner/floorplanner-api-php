<?php
require "../../inc/floorplanner.php";

$fp = new Floorplanner(API_URL, API_KEY);

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
					print "<tr><td><a href=\"user.php?uid={$user->id}\">" . $user->username . "</td></tr>";
				}
			} else {
				print "<tr><td>No users found.</td></tr>";
			}
		?>
		</table>
	</body>
</html>