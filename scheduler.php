<?php


require_once __DIR__ . '/app/bootstrap.php';



writeLog(
    "Scheduler started"
);



try {


    clanSyncService()
        ->syncAll();



    writeLog(
        "Clan sync completed"
    );


}
catch (Throwable $e) {


    writeLog(
        "Scheduler error: "
        .
        $e->getMessage()
    );


}