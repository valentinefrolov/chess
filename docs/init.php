<?php

require_once '../autoload.php';

$game = new \Chess\Game();

if(!empty($_GET['game_id'])) {
    if(!$game->proceed($_GET['game_id'])) {
        echo 'game data not found'; exit();
    }
} else {
    $game->init('test', 'test2');
}

echo json_encode($game->getData(), JSON_UNESCAPED_UNICODE);




