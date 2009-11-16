<?php
include "inc/floorplanner.php";

$fp = new Floorplanner(API_URL, API_KEY);
$users = $fp->getUsers();

if (count($users)) {
	$user = $users[0];
	print_r ($user->name);
} else {
	print "no users";
}

?>