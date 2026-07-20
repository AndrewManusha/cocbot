<?php


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
// TELEGRAM MESSAGE
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
// REPLY USER
// =====================================

function getReplyUser($message)
{

    if (
        empty(
            $message['reply_to_message']['from']
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
    userService()
        ->register($user);
}





// =====================================
// MENTION USER
// =====================================

function mentionUser($user)
{

    return
        '<a href="tg://user?id=' .
        $telegram_id .
        '">⁣</a>';
}





// =====================================
// ACTION LOG
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



// =====================================
// нормализация тега игрока/клана
// =====================================


function normalizeTag($tag)
{
    $tag = strtoupper(trim($tag));

    return str_replace('#', '', $tag);
}