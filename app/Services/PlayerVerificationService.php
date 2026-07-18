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