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

    public function exists(
        string $tag
    ): bool
    {

        return $this->clans
            ->exists(
                $tag
            );

    }





    // =====================================
    // ДОБАВЛЕНИЕ КЛАНА
    // =====================================

    public function add(
        array $clan
    ): bool
    {

        return $this->clans
            ->create(
                $clan
            );

    }





    // =====================================
    // ДОБАВИТЬ КЛАН ПО TAG
    // =====================================

    public function addByTag(
        string $tag
    ): array
    {

        $tag =
            normalizeTag(
                $tag
            );



        if (!$tag) {

            return [

                'success' => false,

                'message' =>
                    'Не указан тег клана.'

            ];

        }



        if (
            $this->exists(
                $tag
            )
        ) {

            return [

                'success' => false,

                'message' =>
                    '⚠️ Этот клан уже добавлен.'

            ];

        }



        $clan =
            clashApi()
                ->getClan(
                    $tag
                );



        if (!$clan) {

            return [

                'success' => false,

                'message' =>
                    '❌ Клан не найден.'

            ];

        }



        if (
            !$this->add(
                $clan
            )
        ) {

            return [

                'success' => false,

                'message' =>
                    '❌ Ошибка сохранения.'

            ];

        }



        return [

            'success' => true,

            'message' =>
                "✅ Клан добавлен:\n\n" .
                "🏰 {$clan['name']}\n" .
                "🎯 Уровень: {$clan['clanLevel']}\n" .
                "👥 Участников: {$clan['members']}"

        ];

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