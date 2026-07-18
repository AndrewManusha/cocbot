<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/clash_api.php';




// =====================================
// ПРОВЕРКА АККАУНТА
// =====================================

function verifyPlayerAccount(
    int $telegram_id,
    string $player_tag,
    int $chat_id,
    ?int $thread_id = null
): void
{

    $player_tag =
        normalizeTag(
            $player_tag
        );



    $verification =
        verificationRepository()
            ->find(
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




    if (
        (int)$verification['telegram_id']
        !==
        $telegram_id
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Эта проверка принадлежит другому пользователю."
        );

        return;

    }




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




    $created =
        userPlayerRepository()
            ->create(
                $telegram_id,
                $player_tag
            );



    if (!$created) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Не удалось привязать аккаунт."
        );

        return;

    }




    verificationRepository()
        ->delete(
            $player_tag
        );





    sendMessage(
        $chat_id,
        $thread_id,
        "✅ Аккаунт успешно подтверждён!\n\n" .
        "🎮 Тег: <code>#" .
        htmlspecialchars(
            $player_tag
        ) .
        "</code>"
    );

}