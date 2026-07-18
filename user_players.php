<?php


// =====================================
// USER PLAYERS
// =====================================


// получить все аккаунты пользователя

function getUserPlayers($telegram_id)
{
    return userPlayerRepository()->getByUser(
        $telegram_id
    );
}




// получить основной аккаунт

function getMainPlayer($telegram_id)
{
    return userPlayerRepository()->getMain(
        $telegram_id
    );
}




// проверить привязку аккаунта

function hasUserPlayer($player_tag)
{
    return userPlayerRepository()->exists(
        $player_tag
    );
}




// добавить аккаунт

function addUserPlayer(
    $telegram_id,
    $player_tag
)
{
    return userPlayerRepository()->create(
        $telegram_id,
        $player_tag
    );
}




// сделать основным

function setMainPlayer(
    $telegram_id,
    $player_tag
)
{
    return userPlayerRepository()->setMain(
        $telegram_id,
        $player_tag
    );
}

?>