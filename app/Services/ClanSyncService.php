<?php


class ClanSyncService
{


    public function syncAll(): void
    {

        $clans =
            clanRepository()
                ->getTags();



        foreach ($clans as $clan) {

            $this->syncClan(
                $clan['tag']
            );

        }

    }




    public function syncClan(
        string $clanTag
    ): bool
    {


        $members =
            getClanMembersFromApi(
                $clanTag
            );



        if (
            !$members
            ||
            !isset($members['items'])
        ) {

            return false;

        }




        foreach ($members['items'] as $player) {


            playerRepository()
                ->sync(
                    $player,
                    $clanTag
                );


        }


        return true;

    }


}