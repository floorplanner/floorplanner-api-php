#!/usr/bin/php
<?php


# include the Floorplanner API classes
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../classes' );
require_once 'Floorplanner.php';

# load the API key from the config file
require_once './config.php';

if (count($argv) <= 1) {
	try {
		print "Trying to get a list of users with an API only account...\n";
		$tokenless_fp = Floorplanner::connect($config['api_key']);
		$tokenless_fp->getAccounts();
		print "It succeeded. This should not happen!!!";
	} catch (Floorplanner_Exception $fe) {
		print "It failed miserably, as expected.\n";
		print "With an API only account, you WILL need an authentication token.\n\n";
		
		print "Please provide a user token as the first argument to this script.\n";
	}
} else {
	# A user token must be provided as first argument
	print "Trying to get a list of projects that are accessible with the given token...\n";
	
	# Connect to the Floorplanner server with the provided auth token
	$fp = Floorplanner::connectWithToken($config['api_key'], $argv[1]);

	# Now, get all the projects of the user associated with the token.
	$projects = $fp->getProjects();
	foreach ($projects as $project) {
		print $project->id . ': ' . $project->name . "\n";
	}

	print "\nTotal projects found: " . count($projects) . "\n";
}
?>