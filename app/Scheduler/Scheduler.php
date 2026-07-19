<?php


require_once __DIR__ . '/../bootstrap.php';



// =====================================
// ФАЙЛЫ
// =====================================

$lockFile =
    __DIR__ . '/scheduler.lock';



$stateFile =
    __DIR__ . '/scheduler_state.json';





// =====================================
// ЗАЩИТА ОТ ПАРАЛЛЕЛЬНОГО ЗАПУСКА
// =====================================

if (file_exists($lockFile)) {


    $lockTime =
        filemtime($lockFile);



    // если старше 5 минут - считаем зависшим

    if (
        time() - $lockTime < 300
    ) {

        exit;

    }


    unlink($lockFile);

}



file_put_contents(
    $lockFile,
    time()
);





try {


    writeLog(
        "Scheduler started"
    );





    // =====================================
    // ЗАГРУЗКА ЗАДАЧ
    // =====================================

    $tasks =
        require __DIR__ . '/Tasks.php';





    // =====================================
    // СОСТОЯНИЕ
    // =====================================

    $state = [];



    if (
        file_exists($stateFile)
    ) {


        $state =
            json_decode(
                file_get_contents($stateFile),
                true
            )
            ??
            [];

    }






    // =====================================
    // ПЕРЕВОД ИНТЕРВАЛОВ
    // =====================================

    function parseInterval(
        string $time
    ): int
    {


        preg_match(
            '/(\d+)([smhd])/',
            $time,
            $matches
        );



        if (!$matches) {

            return 0;

        }



        $value =
            (int)$matches[1];



        return match ($matches[2]) {


            's' =>
                $value,


            'm' =>
                $value * 60,


            'h' =>
                $value * 3600,


            'd' =>
                $value * 86400,


            default =>
                0

        };


    }







    // =====================================
    // ЗАПУСК ЗАДАЧ
    // =====================================

    $now =
        time();





    foreach (
        $tasks as $name => $task
    ) {



        $interval =
            parseInterval(
                $task['interval']
            );



        if (
            $interval <= 0
        ) {

            writeLog(
                "Invalid interval: " . $name
            );


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





            file_put_contents(
                $stateFile,
                json_encode(
                    $state,
                    JSON_PRETTY_PRINT
                )
            );





            writeLog(
                "Scheduler task completed: "
                .
                $name
            );


        }
        catch (Throwable $e) {


            writeLog(
                "Scheduler task error "
                .
                $name
                .
                ": "
                .
                $e->getMessage()
            );


        }



    }




}
catch (Throwable $e) {


    writeLog(
        "Scheduler fatal error: "
        .
        $e->getMessage()
    );


}
finally {


    unlink($lockFile);


}