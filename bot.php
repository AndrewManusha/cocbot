<?php


$update = file_get_contents('php://input');

file_put_contents(
    __DIR__ . '/update_log.json',
    $update . PHP_EOL . PHP_EOL,
    FILE_APPEND
);


// BOOTSTRAP

require_once __DIR__ . '/app/bootstrap.php';



// UPDATE

$content =
    file_get_contents(
        "php://input"
    );


if (!$content) {

    exit;

}


$update =
    json_decode(
        $content,
        true
    );


if (!$update) {

    exit;

}



// ROUTER

router()->handle(
    $update
);