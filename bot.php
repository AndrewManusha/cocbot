<?php

file_put_contents(
    __DIR__.'/alive.txt',
    date('H:i:s')." работает\n",
    FILE_APPEND
);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/permissions.php';
require_once __DIR__ . '/commands.php';


// =====================================
// ПОЛУЧЕНИЕ ДАННЫХ ОТ TELEGRAM
// =====================================

$content = file_get_contents("php://input");


if (!$content) {

    exit;

}



$update =
    json_decode(
        $content,
        true
    );

if (
    !$update ||
    !isset(
        $update['message']
    )
) {

    exit;

}



$message =
    $update['message'];



// =====================================
// ПРОВЕРКА ПОЛЬЗОВАТЕЛЯ
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



$user = [

    'id' =>
        $telegramUser['id'],

    'username' =>
        '@'
        .
        (
            $telegramUser['username']
            ??
            $telegramUser['first_name']
        )

];



// Автоматически добавляем в базу

registerUser(
    $user
);



// =====================================
// ОБРАБОТКА КОМАНД
// =====================================

processCommand(
    $message
);



?>