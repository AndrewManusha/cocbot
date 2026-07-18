<?php


// =====================================
// PLAYER VERIFICATION FUNCTIONS
// =====================================


function createVerification(
    $telegram_id,
    $player_tag,
    $labels
)
{

    return verificationRepository()
        ->create(
            $telegram_id,
            $player_tag,
            $labels
        );

}




function getVerification(
    $player_tag
)
{

    return verificationRepository()
        ->find(
            $player_tag
        );

}




function deleteVerification(
    $player_tag
)
{

    return verificationRepository()
        ->delete(
            $player_tag
        );

}




function clearExpiredVerifications()
{

    return verificationRepository()
        ->clearExpired();

}