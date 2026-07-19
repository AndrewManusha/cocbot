<?php


return [


    // =====================================
    // СИНХРОНИЗАЦИЯ ИГРОКОВ КЛАНОВ
    // =====================================

    'clan_sync' => [

        'interval' => '5m',

        'handler' => function () {

            clanSyncService()
                ->syncAll();

        }

    ],



];