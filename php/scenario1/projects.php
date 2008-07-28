<?php

require '../classes/Floorplanner.php';
require 'config.php';

session_start();

$fp = Floorplanner::connect($config['api_key']);

$page = (empty($_GET['page'])) ? 1 : intval($_GET['page']);
if ($page < 1) $page = 1;

$user  = $fp->getUser($_GET['user_id']);
$projects = $user->getProjects();
?>

<h1> User info </h1>

<p> E-mail: <a href="mailto:<?=htmlspecialchars($user->email)?>"><?=htmlspecialchars($user->email)?></a></p>
	

<h2> Projects </h2>
<ul>
<?php foreach ($projects as $project) : ?>
	<li><a href="project.php?project_id=<?=$project->id?>"><?=$project->name?></a></li>	
<?php endforeach; ?>
</ul>