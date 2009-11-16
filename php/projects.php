<?php
require "inc/floorplanner.php";

$fp = new Floorplanner(API_URL, API_KEY);

$page = 1;
$per_page = 100;

$projects = $fp->getProjects();
?>
<html>
	<head>
		<title>Floorplanner API - Projects</title>
	</head>
	<body>
		<div>
			<a href="#">create project</a> | <a href="index.php">home</a>
			<hr />
		</div>
		
		<table>
		<?php
			if (count($projects)) {
				foreach($projects as $project) {
					print "<tr><td><a href=\"project.php?pid={$project->id}\">" . $project->name . "</td></tr>";
				}
			} else {
				print "<tr><td>No projects found.</td></tr>";
			}
		?>
		</table>
	</body>
</html>