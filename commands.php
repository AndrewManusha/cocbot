<?php

require_once __DIR__ . '/helpers.php';


// =====================================
// !ПОМОЩЬ
// =====================================

function commandHelp(
    $chat_id,
    $thread_id
)
{

    $text = "

📖 <b>Команды клана</b>


👥 Для всех:

!клан

Показать состав клана


!тег #TAG

Указать свой Clash of Clans тег



👑 Руководство:

!объявление

Общее объявление

";


    sendMessage(
        $chat_id,
        $thread_id,
        $text
    );

}




// =====================================
// !КЛАН
// =====================================

function commandList(
    $chat_id,
    $thread_id
)
{

    global $db;


    $text =
        "📋 <b>КЛАН</b>\n\n";



    // ===============================
    // АДМИНЫ
    // ===============================


    $stmt = $db->query("

        SELECT users.*

        FROM users

        INNER JOIN admins

        ON users.telegram_id = admins.telegram_id

        ORDER BY username

    ");



    $admins =
        $stmt->fetchAll();



    $text .=
        "👑 <b>РУКОВОДИТЕЛИ</b>\n\n";



    if (!$admins) {


        $text .=
            "— нет\n\n";


    }
    else {


        foreach ($admins as $user) {


            $text .=
                mentionUser($user)
                .
                "\n";

        }


        $text .= "\n";

    }





    // ===============================
    // ИГРОКИ
    // ===============================


    $stmt = $db->query("

        SELECT users.*

        FROM users

        LEFT JOIN admins

        ON users.telegram_id = admins.telegram_id

        WHERE admins.telegram_id IS NULL

        ORDER BY username

    ");



    $users =
        $stmt->fetchAll();



    $text .=
        "👥 <b>УЧАСТНИКИ</b>\n\n";



    if (!$users) {


        $text .=
            "— нет";


    }
    else {


        foreach ($users as $user) {


            $line =
                mentionUser($user);



            if (
                !empty(
                    $user['player_tag']
                )
            ) {


                $line .=
                    " <code>" .
                    htmlspecialchars(
                        $user['player_tag']
                    )
                    .
                    "</code>";

            }



            $text .=
                $line .
                "\n";


        }


    }



    sendLongMessage(
        $chat_id,
        $thread_id,
        $text
    );

}




// =====================================
// !ТЕГ
// =====================================

function commandTag(
    $chat_id,
    $thread_id,
    $from_id,
    $args
)
{

    $tag =
        trim(
            implode(
                " ",
                $args
            )
        );



    if ($tag == '') {


        sendMessage(
            $chat_id,
            $thread_id,

            "❌ Использование:\n!тег #TAG"

        );


        return;

    }



    setPlayerTag(
        $from_id,
        $tag
    );



    sendMessage(
        $chat_id,
        $thread_id,

        "✅ Ваш тег сохранён:\n<code>" .
        htmlspecialchars($tag) .
        "</code>"

    );

}




// =====================================
// !ОБЪЯВЛЕНИЕ
// =====================================

function commandAnnouncement(
    $chat_id,
    $thread_id,
    $from_id
)
{


    if (
        !isAdmin($from_id)
    ) {


        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Недостаточно прав."
        );


        return;

    }



    $users =
        getUsers();



    if (!$users) {


        sendMessage(
            $chat_id,
            $thread_id,
            "📢 В базе нет участников."
        );


        return;

    }



    $mentions = "";



    foreach ($users as $user) {


        $mentions .=
            mentionUser($user)
            .
            " ";

    }



    sendMessage(
        $chat_id,
        $thread_id,

        "📢 <b>ОБЩЕЕ ОБЪЯВЛЕНИЕ</b>\n\n" .
        trim($mentions)

    );

}




// =====================================
// ОБРАБОТКА КОМАНД
// =====================================

function processCommand($message)
{


    $text =
        trim(
            $message['text'] ?? ''
        );



    if (
        $text == ''
        ||
        (
            $text[0] != '!'
            &&
            $text[0] != '/'
        )
    ) {

        return;

    }



    $chat_id =
        $message['chat']['id'];



    $thread_id =
        $message['message_thread_id']
        ??
        null;



    $from_id =
        $message['from']['id'];



    $parts =
        explode(
            " ",
            $text
        );



    $command =
        mb_strtolower(
            str_replace(
                ['!','/'],
                '',
                $parts[0]
            )
        );



    $args =
        array_slice(
            $parts,
            1
        );



    switch ($command) {


        case "помощь":

        case "команды":

            commandHelp(
                $chat_id,
                $thread_id
            );

        break;



        case "клан":

        case "список":

        case "игроки":

            commandList(
                $chat_id,
                $thread_id
            );

        break;



        case "тег":

            commandTag(
                $chat_id,
                $thread_id,
                $from_id,
                $args
            );

        break;



        case "объявление":

        case "обьявление":

            commandAnnouncement(
                $chat_id,
                $thread_id,
                $from_id
            );

        break;


    }


}


?>