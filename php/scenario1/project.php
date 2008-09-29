<?php

require '../classes/Floorplanner.php';
require 'config.php';
session_start();

if (empty($_SESSION['user_token'])) {
	header('Location: index.php');
}

$fp = Floorplanner::connect($config['api_key']);
$project  = $fp->getProject($_GET['project_id']);

if (empty($_SESSION['user_id']) || $_SESSION['user_id'] != $_GET['user_id']) {
	// Cache authentication tokens to spare our servers
	$_SESSION['user_id'] = $_GET['user_id'];
	$_SESSION['user_token'] = $project->getToken();
}

?>
<html>
<head>
	<title> Floorplanner integration </title>
	<?= $fp->javascriptIncludes(); ?>
	<script type="text/javascript" src="http://beta.floorplanner.com/javascripts/prototype.js"></script>
</head>
<body>
<h1> Project: <?=htmlspecialchars($project->name)?> </h1>
<div id="floorplanner" style="width: 900px; height: 600px;">
	Floorplanner will load here
</div>
<ul id="floors"></ul>

<script type="text/javascript">
<!--
<?= $project->embedScript('floorplanner', $_SESSION['user_token']); ?>

fp.observe('LOADED', function() {
	$('floors').innerHTML = '';
	var p = fp.getProject();
	p.floors.each(function(floor) {
		floorLink = new Element('a', {href: '#'}).update(floor.name);
		floorLink.observe('click', function() {
			fp.get2dMovie().loadFloor(floor.id);
			return false;
		});
		var floorItem = new Element('li').update(floorLink);
		var designList = new Element('ul');
		floorItem.insert(designList);
		floor.designs.each(function(design) { 
			designLink = new Element('a', {href: '#'}).update(design.name);
			designLink.observe('click', function() {
				fp.loadDesign(design.id);
			});
			designList.insert(new Element('li').update(designLink));
		});
		$('floors').insert(floorItem);
	});
});
-->
</script>

</body>
</html>
	
