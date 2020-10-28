<?php

require_once '../autoload.php';

$game = new \Chess\Game();
$game->proceed($_GET['game_id']);
$game->action($_POST['id'], $_POST['x'], $_POST['y']);
