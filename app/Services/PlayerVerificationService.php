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
    // LABELS ДЛЯ ВЕРИФИКАЦИИ
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
    // ГЕНЕРАЦИЯ 3 LABELS
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





    // =====================================
    // ПОЛУЧИТЬ LABEL IDS ИГРОКА
    // =====================================

    public function getPlayerLabelIds(
        array $player
    ): array
    {

        $ids = [];



        if (
            empty($player['labels'])
        ) {

            return $ids;

        }



        foreach ($player['labels'] as $label) {

            $ids[] =
                (int)$label['id'];

        }



        sort($ids);



        return $ids;

    }





    // =====================================
    // ПРОВЕРКА LABELS
    // =====================================

    public function checkLabels(
        array $player,
        string $requiredLabels
    ): bool
    {

        $current =
            $this->getPlayerLabelIds(
                $player
            );



        $required =
            explode(
                ',',
                $requiredLabels
            );



        $required =
            array_map(
                'intval',
                $required
            );



        sort($required);



        return $current === $required;

    }





    // =====================================
    // СОЗДАНИЕ ПРОВЕРКИ
    // =====================================

    public function create(
        int $telegram_id,
        string $player_tag,
        array $labels
    ): bool
    {

        return $this->verifications
            ->create(
                $telegram_id,
                $player_tag,
                $labels
            );

    }





    // =====================================
    // ПРОВЕРКА АККАУНТА
    // =====================================

    public function verify(
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
            $this->verifications
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
            clashApi()
                ->getPlayer(
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
            !$this->checkLabels(
                $player,
                $verification['labels']
            )
        ) {

            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Метки пока не совпадают."
            );

            return;

        }





        if (
            !$this->players
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





        $this->verifications
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

}