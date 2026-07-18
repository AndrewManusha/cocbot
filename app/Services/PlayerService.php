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





    public function exists(string $tag): bool
    {

        return $this->players
            ->exists($tag);

    }





    public function getUserPlayers(
        int $telegram_id
    ): array
    {

        return $this->userPlayers
            ->getByUser(
                $telegram_id
            );

    }





    public function isLinked(
        string $tag
    ): bool
    {

        return $this->userPlayers
            ->exists(
                $tag
            );

    }





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

}