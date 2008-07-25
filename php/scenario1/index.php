<?php

require '../classes/Floorplanner.php';
require 'config.php';

session_start();
$fp = Floorplanner::connect($config['api_key']);

$page = (empty($_GET['page'])) ? 1 : intval($_GET['page']);
if ($page < 1) $page = 1;

$accounts = $fp->getAccounts($page, 30);
?>

<h1> Accounts </h1>
<ul>
<?php foreach ($accounts as $account) : ?>
	<li><a href="projects.php?account_id=<?=$account->id?>"><?=$account->email?></a></li>	
<?php endforeach; ?>
</ul>

<p>
<?php if ($page > 1) : ?>
	<a href="index.php?page=<?=$page - 1?>">&laquo;Previous page</a> |
<?php endif; ?>
	<a href="index.php?page=<?=$page + 1?>">Next page &raquo;</a>
</p>