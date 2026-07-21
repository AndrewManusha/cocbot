<?php


class ClanSyncService
{


    // =====================================
    // СИНХРОНИЗАЦИЯ ВСЕХ КЛАНОВ
    // =====================================

    public function syncAll(): void
    {
        $clans =
            clanRepository()
                ->getTags();



        foreach ($clans as $tag) {

            $this->syncClan(
                $tag
            );

        }
    }



    // =====================================
    // СИНХРОНИЗАЦИЯ ОДНОГО КЛАНА
    // =====================================

    public function syncClan(
        string $tag
    ): bool
    {
        $tag =
            normalizeTag(
                $tag
            );


        if (
            $tag === ''
        ) {

            return false;
        }



        $clan =
            clashApi()
                ->getClan(
                    $tag
                );


        if (
            !$clan
        ) {

            return false;
        }



        /*
         * API возвращает:
         *
         * clan data
         * +
         * memberList
         *
         * Для hash используем только данные клана.
         * Участники меняются слишком часто.
         */


        $clan['tag'] =
            normalizeTag(
                $clan['tag']
            );



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



        // Сохраняем информацию о клане

        clanRepository()
            ->save(
                $clan,
                $hash
            );



        // Сохраняем участников

        $this->syncMembers(
            $clan
        );



        return true;
    }



    // =====================================
    // СИНХРОНИЗАЦИЯ УЧАСТНИКОВ
    // =====================================

    private function syncMembers(
        array $clan
    ): void
    {
        $members =
            $clan['memberList']
            ??
            [];



        foreach ($members as $player) {


            playerRepository()
                ->sync(
                    $player,
                    $clan['tag']
                );

        }
    }


}