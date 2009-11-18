<?php
require "../../inc/floorplanner.php";

$id = isset($_GET["id"]) ? $_GET["id"] : -1;
$project_id = isset($_GET["project_id"]) ? $_GET["project_id"] : -1;
$floor_id = isset($_GET["floor_id"]) ? $_GET["floor_id"] : -1;
$token = isset($_GET["token"]) ? $_GET["token"] : NULL;
$edit = isset($_GET["edit"]) ? $_GET["edit"] : 0;
$act = isset($_GET["act"]) ? $_GET["act"] : "show";

$fp = new Floorplanner(API_URL, API_KEY);

if ($act == "create_design") {
	$design = array();
	$design["name"] = "New Design";
	$design["project-id"] = $_GET["project_id"];
	$design["floor-id"] = $_GET["floor_id"];
	$fp->createDesign($design);
	header("Location: project.php?id=" . $design["project-id"]);
	die("");
}

if ($id >= 0) {
	$design = $fp->getDesign($id);
	$project_id = $design["project-id"];
} else if ($project_id >= 0) {
	
}

//die("<pre>" . htmlentities($fp->responseXml) . "\n\n" . var_export($design, 1) . "</pre>");
?>
<html>
	<head>
		<title>Floorplanner API - Design</title>
		<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"> </script>
		<script type="text/javascript" src="<?=API_URL;?>javascripts/floorplanner/floorplanner.js"> </script>
		<script type="text/javascript">
			var token = "<?=$token;?>";
			var designId = <?=$id;?>;
			var projectId = <?=$project_id;?>;
			var floorId = <?=$floor_id;?>;
			var editMode = <?=$edit;?>;
			var fp;
			
			function embedFloorplanner() {
				var fpParams = {project_id:projectId, token:token};
				if (designId >= 0) {
					fpParams.design_id = designId;
				}
				if (editMode > 0) {
					fpParams.state = "edit";
				}
				fp = new Floorplanner(fpParams);
				
				fp.observe('LOADED', function( pArgument ) {
					if (floorId > 0) {
						fp.loadFloor(floorId);
					}
				});
				
				fp.embed("floorplannnerView");
			}
			
			function test() {	
				try {
					fp.showForm("ADD_FLOOR");
				} catch(e) {
					alert(e);
				}
			}
			
		</script>
	</head>
	
	<body onload="embedFloorplanner()">
		<div>
			<h3>design</h3>
			<a href="project.php?id=<?=$project_id;?>">back</a>
			<?php
				if ($edit > 0) {
			?>
			<a href="javascript:fp.showForm('ADD_FLOOR')">add floor</a>
			<a href="javascript:fp.showForm('SAVE')">save design</a>
			<?php
			}
			?>
			<hr />
		</div>
		<div id="floorplannnerView"></div>
		
	</body>
</html>
