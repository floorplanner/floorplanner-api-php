<?php

require '../classes/Floorplanner.php';
require 'config.php';

$fp = Floorplanner::connect($config['api_key']);

$project_id = $argv[1];

$project = $fp->getProject($project_id);

$renderResponse = $project->render(400, 600);

var_dump($renderResponse);