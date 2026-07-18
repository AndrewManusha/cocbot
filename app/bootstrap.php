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

require_once __DIR__ . '/Repositories/UserPlayerRepository.php';

require_once __DIR__ . '/Repositories/VerificationRepository.php';




// ===============================
// SERVICES
// ===============================

require_once __DIR__ . '/Services/TelegramService.php';

require_once __DIR__ . '/Services/ClanSyncService.php';

require_once __DIR__ . '/Services/PlayerVerificationService.php';

require_once __DIR__ . '/Services/UserService.php';




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



$GLOBALS['userPlayerRepository'] =
    new UserPlayerRepository();



$GLOBALS['verificationRepository'] =
    new VerificationRepository();



$GLOBALS['telegram'] =
    new TelegramService();



$GLOBALS['clanSyncService'] =
    new ClanSyncService();



$GLOBALS['playerVerificationService'] =
    new PlayerVerificationService();



$GLOBALS['userService'] =
    new UserService();



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



function userPlayerRepository(): UserPlayerRepository
{
    return $GLOBALS['userPlayerRepository'];
}



function verificationRepository(): VerificationRepository
{
    return $GLOBALS['verificationRepository'];
}




function telegram(): TelegramService
{
    return $GLOBALS['telegram'];
}



function clanSyncService(): ClanSyncService
{
    return $GLOBALS['clanSyncService'];
}



function playerVerificationService(): PlayerVerificationService
{
    return $GLOBALS['playerVerificationService'];
}



function userService(): UserService
{
    return $GLOBALS['userService'];
}




function message(): Message
{
    return new Message();
}




function router(): Router
{
    return $GLOBALS['router'];
}