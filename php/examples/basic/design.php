<?php
require "../../inc/floorplanner.php";

$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$token = isset($_GET["token"]) ? $_GET["token"] : NULL;
$edit = isset($_GET["edit"]) ? $_GET["edit"] : 0;

$fp = new Floorplanner(API_URL, API_KEY);

$design = $fp->getDesign($id);

$project = $fp->getProject($design["project-id"]);
//die("<pre>" . var_export($design) . "</pre>");
?>
<html>
	<head>
		<title>Floorplanner API - Design</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"> </script>
		<script type="text/javascript" src="<?=API_URL;?>javascripts/floorplanner/floorplanner.js"> </script>
		<script type="text/javascript">
			var token = "<?=$token;?>";
			var projectId = <?=$project["id"];?>;
			var editMode = <?=$edit;?>;
			
			function embedFloorplanner() {
				var fpParams = {project_id:projectId, token:token};
				if (editMode > 0) {
					fpParams.state = "edit";
				}
				var fp = new Floorplanner(fpParams);
				fp.embed("floorplannnerView");
			}
		</script>
	</head>
	<body onload="embedFloorplanner()">
	
		<div id="floorplannnerView"></div>
		
	</body>
</html>
