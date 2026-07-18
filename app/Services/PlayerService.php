<?php


class PlayerService
{

    private PlayerRepository $players;

    private UserPlayerRepository $userPlayers;



    public function __construct()
    {

        $this->players =
            playerRepository();


        $this->userPlayers =
            userPlayerRepository();

    }





    // Проверка существования игрока в базе

    public function exists(
        string $tag
    ): bool
    {

        return $this->players
            ->exists($tag);

    }





    // Получить аккаунты пользователя

    public function getUserPlayers(
        int $telegram_id
    ): array
    {

        return $this->userPlayers
            ->getByUser(
                $telegram_id
            );

    }





    // Получить главный аккаунт

    public function getMainPlayer(
        int $telegram_id
    ): ?array
    {

        return $this->userPlayers
            ->getMain(
                $telegram_id
            );

    }





    // Проверка привязки

    public function isLinked(
        string $tag
    ): bool
    {

        return $this->userPlayers
            ->exists(
                $tag
            );

    }





    // Привязать аккаунт

    public function link(
        int $telegram_id,
        string $tag
    ): bool
    {

        return $this->userPlayers
            ->create(
                $telegram_id,
                $tag
            );

    }





    // Сделать основным

    public function setMain(
        int $telegram_id,
        string $tag
    ): bool
    {

        return $this->userPlayers
            ->setMain(
                $telegram_id,
                $tag
            );

    }

}