<?php
require "../../inc/floorplanner.php";

$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : -1;

$fp = new Floorplanner(API_URL, API_KEY);

$page = 1;
$per_page = 100;

$projects = $user_id > 0 ? $fp->getUserProjects($user_id) : $fp->getProjects();
?>
<html>
	<head>
		<title>Floorplanner API - Projects</title>
		<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<h3>projects</h3>
		<div>
			<a href="project.php?user_id=<?=$user_id;?>&act=new">create project</a> | <a href="index.php">home</a>
			<hr />
		</div>
		<table>
		<?php
			if ($projects && count($projects)) {
				foreach($projects as $project) {
					print "<tr><td><a href=\"project.php?id={$project['id']}\">" . $project["name"] . " " . 
						$project["user-id"]. "</td></tr>";
				}
			} else {
				print "<tr><td>No projects found.</td></tr>";
			}
		?>
		</table>
	</body>
</html>