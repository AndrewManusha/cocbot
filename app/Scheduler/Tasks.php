<?php


return [

    // =====================================
    // СИНХРОНИЗАЦИЯ КЛАНОВ И ИГРОКОВ
    // =====================================

    'clan_sync' => [

        'interval' => '5m',

        'handler' => function () {

            clanSyncService()
                ->syncAll();

        }

    ],

];