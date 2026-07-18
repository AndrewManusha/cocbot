<?php


class ClanService
{

    private ClanRepository $clans;



    public function __construct()
    {

        $this->clans =
            clanRepository();

    }





    // =====================================
    // ПРОВЕРКА КЛАНА
    // =====================================

    public function exists(string $tag): bool
    {

        return $this->clans
            ->exists(
                $tag
            );

    }





    // =====================================
    // ДОБАВИТЬ КЛАН
    // =====================================

    public function add(array $clan): bool
    {

        return $this->clans
            ->create(
                $clan
            );

    }





    // =====================================
    // СПИСОК КЛАНОВ
    // =====================================

    public function all(): array
    {

        return $this->clans
            ->all();

    }

}