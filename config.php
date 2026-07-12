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


define(
    'CLASH_API_TOKEN',
    'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjQxNWU1MzNlLTI2NDEtNGNlOS1iZTI0LWNmODAzNDZhNWM0YSIsImlhdCI6MTc4Mzg2NTYwOCwic3ViIjoiZGV2ZWxvcGVyL2YyODU2ZjhkLThhZWQtM2JmNS04ZDZmLTZmZjg2ZjA4NGQ0ZSIsInNjb3BlcyI6WyJjbGFzaCJdLCJsaW1pdHMiOlt7InRpZXIiOiJkZXZlbG9wZXIvc2lsdmVyIiwidHlwZSI6InRocm90dGxpbmcifSx7ImNpZHJzIjpbIjUuMTg3LjYuNDUiXSwidHlwZSI6ImNsaWVudCJ9XX0.JtzfgB1sYVp6cpTVRGLqUGIfRRKmO-AUX2soT5VueE-vsbOuoB3a5XvkBm8XxSVQrhmHTmbsejUQZLhqEthakA'
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