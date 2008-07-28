#!/usr/bin/php
<?php

# include the Floorplanner API classes
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../classes' );
require_once 'Floorplanner.php';

# load the API key from the config file
require_once './config.php';

# Connect to the floorplanner server
$fp = Floorplanner::connect($config['api_key']);

# Note: this function does not communicate with the Floorplanner server.
# It will only set the right parameters for API calls that will be done
# later  by calling function on the $fp-object. It will therefore never
# fail.

# Now, lets do something useful.
# Get a list of all the users accessible with the current API key.
$users = $fp->getUsers();
foreach ($users as $user) {
	print $user->email . " (#{$user->id})\n";
}

# get an authentication token for the last user, so we can impersonate him.
$token = $user->getToken();
print "\nToken for user #{$user->id}: {$token}\n\n";

# Now, connect to the Floorplanner server with an authentication token. 
# This eassentially means that you will be logged in as the user the token 
# is generated for. Note that the API key is still required.
$user_fp = Floorplanner::connectWithToken($config['api_key'], $token);

# Get a list of all the projects accessible with the current API key
$projects = $user_fp->getProjects();
foreach ($projects as $project) {
	print $project->name . " (#{$project->id})\n";
}

# get an authentication token for the last project, in case we want to 
# open it in the Flash application.
print "\nToken for project #{$project->id}: " .  $project->getToken() . "\n\n";

?>