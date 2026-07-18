<?php


// =====================================
// USERS
// =====================================

function getUser($telegram_id)
{
    return userRepository()->find(
        $telegram_id
    );
}


function addUser($data)
{
    return userRepository()->create(
        $data
    );
}


function updateUserActivity($telegram_id)
{
    return userRepository()->updateActivity(
        $telegram_id
    );
}


function getUsers()
{
    return userRepository()->all();
}




// =====================================
// PLAYER TAG
// =====================================

function setPlayerTag(
    $telegram_id,
    $player_tag
)
{

    $player_tag =
        strtoupper(
            trim($player_tag)
        );


    $player_tag =
        ltrim(
            $player_tag,
            '#'
        );


    return playerRepository()->setUserTag(
        $telegram_id,
        $player_tag
    );

}




// =====================================
// PLAYERS
// =====================================

function playerExists($player_tag)
{
    return playerRepository()->exists(
        $player_tag
    );
}




// =====================================
// ADMINS
// =====================================

function isAdmin($telegram_id)
{
    return adminRepository()->isAdmin(
        $telegram_id
    );
}