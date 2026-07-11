<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/permissions.php';


// =====================================
// !ПОМОЩЬ
// =====================================

function commandHelp($chat_id, $thread_id)
{

    $text = "
📖 <b>Команды клана</b>

👤 Для всех:

!кв да
Участие в КВ

!кв нет причина
Отказ от КВ

!список
Состав клана

!имя (ваш ник из игры)


👑 Для руководства:

!звание роль
Назначить роль (по реплаю)

!повысить
Повысить игрока

!понизить
Понизить игрока

!удалить
Удалить игрока

!атакуем
Сбор на КВ

!объявление
Пинг всех участников
";


    sendMessage(
        $chat_id,
        $thread_id,
        $text
    );

}



// =====================================
// !СПИСОК
// =====================================

function commandList($chat_id, $thread_id)
{
    

    global $db;
    global $LIST_ORDER;
    global $RANKS;


    $text = "📋 <b>СОСТАВ КЛАНА</b>\n\n";


    foreach ($LIST_ORDER as $rank) {


        $title =
            $RANKS[$rank]['emoji']
            ." "
            .
            mb_strtoupper(
                $RANKS[$rank]['name']
            );


        $stmt = $db->prepare("
            SELECT user_id, username, game_name, kv_status, kv_reason
            FROM members
            WHERE rank IN (?,?)
            ORDER BY username
        ");
        
        $stmt->execute(
            [
                $rank,
                ($rank == 5 ? 6 : $rank)
            ]
        );


        $users =
            $stmt->fetchAll();



        $text .=
            "<b>"
            .
            $title
            .
            "</b> ("
            .
            count($users)
            .
            ")\n";



        if (!$users) {

            $text .= "— пусто\n\n";

            continue;

        }



        foreach ($users as $user) {


            // Олды и руководство без КВ статуса

            if (
                !$RANKS[$rank]['kv']
            ) {


                $text .=

                    mentionUser($user)
                    .
                    "\n";


            }

            else {


                $status =
                    $user['kv_status']
                    ==
                    'да'
                    ?
                    "🟢"
                    :
                    "🔴";



                $reason =
                    $user['kv_reason']
                    ?
                    " — "
                    .
                    $user['kv_reason']
                    :
                    "";



                $text .=

                    $status
                    .
                    " "
                    .
                    mentionUser($user)
                    .
                    $reason
                    .
                    "\n";

            }

        }


        $text .= "\n";


    }


    sendLongMessage(
        $chat_id,
        $thread_id,
        $text
    );

}



// =====================================
// !ЗВАНИЕ
// =====================================

function commandRank(
    $chat_id, $thread_id,
    $from_id,
    $message,
    $args
)
{

    global $db;
    global $RANKS;


    if (
        !checkAdmin(
            $chat_id, $thread_id,
            $from_id
        )
    ) return;



    if (
        !requireReply(
            $chat_id, $thread_id,
            $message
        )
    ) return;



    $target =
        getReplyUser(
            $message
        );

    $admin =
    getUser($from_id);

    $new_rank = false;



    $input =
    mb_strtolower(
        $args[0] ?? ''
    );



    foreach ($RANKS as $rank=>$data) {


        if (
            in_array(
                $input,
                $data['aliases']
            )
        ) {

            $new_rank = $rank;

        }

    }



    if ($new_rank === false) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Такой роли нет."
        );

        return;

    }



    // Нельзя назначить скрытый ранг 6
    if ($new_rank == 6) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Нельзя назначить скрытый ранг."
        );

        return;

    }
    
    
    // Нельзя назначать ранг равный или выше своего
    if (
        $new_rank >= $admin['rank']
        &&
        $admin['rank'] != 6
    ) {
    
        sendMessage(
            $chat_id, $thread_id,
            "❌ Нельзя назначить ранг равный или выше своего."
        );
    
        return;
    
    }



    if (
        !canManageUser(
            $from_id,
            $target['id']
        )
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Недостаточно прав."
        );

        return;

    }



    // Проверяем есть ли игрок в базе

    $stmt = $db->prepare("
        SELECT user_id, username, game_name, kv_status, kv_reason
        FROM members
        WHERE user_id=?
    ");


    $stmt->execute([
        $target['id']
    ]);


    $exists = $stmt->fetchColumn();



    if ($exists) {


        // Игрок есть — меняем ранг

        $stmt = $db->prepare("
            UPDATE members
            SET rank=?
            WHERE user_id=?
        ");


        $stmt->execute([
            $new_rank,
            $target['id']
        ]);



    } else {


        // Игрока нет — создаём


        $username =
            $target['username'] ?? '';


        $first_name =
            $target['first_name'] ?? '';



        $stmt = $db->prepare("
            INSERT INTO members
            (
                user_id,
                username,
                first_name,
                game_name,
                rank,
                kv_status,
                kv_reason
            )
            VALUES
            (?, ?, ?, '', ?, 'да', '')
        ");



        $stmt->execute([
            $target['id'],
            $username,
            $first_name,
            $new_rank
        ]);

    }

    $user = getUser($target['id']);

    addLog(
        $from_id,
        $target['username'] ?? $target['first_name'],
        "назначено звание ".$RANKS[$new_rank]['name']
    );



    sendMessage(
        $chat_id,
        $thread_id,

        "✅ "
        .
        mentionUser($user)
        .
        " теперь "
        .
        $RANKS[$new_rank]['emoji']
        .
        " "
        .
        $RANKS[$new_rank]['name']
    );

}
// =====================================
// !ПОВЫСИТЬ
// =====================================

function commandPromote(
    $chat_id, $thread_id,
    $from_id,
    $message
)
{

    global $db;
    global $RANKS;


    if (
        !checkAdmin($chat_id, $thread_id, $from_id)
    ) return;



    if (
        !requireReply($chat_id, $thread_id, $message)
    ) return;



    $target = getReplyUser($message);



    if (
        !canPromote(
            $from_id,
            $target['id']
        )
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Недостаточно прав."
        );

        return;

    }



    $user = getUser(
        $target['id']
    );


    if ($user['rank'] == 6) {
    
        sendMessage(
            $chat_id, $thread_id,
            "❌ Нельзя изменить владельца."
        );
    
        return;
    
    }

    $new_rank =
        $user['rank'] + 1;



    if (
        $new_rank > 5
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Максимальное звание."
        );

        return;

    }



    $stmt = $db->prepare("

        UPDATE members

        SET rank=?

        WHERE user_id=?

    ");



    $stmt->execute(
        [
            $new_rank,
            $target['id']
        ]
    );



    addLog(
        $from_id,
        $target['username'],
        "повышен до ".$RANKS[$new_rank]['name']
    );



    sendMessage(
        $chat_id,
        $thread_id,

        "⬆️ "
        .
        $target['username']
        .
        " теперь "
        .
        $RANKS[$new_rank]['emoji']
        .
        " "
        .
        $RANKS[$new_rank]['name']
    );

}



// =====================================
// !ПОНИЗИТЬ
// =====================================

function commandDemote(
    $chat_id, $thread_id,
    $from_id,
    $message
)
{

    global $db;
    global $RANKS;



    if (
        !checkAdmin($chat_id, $thread_id, $from_id)
    ) return;



    if (
        !requireReply($chat_id, $thread_id,$message)
    ) return;



    $target =
        getReplyUser($message);



    if (
        !canDemote(
            $from_id,
            $target['id']
        )
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Недостаточно прав."
        );

        return;

    }



    $user =
        getUser(
            $target['id']
        );



    $new_rank =
        $user['rank'] - 1;



    if (
        $new_rank < 1
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Ниже уже нельзя."
        );

        return;

    }



    $stmt =
        $db->prepare("

        UPDATE members

        SET rank=?

        WHERE user_id=?

    ");



    $stmt->execute(
        [
            $new_rank,
            $target['id']
        ]
    );



    addLog(
        $from_id,
        $target['username'],
        "понижен до ".$RANKS[$new_rank]['name']
    );



    sendMessage(
        $chat_id,
        $thread_id,

        "⬇️ "
        .
        $target['username']
        .
        " теперь "
        .
        $RANKS[$new_rank]['emoji']
        .
        " "
        .
        $RANKS[$new_rank]['name']
    );

}



// =====================================
// !УДАЛИТЬ
// =====================================

function commandDelete(
    $chat_id, $thread_id,
    $from_id,
    $message
)
{

    global $db;



    if (
        !checkAdmin($chat_id, $thread_id,$from_id)
    ) return;



    if (
        !requireReply($chat_id, $thread_id,$message)
    ) return;



    $target =
        getReplyUser($message);



    if (
        !canManageUser(
            $from_id,
            $target['id']
        )
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Нельзя удалить этого пользователя."
        );

        return;

    }



    $stmt =
        $db->prepare("

        DELETE FROM members

        WHERE user_id=?

    ");



    $stmt->execute(
        [
            $target['id']
        ]
    );



    addLog(
        $from_id,
        $target['username'],
        "удален из базы"
    );



    sendMessage(
        $chat_id,
        $thread_id,

        "🗑 "
        .
        $target['username']
        .
        " удалён из базы."
    );

}



// =====================================
// !КВ
// =====================================

function commandKV(
    $chat_id, $thread_id,
    $from_id,
    $args
)
{

    global $db;



    $user =
        getUser($from_id);



    if (!$user) return;



    if (
        $user['rank'] == 1
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Олды не участвуют в КВ."
        );

        return;

    }



    $status =
        mb_strtolower(
            $args[0] ?? ''
        );



    if (
        !in_array(
            $status,
            ['да','нет']
        )
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "Используйте: !кв да или !кв нет причина"
        );

        return;

    }



    $reason =
        implode(
            " ",
            array_slice(
                $args,
                1
            )
        );



    $stmt =
        $db->prepare("

        UPDATE members

        SET

        kv_status=?,

        kv_reason=?

        WHERE user_id=?

    ");



    $stmt->execute(
        [
            $status,
            $reason,
            $from_id
        ]
    );



    sendMessage(
        $chat_id,
        $thread_id,

        $status=="да"
        ?
        "🟢 Вы готовы к КВ."
        :
        "🔴 Вы не участвуете в КВ.\nПричина: ".$reason
    );

}

// =====================================
// !АТАКУЕМ
// =====================================

function commandAttack(
    $chat_id, $thread_id,
    $from_id
)
{

    global $db;


    if (
        !isLeader($from_id)
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Недостаточно прав."
        );

        return;

    }



    $stmt = $db->query("

        SELECT user_id, username, game_name

        FROM members

        WHERE rank IN (2,3,4)

        AND kv_status='да'

    ");



    $users =
        $stmt->fetchAll();



    if (!$users) {

        sendMessage(
            $chat_id, $thread_id,
            "⚔ Нет игроков готовых к КВ."
        );

        return;

    }



    $ping = "";



    foreach ($users as $user) {

        $ping .=
            mentionUser($user)
            .
            " ";

    }



    sendMessage(
        $chat_id,
        $thread_id,

        "⚔ <b>СБОР НА КВ!</b>\n\n"
        .
        trim($ping)
        .
        "\n\nВсего: "
        .
        count($users)
    );

}



// =====================================
// !ОБЪЯВЛЕНИЕ
// =====================================

function commandAnnouncement(
    $chat_id, $thread_id,
    $from_id
)
{

    global $db;



    if (
        !isLeader($from_id)
    ) {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Недостаточно прав."
        );

        return;

    }



    $stmt =
        $db->query("

        SELECT user_id, username, game_name

        FROM members

    ");



    $users =
        $stmt->fetchAll();



    if (!$users) {

        sendMessage(
            $chat_id, $thread_id,
            "📢 В базе пока нет участников."
        );

        return;

    }



    $ping = "";



    foreach ($users as $user) {

        $ping .=
            mentionUser($user)
            .
            " ";

    }



    sendMessage(
        $chat_id,
        $thread_id,

        "📢 <b>ОБЩЕЕ ОБЪЯВЛЕНИЕ</b>\n\n"
        .
        trim($ping)

    );

}

// =====================================
// !ИМЯ
// =====================================

function commandGameName(
    $chat_id, $thread_id,
    $from_id,
    $message,
    $args
)
{

    global $db;


    $new_name = trim(
        implode(" ", $args)
    );


    if ($new_name == '') {

        sendMessage(
            $chat_id, $thread_id,
            "❌ Использование: !имя ИгровоеИмя"
        );

        return;

    }


    // Если есть реплай - меняет руководство

    if (
        isset($message['reply_to_message'])
    ) {


        if (
            !checkAdmin(
                $chat_id, $thread_id,
                $from_id
            )
        ) {

            sendMessage(
                $chat_id, $thread_id,
                "❌ Только руководство может менять имена других."
            );

            return;

        }


        $target =
            getReplyUser($message);


        $stmt =
            $db->prepare("
                UPDATE members
                SET game_name=?
                WHERE user_id=?
            ");


        $stmt->execute(
            [
                $new_name,
                $target['id']
            ]
        );


        sendMessage(
            $chat_id, $thread_id,
            "✅ Игровое имя изменено: ".$new_name
        );


        return;

    }



    // Игрок меняет своё имя


    $stmt =
        $db->prepare("
            UPDATE members
            SET game_name=?
            WHERE user_id=?
        ");


    $stmt->execute(
        [
            $new_name,
            $from_id
        ]
    );


    sendMessage(
        $chat_id,
        $thread_id,
        "✅ Ваше игровое имя: ".$new_name
    );

}



// =====================================
// ОБРАБОТКА КОМАНД
// =====================================

function processCommand(
    $message
)
{
    
    $text =
    trim(
        $message['text'] ?? ''
    );


if (
    $text == '' ||
    $text[0] != '!' &&
    $text[0] != '/'
) {

    return;

}

    $text =
        trim(
            $message['text'] ?? ''
        );


    if (
        $text == ''
    ) return;



    $chat_id =
        $message['chat']['id'];
        
        
    $thread_id =
        $message['message_thread_id'] ?? null;



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
            ['/', '!'],
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
        
        case "имя":

    commandGameName(
        $chat_id, $thread_id,
        $from_id,
        $message,
        $args
    );

break;


        case "помощь":
        case "помощ":
        case "команды":
        case "комманды":

            commandHelp($chat_id, $thread_id);

        break;



        case "список":
        case "игроки":
        case "участники":
        case "клан":

            commandList($chat_id, $thread_id);

        break;



        case "звание":
        case "ранг":

            commandRank(
                $chat_id, $thread_id,
                $from_id,
                $message,
                $args
            );

        break;



        case "повысить":

            commandPromote(
                $chat_id, $thread_id,
                $from_id,
                $message
            );

        break;



        case "понизить":

            commandDemote(
                $chat_id, $thread_id,
                $from_id,
                $message
            );

        break;



        case "удалить":

            commandDelete(
                $chat_id, $thread_id,
                $from_id,
                $message
            );

        break;



        case "кв":

            commandKV(
                $chat_id, $thread_id,
                $from_id,
                $args
            );

        break;



        case "атакуем":

            commandAttack(
                $chat_id, $thread_id,
                $from_id
            );

        break;



        case "объявление":
        case "обьявление":

            commandAnnouncement(
                $chat_id, $thread_id,
                $from_id
            );

        break;


    }

}


?>