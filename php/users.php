<?php
require "inc/floorplanner.php";

$fp = new Floorplanner(API_URL, API_KEY);

$page = 1;
$per_page = 100;

$users = $fp->getUsers();
?>
<html>
	<head>
		<title>Floorplanner API - Users</title>
	</head>
	<body>
		<div>
			<a href="user.php?act=new">create user</a> | <a href="index.php">home</a>
		</div>
		
		<table>
		<?php
			if (count($users)) {
				foreach($users as $user) {
					
				}
			} else {
				print "<tr><td>No users found.</td></tr>";
			}
		?>
		</table>
	</body>
</html>