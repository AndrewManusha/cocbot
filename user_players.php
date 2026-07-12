<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/clash_api.php';


// =====================================
// ПОЛУЧИТЬ ВСЕ АККАУНТЫ ПОЛЬЗОВАТЕЛЯ
// =====================================

function getUserPlayers($telegram_id)
{
    global $db;


    $stmt = $db->prepare("

        SELECT *

        FROM user_players

        WHERE telegram_id = ?

        ORDER BY is_main DESC, created_at ASC

    ");


    $stmt->execute([
        $telegram_id
    ]);


    return $stmt->fetchAll();

}




// =====================================
// ПОЛУЧИТЬ ОСНОВНОЙ АККАУНТ
// =====================================

function getMainPlayer($telegram_id)
{
    global $db;


    $stmt = $db->prepare("

        SELECT *

        FROM user_players

        WHERE telegram_id = ?

        AND is_main = 1

        LIMIT 1

    ");


    $stmt->execute([
        $telegram_id
    ]);


    return $stmt->fetch();

}




// =====================================
// ПРОВЕРКА ПРИВЯЗКИ АККАУНТА
// =====================================

function hasUserPlayer($player_tag)
{
    global $db;


    $player_tag =
        normalizeTag($player_tag);


    $stmt = $db->prepare("

        SELECT player_tag

        FROM user_players

        WHERE player_tag = ?

        LIMIT 1

    ");


    $stmt->execute([
        $player_tag
    ]);


    return (bool)$stmt->fetchColumn();

}




// =====================================
// ДОБАВИТЬ АККАУНТ ПОЛЬЗОВАТЕЛЯ
// =====================================

function addUserPlayer(
    $telegram_id,
    $player_tag
)
{
    global $db;


    $player_tag =
        normalizeTag($player_tag);


    if (hasUserPlayer($player_tag)) {

        return false;

    }


    // Проверяем есть ли уже аккаунты

    $stmt = $db->prepare("

        SELECT COUNT(*)

        FROM user_players

        WHERE telegram_id = ?

    ");


    $stmt->execute([
        $telegram_id
    ]);


    $count =
        $stmt->fetchColumn();



    $is_main =
        ($count == 0) ? 1 : 0;



    $stmt = $db->prepare("

        INSERT INTO user_players
        (
            player_tag,
            telegram_id,
            is_main,
            created_at,
            verified_at
        )

        VALUES
        (
            ?,
            ?,
            ?,
            NOW(),
            NOW()
        )

    ");


    return $stmt->execute([

        $player_tag,

        $telegram_id,

        $is_main

    ]);

}




// =====================================
// СДЕЛАТЬ АККАУНТ ОСНОВНЫМ
// =====================================

function setMainPlayer(
    $telegram_id,
    $player_tag
)
{
    global $db;


    $player_tag =
        normalizeTag($player_tag);


    $db->beginTransaction();


    try {


        $stmt = $db->prepare("

            UPDATE user_players

            SET is_main = 0

            WHERE telegram_id = ?

        ");


        $stmt->execute([
            $telegram_id
        ]);



        $stmt = $db->prepare("

            UPDATE user_players

            SET is_main = 1

            WHERE telegram_id = ?

            AND player_tag = ?

        ");


        $stmt->execute([

            $telegram_id,

            $player_tag

        ]);


        $db->commit();


        return true;


    }
    catch (Exception $e)
    {

        $db->rollBack();

        return false;

    }

}

?>