<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/clash_api.php';




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
        normalizeTag(
            $player_tag
        );



    // Получаем активную проверку

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




    // Проверяем владельца проверки

    if (
        $verification['telegram_id']
        !=
        $telegram_id
    ) {

        sendMessage(
            $chat_id,
            $thread_id,
            "❌ Эта проверка принадлежит другому пользователю."
        );

        return;

    }




    // Получаем игрока из API

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
        !userPlayerRepository()
            ->create(
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
        )
        .
        "</code>"
    );

}

?>