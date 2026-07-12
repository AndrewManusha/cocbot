<?php

require_once __DIR__ . '/database.php';


// =====================================
// СОЗДАТЬ ПРОВЕРКУ
// =====================================

function createVerification(
    $telegram_id,
    $player_tag,
    $labels
)
{
    global $db;


    if (is_array($labels)) {

        $labels = implode(
            ',',
            $labels['ids']
        );

    }


    $stmt = $db->prepare("

        INSERT INTO player_verifications
        (
            player_tag,
            telegram_id,
            labels,
            created_at,
            expires_at
        )

        VALUES
        (
            ?,
            ?,
            ?,
            NOW(),
            DATE_ADD(NOW(), INTERVAL 5 MINUTE)
        )

        ON DUPLICATE KEY UPDATE

            telegram_id = VALUES(telegram_id),
            labels = VALUES(labels),
            created_at = NOW(),
            expires_at = DATE_ADD(NOW(), INTERVAL 5 MINUTE)

    ");


    return $stmt->execute([

        $player_tag,

        $telegram_id,

        $labels

    ]);

}




// =====================================
// ПОЛУЧИТЬ ПРОВЕРКУ
// =====================================

function getVerification($player_tag)
{
    global $db;


    $stmt = $db->prepare("

        SELECT *

        FROM player_verifications

        WHERE player_tag = ?
        AND expires_at >= NOW()

        LIMIT 1

    ");


    $stmt->execute([

        $player_tag

    ]);


    return $stmt->fetch();

}




// =====================================
// УДАЛИТЬ ПРОВЕРКУ
// =====================================

function deleteVerification($player_tag)
{
    global $db;


    $stmt = $db->prepare("

        DELETE FROM player_verifications

        WHERE player_tag = ?

    ");


    return $stmt->execute([

        $player_tag

    ]);

}




// =====================================
// УДАЛЕНИЕ ПРОСРОЧЕННЫХ ПРОВЕРОК
// =====================================

function clearExpiredVerifications()
{
    global $db;


    $stmt = $db->prepare("

        DELETE FROM player_verifications

        WHERE expires_at < NOW()

    ");


    return $stmt->execute();

}

?>