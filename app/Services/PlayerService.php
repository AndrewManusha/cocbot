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





    // =====================================
    // ПРОВЕРКА СУЩЕСТВОВАНИЯ ИГРОКА
    // =====================================

    public function exists(
        string $tag
    ): bool
    {

        return $this->players
            ->exists(
                $tag
            );

    }





    // =====================================
    // ПОЛУЧИТЬ ВСЕ АККАУНТЫ ПОЛЬЗОВАТЕЛЯ
    // =====================================

    public function getUserPlayers(
        int $telegram_id
    ): array
    {

        return $this->userPlayers
            ->getByUser(
                $telegram_id
            );

    }





    // =====================================
    // ПОЛУЧИТЬ ГЛАВНЫЙ АККАУНТ
    // =====================================

    public function getMainPlayer(
        int $telegram_id
    ): ?array
    {

        return $this->userPlayers
            ->getMain(
                $telegram_id
            );

    }





    // =====================================
    // ПОЛУЧИТЬ АККАУНТ ПО TAG
    // =====================================

    public function getPlayerByTag(
        string $tag
    ): ?array
    {

        return $this->userPlayers
            ->getByTag(
                $tag
            );

    }





    // =====================================
    // ПРОВЕРКА ПРИВЯЗКИ
    // =====================================

    public function isLinked(
        string $tag
    ): bool
    {

        return $this->userPlayers
            ->exists(
                $tag
            );

    }





    // =====================================
    // ПРОВЕРКА ПРИНАДЛЕЖНОСТИ
    // =====================================

    public function belongsToUser(
        int $telegram_id,
        string $tag
    ): bool
    {

        return $this->userPlayers
            ->belongsToUser(
                $telegram_id,
                $tag
            );

    }





    // =====================================
    // ПРИВЯЗАТЬ АККАУНТ
    // =====================================

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





    // =====================================
    // СДЕЛАТЬ ОСНОВНЫМ
    // =====================================

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





    // =====================================
    // УДАЛИТЬ АККАУНТ
    // =====================================

    public function unlink(
        int $telegram_id,
        string $tag
    ): bool
    {

        return $this->userPlayers
            ->delete(
                $telegram_id,
                $tag
            );

    }


}