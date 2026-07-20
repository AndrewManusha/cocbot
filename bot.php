<?php

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