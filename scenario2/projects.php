<?php

require '../classes/Floorplanner.php';
require 'config.php';

session_start();
if (empty($_SESSION['fp_token'])) header('Location: index.php'); 

$fp = Floorplanner::connectWithToken($config['api_key'], $_SESSION['fp_token']);
$projects = $fp->getProjects();
?>

<p> Logging in using token: <?=$fp->token?> </p>
<h2> <?=count($projects)?> projects</h2>
<ul>
<?php foreach ($projects as $project) :?>
	<li><a href="project.php?id=<?=$project->id?>"><?=htmlspecialchars($project->name)?></a></li>
<?php endforeach; ?>
</ul>