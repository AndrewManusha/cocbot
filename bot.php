<?php


file_put_contents(
    __DIR__ . '/alive.txt',
    date('H:i:s') . " работает\n",
    FILE_APPEND
);


// BOOTSTRAP

require_once __DIR__ . '/app/bootstrap.php';




// HELPERS

require_once __DIR__ . '/helpers.php';


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