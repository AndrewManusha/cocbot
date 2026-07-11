<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';


// =====================================
// ПОЛУЧИТЬ РАНГ ПОЛЬЗОВАТЕЛЯ
// =====================================

function getUserRank($user_id)
{

    global $db;


    $stmt = $db->prepare("

        SELECT rank

        FROM members

        WHERE user_id=?

    ");


    $stmt->execute(
        [
            $user_id
        ]
    );


    $rank = $stmt->fetchColumn();


    return $rank ?: 2;

}



// =====================================
// ПРОВЕРКА РУКОВОДСТВА
// =====================================

function isLeader($user_id)
{

    return getUserRank($user_id) >= 4;

}



// =====================================
// ПРОВЕРКА ПОЛНОГО АДМИНА
// =====================================

function isOwner($user_id)
{

    return getUserRank($user_id) >= 5;

}



// =====================================
// МОЖЕТ ЛИ МЕНЯТЬ ЧЕЛОВЕКА
// =====================================

function canManageUser($from_id, $target_id)
{

    $admin = getUser($from_id);
    $target = getUser($target_id);


    if (!$admin || !$target) {
        return false;
    }


    // нельзя менять самого себя
    if ($from_id == $target_id) {
        return false;
    }


    // владелец 6 может менять всех кроме себя
    if ($admin['rank'] == 6) {

        return true;

    }


    // нельзя менять владельца
    if ($target['rank'] == 6) {

        return false;

    }


    // нельзя менять равных и выше
    if ($admin['rank'] <= $target['rank']) {

        return false;

    }


    // соруки и руководители могут менять только ниже себя
    return true;

}



// =====================================
// МОЖНО ЛИ ПОВЫСИТЬ
// =====================================

function canPromote(
    $admin_id,
    $target_id
)
{

    return canManageUser(
        $admin_id,
        $target_id
    );

}



// =====================================
// МОЖНО ЛИ ПОНИЗИТЬ
// =====================================

function canDemote(
    $admin_id,
    $target_id
)
{

    return canManageUser(
        $admin_id,
        $target_id
    );

}



// =====================================
// МОЖЕТ ЛИ ИСПОЛЬЗОВАТЬ КОМАНДУ КВ
// =====================================

function canUseKVCommand($user_id)
{

    $rank =
        getUserRank($user_id);


    global $RANKS;


    return $RANKS[$rank]['kv'];

}



// =====================================
// МОЖЕТ ЛИ ИСПОЛЬЗОВАТЬ АДМИН-КОМАНДЫ
// =====================================

function checkAdmin(
    $chat_id,
    $user_id
)
{

    if (!isLeader($user_id)) {


        sendMessage(

            $chat_id, $thread_id,

            "❌ Недостаточно прав."

        );


        return false;

    }


    return true;

}



// =====================================
// ПРОВЕРКА ЧТО ЕСТЬ REPLY
// =====================================

function requireReply(
    $chat_id,
    $message
)
{

    if (
        !isset(
            $message['reply_to_message']
        )
    ) {


        sendMessage(

            $chat_id, $thread_id,

            "❌ Используйте команду ответом на сообщение пользователя."

        );


        return false;

    }


    return true;

}


?>