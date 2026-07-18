<?php


// =====================================
// СОЗДАТЬ ПРОВЕРКУ
// =====================================

function createVerification(
    $telegram_id,
    $player_tag,
    $labels
)
{

    return verificationRepository()->create(
        $telegram_id,
        $player_tag,
        $labels
    );

}





// =====================================
// ПОЛУЧИТЬ ПРОВЕРКУ
// =====================================

function getVerification(
    $player_tag
)
{

    return verificationRepository()->find(
        $player_tag
    );

}





// =====================================
// УДАЛИТЬ ПРОВЕРКУ
// =====================================

function deleteVerification(
    $player_tag
)
{

    return verificationRepository()->delete(
        $player_tag
    );

}





// =====================================
// УДАЛЕНИЕ ПРОСРОЧЕННЫХ ПРОВЕРОК
// =====================================

function clearExpiredVerifications()
{

    return verificationRepository()->clearExpired();

}

?>