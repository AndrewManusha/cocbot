<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/clash_api.php';



// =====================================
// !ПОМОЩЬ
// =====================================

function commandHelp($chat_id, $thread_id)
{

    $text = "

📖 <b>Команды клана</b>


👥 Для всех:

!список

Показать состав клана


!тег #TAG

Привязать Clash аккаунт



👑 Руководство:

!клан добавить #TAG

Добавить клан


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
// СПИСОК
// =====================================

function commandList($chat_id, $thread_id)
{

    $text =
        "📋 <b>КЛАН</b>\n\n";



    $admins =
        userRepository()
            ->allAdmins();



    $text .=
        "👑 <b>РУКОВОДИТЕЛИ</b>\n\n";


    foreach ($admins as $user) {

        $text .=
            mentionUser($user)
            . "\n";

    }


    $text .= "\n👥 <b>УЧАСТНИКИ</b>\n\n";



    $users =
        userRepository()
            ->allWithoutAdmins();



    foreach ($users as $user) {


        $line =
            mentionUser($user);



        $players =
            userPlayerRepository()
                ->getByUser(
                    $user['telegram_id']
                );



        foreach ($players as $player) {

            $line .=
                " <code>#"
                .
                htmlspecialchars(
                    $player['player_tag']
                )
                .
                "</code>";

        }


        $text .=
            $line . "\n";

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
        normalizeTag(
            implode(
                " ",
                $args
            )
        );



    if (!$tag) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Использование:\n!тег #TAG"
        );

        return;

    }



    $result =
        playerVerificationService()
            ->createVerification(
                $from_id,
                $tag
            );



    if (!$result['success']) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ " . $result['message']
        );

        return;

    }



    $text =
        "🔐 <b>Проверка аккаунта</b>\n\n";


    $text .=
        "Установите метки:\n\n";



    foreach ($result['labels']['names'] as $name) {

        $text .=
            "🏷 "
            .
            htmlspecialchars($name)
            .
            "\n";

    }



    $text .=
        "\nПосле установки нажмите кнопку.";



    sendMessageWithButton(
        $chat_id,
        $thread_id,
        $text,
        "✅ Готово",
        "verify_" . $tag
    );

}





// =====================================
// ОБЪЯВЛЕНИЕ
// =====================================

function commandAnnouncement(
    $chat_id,
    $thread_id,
    $from_id
)
{

    if (
        !adminRepository()
            ->isAdmin($from_id)
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Недостаточно прав."
        );

        return;

    }



    $users =
        userRepository()
            ->all();



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
        "📢 <b>ОБЩЕЕ ОБЪЯВЛЕНИЕ</b>\n\n"
        .
        trim($mentions)
    );

}





// =====================================
// ДОБАВИТЬ КЛАН
// =====================================

function commandAddClan($message,$args)
{

    $chat_id =
        $message['chat']['id'];

    $thread_id =
        $message['message_thread_id']
        ??
        null;


    $from_id =
        $message['from']['id'];



    if (
        !adminRepository()
            ->isAdmin($from_id)
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Нет прав."
        );

        return;

    }



    $tag =
        normalizeTag(
            $args[0] ?? ''
        );



    $result =
        clanService()
            ->addClan(
                $tag
            );



    sendMessage(
        $chat_id,
        $thread_id,
        $result['message']
    );

}





// =====================================
// КЛАНЫ
// =====================================

function commandClans($chat_id,$thread_id)
{

    $clans =
        clanRepository()
            ->all();



    if (!$clans) {

        sendMessage(
            $chat_id,
            $thread_id,
            "🏰 Кланы не добавлены."
        );

        return;

    }



    $text =
        "🏰 <b>КЛАНЫ</b>\n\n";



    foreach ($clans as $clan) {


        $text .=
            "🏰 <b>"
            .
            htmlspecialchars(
                $clan['name']
            )
            .
            "</b>\n";


        $text .=
            "<code>#"
            .
            htmlspecialchars(
                $clan['tag']
            )
            .
            "</code>\n\n";

    }



    sendMessage(
        $chat_id,
        $thread_id,
        $text
    );

}





// =====================================
// ROUTER
// =====================================

function processCommand($message)
{

    $text =
        trim(
            $message['text'] ?? ''
        );


    if (
        !$text ||
        (
            $text[0] != '!' &&
            $text[0] != '/'
        )
    ) {
        return;
    }



    $parts =
        explode(" ",$text);



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



    $chat_id =
        $message['chat']['id'];


    $thread_id =
        $message['message_thread_id']
        ??
        null;


    $from_id =
        $message['from']['id'];



    switch($command){


        case 'помощь':
        case 'команды':

            commandHelp(
                $chat_id,
                $thread_id
            );

        break;



        case 'список':
        case 'игроки':

            commandList(
                $chat_id,
                $thread_id
            );

        break;



        case 'тег':

            commandTag(
                $chat_id,
                $thread_id,
                $from_id,
                $args
            );

        break;



        case 'объявление':
        case 'обьявление':

            commandAnnouncement(
                $chat_id,
                $thread_id,
                $from_id
            );

        break;



        case 'клан':

            if (
                isset($args[0])
                &&
                mb_strtolower($args[0])
                ==
                'добавить'
            ){

                commandAddClan(
                    $message,
                    array_slice(
                        $args,
                        1
                    )
                );

            }
            else {

                commandClans(
                    $chat_id,
                    $thread_id
                );

            }

        break;


    }

}
