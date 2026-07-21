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
    // ПОЛУЧИТЬ КЛАН
    // =====================================

    public function find(
        string $tag
    ): ?array
    {
        return $this->clans
            ->find(
                normalizeTag($tag)
            );
    }



    // =====================================
    // СОХРАНИТЬ КЛАН
    // =====================================

    public function save(
        array $clan,
        string $hash
    ): bool
    {
        return $this->clans
            ->save(
                $clan,
                $hash
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


        if (
            $tag === ''
        ) {

            return [
                'success' => false,
                'message' =>
                    'Не указан тег клана.'
            ];

        }



        $clan =
            clashApi()
                ->getClan(
                    $tag
                );



        if (
            !$clan
        ) {

            return [
                'success' => false,
                'message' =>
                    '❌ Клан не найден.'
            ];

        }



        $clan['tag'] =
            normalizeTag(
                $clan['tag']
            );



        // Убираем участников из hash

        $hashData =
            $clan;


        unset(
            $hashData['memberList']
        );



        $hash =
            md5(
                json_encode(
                    $hashData
                )
            );



        if (
            !$this->save(
                $clan,
                $hash
            )
        ) {

            return [
                'success' => false,
                'message' =>
                    '❌ Ошибка сохранения.'
            ];

        }



        // Сохраняем участников

        foreach (
            $clan['memberList']
            ??
            []
            as $player
        ) {

            playerRepository()
                ->sync(
                    $player,
                    $clan['tag']
                );

        }



        return [
            'success' => true,

            'message' =>
                "✅ Клан сохранен:\n\n" .

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