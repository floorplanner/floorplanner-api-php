<?php

# include the Floorplanner API classes
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../classes' );
require_once 'Floorplanner.php';

# load the API key from the config file
require_once './config.php';

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