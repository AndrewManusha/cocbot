<?php

class PlayerVerificationService
{
    private VerificationRepository $verifications;
    private UserPlayerRepository $players;


    public function __construct()
    {
        $this->verifications = verificationRepository();
        $this->players = userPlayerRepository();
    }



    // =====================================
    // СПИСОК ДОСТУПНЫХ МЕТОК
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
        $labels = $this->getVerificationLabels();

        $ids = array_rand($labels, 3);

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



    // =====================================
    // СОЗДАНИЕ ПРОВЕРКИ
    // =====================================

    public function create(
        int $telegramId,
        string $tag,
        array $labels
    ): bool
    {
        return $this->verifications->create(
            $telegramId,
            normalizeTag($tag),
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
            normalizeTag($tag),
            $chatId,
            $messageId
        );
    }



    // =====================================
    // ПРОВЕРКА CALLBACK
    // =====================================

    public function verify(
        int $telegramId,
        string $tag
    ): void
    {
        $tag = normalizeTag($tag);


        $verification =
            $this->verifications->find($tag);


        if (!$verification) {
            return;
        }



        // Защита от чужих нажатий

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

            $this->editStatus(
                $verification,
                "❌ Не удалось получить данные игрока."
            );

            return;
        }



        // DEBUG LOG

        writeLog(
            "VERIFY TAG DB: " . $tag
        );

        writeLog(
            "VERIFY TAG API: " .
            ($player['tag'] ?? 'NULL')
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

            $this->editStatus(
                $verification,
                "❌ Информация ещё не обновилась или метки установлены неверно.\nПопробуйте снова через минуту."
            );

            return;
        }



        $this->complete(
            $verification,
            $telegramId,
            $player
        );
    }



    // =====================================
    // ПРОВЕРКА МЕТОК
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
                explode(
                    ',',
                    $required
                )
            );


        sort($current);
        sort($required);



        return $current === $required;
    }



        // =====================================
    // УСПЕШНАЯ ПРИВЯЗКА
    // =====================================

    private function complete(
        array $verification,
        int $telegramId,
        array $player
    ): void
    {
        // Берем клан напрямую из ответа Clash API

        $clanTag =
            !empty($player['clan']['tag'])
                ? normalizeTag($player['clan']['tag'])
                : null;



        playerRepository()->sync(
            $player,
            $clanTag
        );


        $playerTag =
            normalizeTag(
                $player['tag']
            );



        $this->players->create(
            $telegramId,
            $playerTag
        );



        $this->verifications->delete(
            $playerTag
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
                        ],
                        JSON_UNESCAPED_UNICODE
                    )
            ]
        );
    }



    // =====================================
    // ИЗМЕНЕНИЕ СООБЩЕНИЯ ПРОВЕРКИ
    // =====================================

    private function editStatus(
        array $verification,
        string $status
    ): void
    {
        $labels =
            $this->getVerificationLabels();



        $text =
            "🔐 <b>Проверка аккаунта</b>\n\n";



        /*
         * Имя и тег берем из API только если
         * удалось получить игрока.
         * Сам тег из БД всегда нормализован.
         */

        $player =
            clashApi()->getPlayer(
                $verification['player_tag']
            );



        if ($player) {

            $text .=
                "👤 Игрок: <b>" .
                htmlspecialchars(
                    $player['name']
                ) .
                "</b>\n";



            $text .=
                "🎮 Тег: <code>#" .
                normalizeTag(
                    $player['tag']
                ) .
                "</code>\n\n";

        } else {

            $text .=
                "🎮 Тег: <code>#" .
                normalizeTag(
                    $verification['player_tag']
                ) .
                "</code>\n\n";
        }



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
                "🏷 " .
                (
                    $labels[(int)$id]
                    ??
                    $id
                ) .
                "\n";
        }



        $text .=
            "\n" .
            $status;



        telegram()->editMessage(
            $verification['chat_id'],
            $verification['message_id'],
            $text,
            [
                'reply_markup' =>
                    $this->button(
                        $verification['player_tag']
                    )
            ]
        );
    }



    // =====================================
    // УСПЕШНЫЙ ТЕКСТ
    // =====================================

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



    // =====================================
    // КНОПКА ПРОВЕРКИ
    // =====================================

    private function button(
        string $tag
    ): string
    {
        return json_encode(
            [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '✅ Проверить',
                            'callback_data' =>
                                'verify_' .
                                normalizeTag($tag)
                        ]
                    ]
                ]
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
}