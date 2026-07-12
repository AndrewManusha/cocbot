<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/clans.php';
require_once __DIR__ . '/clash_api.php';
require_once __DIR__ . '/user_players.php';
require_once __DIR__ . '/player_verifications.php';


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

Указать свой Clash of Clans тег



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

    global $db;


    $text =
        "📋 <b>КЛАН</b>\n\n";



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

        $text .= "— нет\n\n";

    }
    else {

        foreach ($admins as $user) {

            $text .=
                mentionUser($user)
                . "\n";

        }

        $text .= "\n";

    }



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

        $text .= "— нет";

    }
    else {

        foreach ($users as $user) {


            $line =
                mentionUser($user);



            if (!empty($user['player_tag'])) {

                $line .=
                    " <code>" .
                    htmlspecialchars($user['player_tag']) .
                    "</code>";

            }


            $text .=
                $line . "\n";

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
            trim(
                implode(" ", $args)
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



    // Проверяем есть ли игрок в нашей базе

    if (!playerExists($tag)) {


        // Если нет — проверяем через API

        $player =
            getPlayerFromApi($tag);



        if (!$player) {

            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Игрок с таким тегом не найден."
            );

            return;

        }

    }



    // Проверяем, не привязан ли уже аккаунт

    if (hasUserPlayer($tag)) {

        sendMessage(
            $chat_id,
            $thread_id,
            "⚠️ Этот аккаунт уже привязан к Telegram."
        );

        return;

    }



    // Генерируем 3 случайных labels

    $verification =
        generateVerificationLabels();



    // Создаём временную проверку

    createVerification(
        $from_id,
        $tag,
        $verification
    );



    $text =
        "🔐 <b>Проверка аккаунта</b>\n\n";


    $text .=
        "Установите в Clash of Clans следующие метки:\n\n";



    foreach ($verification['names'] as $name) {

        $text .=
            "🏷 " .
            htmlspecialchars($name) .
            "\n";

    }



    $text .=
        "\nПосле установки нажмите кнопку ниже.";





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

    if (!isAdmin($from_id)) {

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
            . " ";

    }



    sendMessage(
        $chat_id,
        $thread_id,
        "📢 <b>ОБЩЕЕ ОБЪЯВЛЕНИЕ</b>\n\n" .
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
        ?? null;


    $from_id =
        $message['from']['id'];



    if (!isAdmin($from_id)) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ У вас нет прав."
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
        normalizeTag($args[0]);



    if (clanExists($tag)) {

        sendMessage(
            $chat_id,
            $thread_id,
            "⚠️ Этот клан уже добавлен."
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



    if (addClan($clan)) {


        sendMessage(
            $chat_id,
            $thread_id,

            "✅ Клан добавлен:\n\n" .
            "🏰 {$clan['name']}\n" .
            "🎯 Уровень: {$clan['clanLevel']}\n" .
            "👥 Участников: {$clan['members']}"
        );


    }
    else {


        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Ошибка сохранения."
        );

    }

}


// =====================================
// !КЛАН
// =====================================

function commandClans($chat_id, $thread_id)
{

    $clans = getClans();


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


    $text .=
        "Всего: <b>" . count($clans) . "</b>\n\n";


    foreach ($clans as $clan) {


        $text .=
            "🏰 <b>" .
            htmlspecialchars($clan['name']) .
            "</b>\n";

        $text .=
            "<code>#" .
            htmlspecialchars($clan['tag']) .
            "</code>\n\n";

    }


    sendMessage(
        $chat_id,
        $thread_id,
        $text
    );

}



// =====================================
// ОБРАБОТКА КОМАНД
// =====================================

function processCommand($message)
{

    $text =
        trim($message['text'] ?? '');



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
        ?? null;

    $from_id =
        $message['from']['id'];



    $parts =
        explode(" ", $text);



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
                mb_strtolower($args[0]) == "добавить"
            ) {

                commandAddClan(
                    $message,
                    array_slice($args, 1)
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