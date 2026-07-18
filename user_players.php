<?php


// =====================================
// USER PLAYERS FUNCTIONS
// =====================================



function getUserPlayers($telegram_id)
{

    return userPlayerRepository()
        ->getByUser(
            $telegram_id
        );

}




function getMainPlayer($telegram_id)
{

    return userPlayerRepository()
        ->getMain(
            $telegram_id
        );

}




function hasUserPlayer($player_tag)
{

    return userPlayerRepository()
        ->exists(
            $player_tag
        );

}




function addUserPlayer(
    $telegram_id,
    $player_tag
)
{

    return userPlayerRepository()
        ->create(
            $telegram_id,
            $player_tag
        );

}




function setMainPlayer(
    $telegram_id,
    $player_tag
)
{

    return userPlayerRepository()
        ->setMain(
            $telegram_id,
            $player_tag
        );

}