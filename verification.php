<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/clash_api.php';
require_once __DIR__ . '/player_verifications.php';
require_once __DIR__ . '/user_players.php';




// =====================================
// ПРОВЕРКА АККАУНТА
// =====================================

function verifyPlayerAccount(
    $telegram_id,
    $player_tag,
    $chat_id,
    $thread_id
)
{

    $player_tag =
        normalizeTag($player_tag);


    // Получаем активную проверку

    $verification =
        getVerification(
            $player_tag
        );


    if (!$verification) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Проверка не найдена или истекла."
        );

        return;

    }


    // Проверяем, что проверка принадлежит этому пользователю

    if (
        $verification['telegram_id'] != $telegram_id
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Эта проверка принадлежит другому пользователю."
        );

        return;

    }


    // Получаем данные игрока

    $player =
        getPlayerFromApi(
            $player_tag
        );


    if (!$player) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Не удалось получить данные игрока."
        );

        return;

    }


    // Проверяем labels

    if (
        !checkPlayerVerificationLabels(
            $player,
            $verification['labels']
        )
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Метки пока не совпадают.\n\n" .
            "Если вы только что изменили их, подождите несколько минут и попробуйте снова."
        );

        return;

    }


    // Добавляем аккаунт

    if (
        !addUserPlayer(
            $telegram_id,
            $player_tag
        )
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Не удалось привязать аккаунт."
        );

        return;

    }


    // Удаляем проверку

    deleteVerification(
        $player_tag
    );


    sendMessage(
        $chat_id,
        $thread_id,
        "✅ Аккаунт успешно подтверждён!\n\n" .
        "🎮 Тег: <code>#" .
        htmlspecialchars($player_tag) .
        "</code>"
    );

}

?>