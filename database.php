<?php


require_once __DIR__ . '/config.php';


// =====================================
// MYSQL CONNECTION
// =====================================

try {

    $db = new PDO(

        "mysql:host=" . DB_HOST .
        ";dbname=" . DB_NAME .
        ";charset=" . DB_CHARSET,

        DB_USER,

        DB_PASS

    );


    $db->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );


    $db->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );


}
catch(PDOException $e)
{

    file_put_contents(

        LOG_FILE,

        "[" . date("d.m.Y H:i:s") . "] DATABASE ERROR: " .
        $e->getMessage() .
        PHP_EOL,

        FILE_APPEND

    );


    exit;

}




// =====================================
// USERS
// =====================================


// получить пользователя

function getUser($telegram_id)
{

    return userRepository()->find(
        $telegram_id
    );

}




// добавить пользователя

function addUser($data)
{

    return userRepository()->create(
        $data
    );

}




// обновить время активности

function updateUserActivity($telegram_id)
{

    return userRepository()->updateActivity(
        $telegram_id
    );

}




// получить всех пользователей

function getUsers()
{

    return userRepository()->all();

}




// =====================================
// PLAYER TAG
// =====================================


// Проверка существования игрока в клане
// Пока таблицы players нет — пропускаем проверку

function checkPlayerExistsInClan($player_tag)
{
    return true;
}




// сохранить Clash тег

function setPlayerTag(
    $telegram_id,
    $player_tag
)
{

    global $db;


    $player_tag =
        strtoupper(
            trim(
                $player_tag
            )
        );


    $player_tag =
        ltrim(
            $player_tag,
            '#'
        );



    if (!checkPlayerExistsInClan($player_tag)) {

        return false;

    }



    $stmt =
        $db->prepare("

            UPDATE users

            SET player_tag = ?

            WHERE telegram_id = ?

        ");



    return $stmt->execute([

        $player_tag,

        $telegram_id

    ]);

}




// =====================================
// PLAYERS
// =====================================


// Проверка существования игрока в таблице players

function playerExists($player_tag)
{

    global $db;


    $player_tag =
        strtoupper(
            trim(
                ltrim(
                    $player_tag,
                    '#'
                )
            )
        );



    $stmt =
        $db->prepare("

            SELECT player_tag

            FROM players

            WHERE player_tag = ?

            LIMIT 1

        ");



    $stmt->execute([

        $player_tag

    ]);



    return (bool)$stmt->fetchColumn();

}




// =====================================
// ADMINS
// =====================================


// проверка администратора

function isAdmin($telegram_id)
{

    global $db;


    $stmt =
        $db->prepare("

            SELECT telegram_id

            FROM admins

            WHERE telegram_id = ?

            LIMIT 1

        ");



    $stmt->execute([

        $telegram_id

    ]);



    return (bool)$stmt->fetch();

}


?>