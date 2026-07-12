<?php


file_put_contents(
    __DIR__ . '/alive.txt',
    date('H:i:s') . " работает\n",
    FILE_APPEND
);



require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/commands.php';
require_once __DIR__ . '/clans.php';
require_once __DIR__ . '/clash_api.php';




// =====================================
// ПОЛУЧЕНИЕ UPDATE TELEGRAM
// =====================================


$content =
    file_get_contents(
        "php://input"
    );



if (!$content) {

    exit;

}



$update =
    json_decode(
        $content,
        true
    );



if (
    !$update
    ||
    !isset(
        $update['message']
    )
) {

    exit;

}



$message =
    $update['message'];




// =====================================
// ПРОВЕРКА USER
// =====================================


if (
    !isset(
        $message['from']
    )
) {

    exit;

}



$telegramUser =
    $message['from'];




// =====================================
// СОХРАНЕНИЕ ПОЛЬЗОВАТЕЛЯ
// =====================================


registerUser(
    $telegramUser
);




// =====================================
// ОБРАБОТКА КОМАНД
// =====================================


processCommand(
    $message
);



?>