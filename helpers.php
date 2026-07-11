<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';


// =====================================
// ЛОГИРОВАНИЕ
// =====================================

function writeLog($text)
{

    file_put_contents(
        LOG_FILE,
        "[" . date("d.m.Y H:i:s") . "] " .
        $text .
        PHP_EOL,
        FILE_APPEND
    );

}



// =====================================
// ОТПРАВКА СООБЩЕНИЯ TELEGRAM
// =====================================

function sendMessage(
    $chat_id,
    $thread_id,
    $text
)
{

    $url = API_URL . "sendMessage";


    $data = [

        'chat_id' => $chat_id,

        'text' => $text,

        'parse_mode' => 'HTML',

        'disable_web_page_preview' => true

    ];


    // если сообщение из темы
    if (
        $thread_id !== null
    ) {

        $data['message_thread_id'] = $thread_id;

    }



    $curl = curl_init();


    curl_setopt_array(
        $curl,
        [

            CURLOPT_URL => $url,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => $data,

            CURLOPT_RETURNTRANSFER => true

        ]
    );


    $result = curl_exec($curl);


    if ($result === false) {

        writeLog(
            "CURL ERROR: ".curl_error($curl)
        );

    }


    curl_close($curl);


    return $result;

}



// =====================================
// РАЗБИВКА ДЛИННЫХ СООБЩЕНИЙ
// =====================================

function sendLongMessage($chat_id, $thread_id, $text)
{

    $parts = str_split(
        $text,
        4000
    );


    foreach ($parts as $part) {

        sendMessage(
            $chat_id, $thread_id,
            $part
        );

    }

}



// =====================================
// ПОЛУЧИТЬ РОЛЬ ПО РАНГУ
// =====================================

function getRankName($rank)
{

    global $RANKS;


    return $RANKS[$rank]['name'] ?? 'неизвестно';

}



function getRankEmoji($rank)
{

    global $RANKS;


    return $RANKS[$rank]['emoji'] ?? '';

}



// =====================================
// ПРОВЕРКА: УЧАСТВУЕТ ЛИ В КВ
// =====================================

function canJoinKV($rank)
{

    global $RANKS;


    return $RANKS[$rank]['kv'];

}



// =====================================
// ПОЛУЧИТЬ ПОЛЬЗОВАТЕЛЯ ИЗ REPLY
// =====================================

function getReplyUser($message)
{

    if (
        !isset(
            $message['reply_to_message']
        )
    ) {

        return false;

    }



    $user =
        $message['reply_to_message']['from'];



    return [

        'id' => $user['id'],

        'username' =>
            '@' .
            (
                $user['username']
                ??
                $user['first_name']
            )

    ];

}



// =====================================
// АВТОДОБАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯ
// =====================================

function registerUser($user)
{

    global $db;



    $stmt = $db->prepare("

        SELECT user_id

        FROM members

        WHERE user_id=?

    ");



    $stmt->execute(
        [
            $user['id']
        ]
    );



    if (!$stmt->fetch()) {


        $stmt = $db->prepare("

            INSERT INTO members

            (
                user_id,
                username,
                rank
            )

            VALUES

            (?,?,2)

        ");



        $stmt->execute(

            [

                $user['id'],

                $user['username']

            ]

        );


        writeLog(
            "Добавлен новый пользователь " .
            $user['username']
        );


    }

    else {


        $stmt = $db->prepare("

            UPDATE members

            SET

            username=?,

            last_seen=CURRENT_TIMESTAMP

            WHERE user_id=?

        ");



        $stmt->execute(

            [

                $user['username'],

                $user['id']

            ]

        );


    }


}



// =====================================
// ПОЛУЧИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ
// =====================================

function getUser($id)
{

    global $db;


    $stmt = $db->prepare("

        SELECT *

        FROM members

        WHERE user_id=?

    ");



    $stmt->execute(
        [$id]
    );


    return $stmt->fetch();

}



// =====================================
// ЗАПИСЬ ДЕЙСТВИЯ В ЛОГ
// =====================================

function addLog(
    $user_id,
    $username,
    $action
)
{

    global $db;



    $stmt = $db->prepare("

        INSERT INTO logs

        (
            user_id,
            username,
            action
        )

        VALUES
        (?,?,?)

    ");



    $stmt->execute(

        [

            $user_id,

            $username,

            $action

        ]

    );


    writeLog(
        $username .
        " : " .
        $action
    );

}



function mentionUser($user)
{

    if (!empty($user['game_name'])) {

        $name = $user['game_name'];

    } elseif (!empty($user['username'])) {

        $name = ltrim($user['username'], '@');

        $name = '@'.$name;

    } else {

        $name = $user['first_name'] ?? 'Игрок';

    }


    return '<a href="tg://user?id='.$user['user_id'].'">' .
            htmlspecialchars($name) .
           '</a>';

}


function getTargetUser($message, $args)
{

    global $db;


    // Вариант 1: ответ на сообщение

    if (
        isset($message['reply_to_message'])
    ) {

        return getReplyUser($message);

    }



    // Вариант 2: поиск по @username

    foreach ($args as $key=>$arg) {


        if (
            str_starts_with($arg, "@")
        ) {


            $username =
                ltrim(
                    $arg,
                    "@"
                );


            $stmt = $db->prepare("
                SELECT *
                FROM members
                WHERE username=?
            ");


            $stmt->execute([
                $username
            ]);


            $user =
                $stmt->fetch();



            if ($user) {

                return $user;

            }


        }

    }



    return false;

}


?>