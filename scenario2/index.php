<?php

require '../classes/Floorplanner.php';
require 'config.php';

session_start();

if (empty($_GET['token'])) { ?>
	
	<p><a href="http://beta.floorplanner.com/authorize/<?=$config['authorization_id']?>/additional_info_123">Log in</a> with your Floorplanner account</p>

<?php } else {
	
	$_SESSION['additional_info'] = $_GET['info'];  // Will be copied from the URL above: additional_info_123
	$_SESSION['fp_token']        = $_GET['token']; // Store this in the user's session.
	header('Location: projects.php');
	
}

?>