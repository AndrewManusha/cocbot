<?php

require_once __DIR__ . '/../bootstrap.php';


// =====================================
// НАСТРОЙКИ
// =====================================

$lockFile =
    __DIR__ . '/scheduler.lock';



$stateFile =
    __DIR__ . '/scheduler_state.json';




// =====================================
// ЗАЩИТА ОТ ПАРАЛЛЕЛЬНОГО ЗАПУСКА
// =====================================

if (file_exists($lockFile)) {

    exit;

}


file_put_contents(
    $lockFile,
    time()
);




// =====================================
// ЗАГРУЗКА ЗАДАЧ
// =====================================

$tasks =
    require __DIR__ . '/Tasks.php';



$state = [];



if (file_exists($stateFile)) {

    $state =
        json_decode(
            file_get_contents($stateFile),
            true
        )
        ??
        [];

}




// =====================================
// ПЕРЕВОД 1m 10m 1h 1d В СЕКУНДЫ
// =====================================

function parseInterval(string $time): int
{

    $value =
        (int)substr(
            $time,
            0,
            -1
        );


    $unit =
        substr(
            $time,
            -1
        );



    return match ($unit) {

        'm' => $value * 60,

        'h' => $value * 3600,

        'd' => $value * 86400,

        default => 0

    };

}




// =====================================
// ВЫПОЛНЕНИЕ ЗАДАЧ
// =====================================

$now =
    time();



foreach ($tasks as $name => $task) {


    $interval =
        parseInterval(
            $task['interval']
        );



    if ($interval <= 0) {

        continue;

    }



    $lastRun =
        $state[$name]
        ??
        0;



    if (
        ($now - $lastRun)
        <
        $interval
    ) {

        continue;

    }



    try {


        call_user_func(
            $task['handler']
        );



        $state[$name] =
            $now;



        writeLog(
            "Scheduler task completed: " . $name
        );


    }
    catch (Throwable $e) {


        writeLog(
            "Scheduler error {$name}: "
            .
            $e->getMessage()
        );


    }


}




// =====================================
// СОХРАНЕНИЕ СОСТОЯНИЯ
// =====================================

file_put_contents(
    $stateFile,
    json_encode(
        $state,
        JSON_PRETTY_PRINT
    )
);




// =====================================
// СНЯТИЕ LOCK
// =====================================

unlink($lockFile);