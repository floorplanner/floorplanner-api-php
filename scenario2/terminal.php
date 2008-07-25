#!/usr/bin/php
<?php

require '../classes/Floorplanner.php';
header('Content-Type: text/plain');

$webservice = "http://willems-laptop:3000";
$api_key    = '78fad6f12798eb6e646bbb10255ea96974081d29';

if (count($argv) <= 1) die("Please provide a user token as argument");

$fp = Floorplanner::connectWithToken($api_key, $argv[1], $webservice);

$projects = $fp->getProjects();
foreach ($projects as $project) {
	print $project->id . ': ' . $project->name . "\n";
}

print "\nTotal projects found: " . count($projects) . "\n";

?>