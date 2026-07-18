<?php


require_once __DIR__ . '/../config.php';


// ===============================
// DATABASE
// ===============================

require_once __DIR__ . '/Database/Database.php';


// ===============================
// REPOSITORIES
// ===============================

require_once __DIR__ . '/Repositories/UserRepository.php';

require_once __DIR__ . '/Repositories/PlayerRepository.php';

require_once __DIR__ . '/Repositories/AdminRepository.php';

require_once __DIR__ . '/Repositories/ClanRepository.php';




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
// CALLBACKS
// ===============================

require_once __DIR__ . '/Callbacks/VerifyCallback.php';

require_once __DIR__ . '/Callbacks/CallbackHandler.php';




// ===============================
// ROUTER
// ===============================

require_once __DIR__ . '/Router/Router.php';





// ===============================
// INSTANCES
// ===============================

$GLOBALS['database'] =
    new Database();



$GLOBALS['userRepository'] =
    new UserRepository();



$GLOBALS['playerRepository'] =
    new PlayerRepository();



$GLOBALS['adminRepository'] =
    new AdminRepository();



$GLOBALS['clanRepository'] =
    new ClanRepository();



$GLOBALS['telegram'] =
    new TelegramService();



$GLOBALS['router'] =
    new Router();






// ===============================
// HELPERS
// ===============================


function database(): Database
{
    return $GLOBALS['database'];
}




function userRepository(): UserRepository
{
    return $GLOBALS['userRepository'];
}




function playerRepository(): PlayerRepository
{
    return $GLOBALS['playerRepository'];
}



function adminRepository(): AdminRepository
{
    return $GLOBALS['adminRepository'];
}



function clanRepository(): ClanRepository
{
    return $GLOBALS['clanRepository'];
}




function telegram(): TelegramService
{
    return $GLOBALS['telegram'];
}




function message(): Message
{
    return new Message();
}




function router(): Router
{
    return $GLOBALS['router'];
}