<?php


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
// ОТПРАВКА TELEGRAM MESSAGE
// =====================================

function sendMessage(
    $chat_id,
    $thread_id,
    $text
)
{
    return telegram()->sendMessage(
        $chat_id,
        $thread_id,
        $text
    );
}




// =====================================
// ДЛИННЫЕ СООБЩЕНИЯ
// =====================================

function sendLongMessage(
    $chat_id,
    $thread_id,
    $text
)
{

    $parts =
        str_split(
            $text,
            4000
        );


    foreach ($parts as $part) {

        sendMessage(
            $chat_id,
            $thread_id,
            $part
        );

    }

}




// =====================================
// СООБЩЕНИЕ С КНОПКОЙ
// =====================================

function sendMessageWithButton(
    $chat_id,
    $thread_id,
    $text,
    $buttonText,
    $callbackData
)
{

    return message()

        ->chat($chat_id)

        ->thread($thread_id)

        ->text($text)

        ->button(
            $buttonText,
            $callbackData
        )

        ->send();

}





// =====================================
// ОТВЕТ НА CALLBACK
// =====================================

function answerCallback(
    $callback_id,
    $text = ''
)
{
    return telegram()->answerCallback(
        $callback_id,
        $text
    );
}





// =====================================
// ПОЛУЧИТЬ USER ИЗ REPLY
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

        'telegram_id' =>
            $user['id'],

        'username' =>
            $user['username'] ?? '',

        'first_name' =>
            $user['first_name'] ?? '',

        'last_name' =>
            $user['last_name'] ?? ''

    ];

}





// =====================================
// АВТОРЕГИСТРАЦИЯ USER
// =====================================

function registerUser($user)
{

    $exists =
        getUser(
            $user['id']
        );



    if (!$exists) {


        addUser([

            'telegram_id' =>
                $user['id'],

            'username' =>
                $user['username'] ?? '',

            'first_name' =>
                $user['first_name'] ?? '',

            'last_name' =>
                $user['last_name'] ?? ''

        ]);



        writeLog(
            "Добавлен пользователь " .
            (
                $user['username']
                ??
                $user['id']
            )
        );


    }
    else {


        updateUserActivity(
            $user['id']
        );


    }

}





// =====================================
// ССЫЛКА НА USER
// =====================================

function mentionUser($user)
{

    if (
        !empty($user['username'])
    ) {


        $name =
            '@' .
            ltrim(
                $user['username'],
                '@'
            );


    }
    else {


        $name =
            $user['first_name']
            ??
            'Игрок';


    }



    return

        '<a href="tg://user?id=' .
        $user['telegram_id'] .
        '">' .

        htmlspecialchars(
            $name
        ) .

        '</a>';

}





// =====================================
// ЛОГ ДЕЙСТВИЙ
// =====================================

function addLog(
    $user_id,
    $username,
    $action
)
{

    writeLog(

        $username .
        " : " .
        $action

    );

}