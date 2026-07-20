<?php

class PlayerVerificationService
{
    private VerificationRepository $verifications;
    private UserPlayerRepository $players;


    public function __construct()
    {
        $this->verifications =
            verificationRepository();

        $this->players =
            userPlayerRepository();
    }



    // =====================================
    // МЕТКИ ДЛЯ ПРОВЕРКИ
    // =====================================

    public function getVerificationLabels(): array
    {
        return [
            57000000 => 'Clan Wars',
            57000001 => 'Clan War League',
            57000002 => 'Trophy Pushing',
            57000003 => 'Friendly Wars',
            57000004 => 'Clan Games',
            57000005 => 'Builder Base',
            57000006 => 'Base Designing',
            57000007 => 'Farming',
            57000008 => 'Active Donator',
            57000009 => 'Active Daily',
            57000010 => 'Hungry Learner',
            57000011 => 'Friendly',
            57000012 => 'Talkative',
            57000013 => 'Teacher',
            57000014 => 'Competitive',
            57000015 => 'Veteran',
            57000016 => 'Newbie',
            57000017 => 'Amateur Attacker',
            57000018 => 'Clan Capital'
        ];
    }



    // =====================================
    // ГЕНЕРАЦИЯ МЕТОК
    // =====================================

    public function generateLabels(): array
    {
        $labels =
            $this->getVerificationLabels();


        $ids =
            array_rand(
                $labels,
                3
            );


        sort($ids);


        return [
            'ids' => $ids,

            'names' => [
                $labels[$ids[0]],
                $labels[$ids[1]],
                $labels[$ids[2]]
            ]
        ];
    }



    public function create(
        int $telegramId,
        string $tag,
        array $labels
    ): bool
    {
        return $this->verifications->create(
            $telegramId,
            $tag,
            $labels
        );
    }



    public function setMessage(
        string $tag,
        int $chatId,
        int $messageId
    ): bool
    {
        return $this->verifications->setMessage(
            $tag,
            $chatId,
            $messageId
        );
    }



    // =====================================
    // ПРОВЕРКА АККАУНТА
    // =====================================

    public function verify(
        int $telegramId,
        string $tag
    ): void
    {
        $tag =
            normalizeTag($tag);



        $verification =
            $this->verifications->find($tag);



        if (!$verification) {

            return;

        }



        // Чужая кнопка

        if (
            (int)$verification['telegram_id']
            !==
            $telegramId
        ) {

            return;

        }



        $player =
            clashApi()->getPlayer($tag);



        if (!$player) {

            $this->editMessage(
                $verification,
                "❌ Не удалось получить данные игрока."
            );

            return;

        }



        writeLog(
            "VERIFY TAG DB: " .
            $verification['player_tag']
        );

        writeLog(
            "VERIFY TAG API: " .
            $player['tag']
        );

        writeLog(
            "API LABELS: " .
            json_encode(
                $player['labels'] ?? []
            )
        );

        writeLog(
            "DB LABELS: " .
            $verification['labels']
        );



        if (
            !$this->checkLabels(
                $player,
                $verification['labels']
            )
        ) {

            $this->editFailed(
                $verification
            );

            return;

        }



        playerRepository()->sync(
            $player,
            CLAN_TAG
        );



        $this->players->create(
            $telegramId,
            normalizeTag($player['tag'])
        );



        $this->verifications->delete(
            $tag
        );



        telegram()->editMessage(
            $verification['chat_id'],
            $verification['message_id'],
            $this->successText($player),
            [
                'reply_markup' =>
                    json_encode(
                        [
                            'inline_keyboard' => []
                        ]
                    )
            ]
        );

    }



    // =====================================
    // ПРОВЕРКА LABELS
    // =====================================

    private function checkLabels(
        array $player,
        string $required
    ): bool
    {
        $current = [];


        foreach (
            $player['labels'] ?? []
            as $label
        ) {

            $current[] =
                (int)$label['id'];

        }



        $required =
            array_map(
                'intval',
                explode(',', $required)
            );



        foreach ($required as $id) {

            if (
                !in_array(
                    $id,
                    $current,
                    true
                )
            ) {

                return false;

            }

        }


        return true;
    }



    // =====================================
    // ОШИБКА МЕТОК
    // =====================================

    private function editFailed(
        array $verification
    ): void
    {
        telegram()->editMessage(
            $verification['chat_id'],
            $verification['message_id'],
            $this->failedText($verification),
            [
                'reply_markup' =>
                    json_encode(
                        [
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => '✅ Проверить',
                                        'callback_data' =>
                                            'verify_' .
                                            $verification['player_tag']
                                    ]
                                ]
                            ]
                        ]
                    )
            ]
        );
    }



    private function editMessage(
        array $verification,
        string $text
    ): void
    {
        telegram()->editMessage(
            $verification['chat_id'],
            $verification['message_id'],
            $text,
            [
                'reply_markup' =>
                    json_encode(
                        [
                            'inline_keyboard' => []
                        ]
                    )
            ]
        );
    }



    private function failedText(
        array $verification
    ): string
    {
        $labels =
            $this->getVerificationLabels();



        $text =
            "🔐 <b>Проверка аккаунта</b>\n\n";



        $text .=
            "🎮 Тег: <code>#"
            .
            $verification['player_tag']
            .
            "</code>\n\n";



        $text .=
            "Установите следующие метки:\n\n";



        foreach (
            explode(
                ',',
                $verification['labels']
            )
            as $id
        ) {

            $text .=
                "🏷 "
                .
                $labels[(int)$id]
                .
                "\n";

        }



        return $text .
            "\n❌ Информация ещё не обновилась или метки установлены неверно." .
            "\nПопробуйте снова через минуту.";
    }



    private function successText(
        array $player
    ): string
    {
        return
            "✅ <b>Аккаунт успешно привязан!</b>\n\n" .
            "👤 Игрок: <b>" .
            htmlspecialchars(
                $player['name']
            ) .
            "</b>\n" .
            "🎮 Тег: <code>#" .
            normalizeTag(
                $player['tag']
            ) .
            "</code>";
    }

}