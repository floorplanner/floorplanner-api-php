<?php

# include the Floorplanner API classes
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../classes' );
require_once 'Floorplanner.php';

# load the API key from the config file
require_once './config.php';

session_start();
if (empty($_SESSION['fp_token'])) header('Location: index.php'); 

$fp = Floorplanner::connectWithToken($config['api_key'], $_SESSION['fp_token']);
$project = $fp->getProject($_GET['id']);
?>
<html>
<head>
	<title><?=htmlspecialchars($project->name)?></title>
	<script type="text/javascript" src="http://beta.floorplanner.com/javascripts/prototype.js"></script>	

	<?= $fp->javascriptIncludes(); ?>

	<script type="text/javascript">
	<!--
		
		var fp = new Floorplanner('<?=$project->hash()?>', {config: Floorplanner.STATE_EDIT, auth_token: '<?=$fp->token?>'});
		window.onload = function() {
			fp.embed('floorplanner');
		};
		
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
</head>
<body>
<h2><?=htmlspecialchars($project->name)?></h2>
<p><?=htmlspecialchars($project->description)?></p>
<div id="floorplanner" style="width: 800px; height: 600px;">Hoi Floorplanner!</div>
<ul id="floors"></ul>

<button onclick="fp.showForm('SAVE')">Save</button>
</body>
</html>