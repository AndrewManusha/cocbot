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