<?php

require_once __DIR__ . '/config.php';


// ===============================
// ПОДКЛЮЧЕНИЕ SQLITE
// ===============================

try {

    $db = new PDO(
        'sqlite:' . DB_FILE
    );

    $db->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $db->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );


} catch (PDOException $e) {

    file_put_contents(
        LOG_FILE,
        date('d.m.Y H:i:s') .
        " DATABASE ERROR: " .
        $e->getMessage() .
        PHP_EOL,
        FILE_APPEND
    );

    exit;

}



// ===============================
// СОЗДАНИЕ ТАБЛИЦЫ УЧАСТНИКОВ
// ===============================

$db->exec("

CREATE TABLE IF NOT EXISTS members (

    user_id INTEGER PRIMARY KEY,

    username TEXT NOT NULL,

    rank INTEGER DEFAULT 2,

    kv_status TEXT DEFAULT 'да',

    kv_reason TEXT DEFAULT '',

    joined_at TEXT DEFAULT CURRENT_TIMESTAMP,

    last_seen TEXT DEFAULT CURRENT_TIMESTAMP

)

");



// ===============================
// ТАБЛИЦА ЛОГОВ
// ===============================

$db->exec("

CREATE TABLE IF NOT EXISTS logs (

    id INTEGER PRIMARY KEY AUTOINCREMENT,

    user_id INTEGER,

    username TEXT,

    action TEXT,

    created_at TEXT DEFAULT CURRENT_TIMESTAMP

)

");



// ===============================
// СОЗДАНИЕ НАСТРОЕК
// ===============================

$db->exec("

CREATE TABLE IF NOT EXISTS settings (

    name TEXT PRIMARY KEY,

    value TEXT

)

");



// ===============================
// ДОБАВЛЕНИЕ ПЕРВОГО АДМИНА
// ===============================
//
// После установки сюда можно добавить
// свой Telegram ID.
//
// Например:
// UPDATE members SET rank=5 WHERE user_id=123456;
//
//



?>