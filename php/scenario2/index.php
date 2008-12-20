<?php

# include the Floorplanner API classes
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../classes' );
require_once 'Floorplanner.php';

# load the API key from the config file
require_once './config.php';

session_start();

if (empty($_GET['token'])) { ?>
	
	<p><a href="http://www.floorplanner.com/authorize/<?=$config['authorization_id']?>/additional_info_123">Log in</a> with your Floorplanner account</p>

<?php } else {
	
	$_SESSION['additional_info'] = $_GET['info'];  // Will be copied from the URL above: additional_info_123
	$_SESSION['fp_token']        = $_GET['token']; // Store this in the user's session.
	header('Location: projects.php');
	
}

?>