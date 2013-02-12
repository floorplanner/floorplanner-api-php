<?php

require '../classes/Floorplanner.php';
require 'config.php';

$fp = Floorplanner::connect($config['api_key']);

$xml_file_path = $argv[1];
$xml = file_get_contents($xml_file_path);

$project = $fp->createProject($xml);
var_dump($project);
