<?php


class TagCommand
{


    public function handle(array $message): void
    {

        $chatId = $message['chat']['id'];

        $threadId =
            $message['message_thread_id']
            ?? null;

        $telegramId =
            $message['from']['id'];



        // =====================================
        // ПОЛУЧАЕМ ВВЕДЕННЫЙ TAG
        // =====================================

        $parts =
            explode(
                ' ',
                trim($message['text'] ?? '')
            );

        $inputTag =
            normalizeTag(
                implode(' ', array_slice($parts, 1))
            );


        if ($inputTag === '') {

            sendMessage(
                $chatId,
                $threadId,
                "❌ Использование:\n!тег #TAG"
            );

            return;

        }



        // =====================================
        // ПОЛУЧАЕМ ИГРОКА ИЗ API
        // =====================================

        $player =
            clashApi()->getPlayer($inputTag);


        if (!$player) {

            sendMessage(
                $chatId,
                $threadId,
                "❌ Игрок с таким тегом не найден."
            );

            return;

        }



        // Настоящий tag берем только из API

        $tag =
            normalizeTag(
                $player['tag']
            );



        // =====================================
        // ПРОВЕРКА ПРИВЯЗКИ
        // =====================================

        if (
            userPlayerRepository()
                ->exists($tag)
        ) {

            sendMessage(
                $chatId,
                $threadId,
                "⚠️ Этот аккаунт уже привязан."
            );

            return;

        }



        // =====================================
        // СОЗДАЕМ ПРОВЕРКУ
        // =====================================

        $labels =
            playerVerificationService()
                ->generateLabels();


        if (
            !playerVerificationService()
                ->create(
                    $telegramId,
                    $tag,
                    $labels
                )
        ) {

            sendMessage(
                $chatId,
                $threadId,
                "❌ Не удалось создать проверку."
            );

            return;

        }



        // =====================================
        // СОЗДАЕМ СООБЩЕНИЕ ПРОВЕРКИ
        // =====================================

        $text =
            "🔐 <b>Проверка аккаунта</b>\n\n";

        $text .=
            "👤 Игрок: <b>"
            .
            htmlspecialchars($player['name'])
            .
            "</b>\n";

        $text .=
            "🎮 Тег: <code>#{$tag}</code>\n\n";

        $text .=
            "Установите следующие метки:\n\n";


        foreach ($labels['names'] as $name) {

            $text .=
                "🏷 "
                .
                htmlspecialchars($name)
                .
                "\n";

        }


        $text .=
            "\nПосле установки нажмите кнопку проверки.";



        $response =
            telegram()->sendMessage(
                $chatId,
                $threadId,
                $text,
                [
                    'reply_markup' => json_encode(
                        [
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => '✅ Проверить',
                                        'callback_data' => 'verify_' . $tag
                                    ]
                                ]
                            ]
                        ],
                        JSON_UNESCAPED_UNICODE
                    )
                ]
            );



        // =====================================
        // СОХРАНЯЕМ MESSAGE ID
        // =====================================

        if (
            isset($response['result']['message_id'])
        ) {

            playerVerificationService()
                ->setMessage(
                    $tag,
                    $chatId,
                    $response['result']['message_id']
                );

        }

    }


}