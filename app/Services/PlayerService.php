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
                normalizeTag($tag)
            );
    }



    // =====================================
    // ПОЛУЧИТЬ АККАУНТЫ ПОЛЬЗОВАТЕЛЯ
    // =====================================

    public function getUserPlayers(
        int $telegramId
    ): array
    {
        return $this->userPlayers
            ->getByUser(
                $telegramId
            );
    }



    // =====================================
    // ПОЛУЧИТЬ ОСНОВНОЙ АККАУНТ
    // =====================================

    public function getMainPlayer(
        int $telegramId
    ): ?array
    {
        return $this->userPlayers
            ->getMain(
                $telegramId
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
                normalizeTag($tag)
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
                normalizeTag($tag)
            );
    }



    // =====================================
    // ПРОВЕРКА ПРИНАДЛЕЖНОСТИ
    // =====================================

    public function belongsToUser(
        int $telegramId,
        string $tag
    ): bool
    {
        return $this->userPlayers
            ->belongsToUser(
                $telegramId,
                normalizeTag($tag)
            );
    }



    // =====================================
    // ПРИВЯЗАТЬ АККАУНТ
    // =====================================

    public function link(
        int $telegramId,
        string $tag
    ): bool
    {
        return $this->userPlayers
            ->create(
                $telegramId,
                normalizeTag($tag)
            );
    }



    // =====================================
    // СДЕЛАТЬ ОСНОВНЫМ
    // =====================================

    public function setMain(
        int $telegramId,
        string $tag
    ): bool
    {
        return $this->userPlayers
            ->setMain(
                $telegramId,
                normalizeTag($tag)
            );
    }



    // =====================================
    // УДАЛИТЬ АККАУНТ
    // =====================================

    public function unlink(
        int $telegramId,
        string $tag
    ): bool
    {
        return $this->userPlayers
            ->delete(
                $telegramId,
                normalizeTag($tag)
            );
    }


}