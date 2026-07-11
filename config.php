<?php

// ===============================
// TELEGRAM BOT
// ===============================

define(
    'TOKEN',
    '8744255864:AAEF78ZYPIYCiipaIrEpKui1dZi6rAMwJAY'
);


define(
    'API_URL',
    'https://api.telegram.org/bot' . TOKEN . '/'
);


// ===============================
// MYSQL DATABASE
// ===============================

define('DB_HOST', 'localhost');

define('DB_NAME', 'a120585_cocbot');

define('DB_USER', 'a120585_cocbot_u');

define('DB_PASS', 'f9y1vzf3ggsgu19unk');

define(
    'DB_CHARSET',
    'utf8mb4'
);


// ===============================
// LOGS
// ===============================

define(
    'LOG_FILE',
    __DIR__ . '/bot.log'
);


// ===============================
// BOT SETTINGS
// ===============================

define(
    'DEFAULT_PARSE_MODE',
    'HTML'
);


define(
    'MAX_LIST_SIZE',
    100
);


?>