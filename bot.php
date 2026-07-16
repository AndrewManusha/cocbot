<<?php


file_put_contents(
    __DIR__ . '/alive.txt',
    date('H:i:s') . " работает\n",
    FILE_APPEND
);



// =====================================
// BOOTSTRAP
// =====================================

require_once __DIR__ . '/app/bootstrap.php';


// =====================================
// DATABASE
// =====================================

require_once __DIR__ . '/database.php';


// =====================================
// HELPERS
// =====================================

require_once __DIR__ . '/helpers.php';


// =====================================
// BOT LOGIC
// =====================================

require_once __DIR__ . '/commands.php';

require_once __DIR__ . '/clans.php';

require_once __DIR__ . '/clash_api.php';

require_once __DIR__ . '/user_players.php';

require_once __DIR__ . '/player_verifications.php';

require_once __DIR__ . '/verification.php';




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




// =====================================
// ПРОВЕРКА UPDATE
// =====================================


if (!$update) {

    exit;

}




// =====================================
// CALLBACK QUERY (КНОПКИ)
// =====================================


if (
    isset($update['callback_query'])
) {


    $callback =
        $update['callback_query'];



    $data =
        $callback['data'] ?? '';



    if (
        str_starts_with(
            $data,
            'verify_'
        )
    ) {


        $playerTag =
            str_replace(
                'verify_',
                '',
                $data
            );



        $chat_id =
            $callback['message']['chat']['id'];



        $thread_id =
            $callback['message']['message_thread_id']
            ?? null;



        $telegram_id =
            $callback['from']['id'];



        verifyPlayerAccount(
            $telegram_id,
            $playerTag,
            $chat_id,
            $thread_id
        );


        answerCallback(
            $callback['id']
        );

    }



    exit;

}





// =====================================
// ОБЫЧНОЕ СООБЩЕНИЕ
// =====================================


if (
    !isset($update['message'])
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


