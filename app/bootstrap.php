<?php


require_once __DIR__ . '/../config.php';


// ===============================
// SERVICES
// ===============================

require_once __DIR__ . '/Services/TelegramService.php';


// ===============================
// MESSAGING
// ===============================

require_once __DIR__ . '/Messaging/Button.php';

require_once __DIR__ . '/Messaging/Keyboard.php';

require_once __DIR__ . '/Messaging/Message.php';




// ===============================
// SERVICES INSTANCES
// ===============================

$GLOBALS['telegram'] =
    new TelegramService();




// ===============================
// HELPERS
// ===============================

function telegram(): TelegramService
{
    return $GLOBALS['telegram'];
}



function message(): Message
{
    return new Message();
}