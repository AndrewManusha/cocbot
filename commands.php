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
// СПИСОК ИГРОКОВ
// =====================================

function commandList($chat_id, $thread_id)
{

    $text =
        "📋 <b>КЛАН</b>\n\n";


    $userService =
        userService();


    $playerService =
        playerService();



    $admins =
        $userService
            ->admins();



    $text .=
        "👑 <b>РУКОВОДИТЕЛИ</b>\n\n";



    if (!$admins) {

        $text .= "— нет\n\n";

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




    $users =
        $userService
            ->members();




    $text .=
        "👥 <b>УЧАСТНИКИ</b>\n\n";



    if (!$users) {

        $text .= "— нет";

    }
    else {


        foreach ($users as $user) {


            $line =
                mentionUser($user);



            $players =
                $playerService
                    ->getUserPlayers(
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
                $line
                .
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
        normalizeTag(
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



    $playerService =
        playerService();



    if (
        !$playerService
            ->exists($tag)
    ) {


        $player =
            getPlayerFromApi($tag);



        if (!$player) {

            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Игрок не найден."
            );

            return;

        }

    }




    if (
        $playerService
            ->isLinked($tag)
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "⚠️ Этот аккаунт уже привязан."
        );

        return;

    }




    $labels =
        generateVerificationLabels();



    verificationRepository()
        ->create(
            $from_id,
            $tag,
            $labels
        );




    $text =
        "🔐 <b>Проверка аккаунта</b>\n\n";


    $text .=
        "Установите метки:\n\n";



    foreach ($labels['names'] as $name) {

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
// !ОБЪЯВЛЕНИЕ
// =====================================

function commandAnnouncement(
    $chat_id,
    $thread_id,
    $from_id
)
{

    if (
        !adminService()
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
        userService()
            ->all();



    if (!$users) {

        sendMessage(
            $chat_id,
            $thread_id,
            "📢 Пользователей нет."
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
        "📢 <b>ОБЩЕЕ ОБЪЯВЛЕНИЕ</b>\n\n"
        .
        trim($mentions)
    );

}





// =====================================
// !КЛАН ДОБАВИТЬ
// =====================================

function commandAddClan($message, $args)
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
        !adminService()
            ->isAdmin($from_id)
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Нет прав."
        );

        return;

    }



    if (empty($args[0])) {

        sendMessage(
            $chat_id,
            $thread_id,
            "Использование:\n!клан добавить #TAG"
        );

        return;

    }



    $tag =
        normalizeTag(
            $args[0]
        );



    $service =
        clanService();



    if (
        $service
            ->exists($tag)
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "⚠️ Клан уже добавлен."
        );

        return;

    }



    $clan =
        getClanFromApi($tag);



    if (!$clan) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Клан не найден."
        );

        return;

    }



    if (
        $service
            ->add($clan)
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "✅ Клан добавлен:\n\n"
            .
            "🏰 {$clan['name']}\n"
            .
            "🎯 Уровень: {$clan['clanLevel']}\n"
            .
            "👥 Участников: {$clan['members']}"
        );

    }

}





// =====================================
// !КЛАНЫ
// =====================================

function commandClans($chat_id, $thread_id)
{

    $clans =
        clanService()
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
            $message['text']
            ??
            ''
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



        case "клан":

            if (
                isset($args[0])
                &&
                mb_strtolower($args[0])
                ==
                "добавить"
            ) {

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

?>