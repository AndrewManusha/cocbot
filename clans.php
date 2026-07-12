<?php

require_once __DIR__ . '/database.php';

/**
 * Привести тег клана к единому виду.
 */
function normalizeClanTag($tag)
{
    $tag = strtoupper(trim($tag));
    $tag = str_replace('#', '', $tag);

    return $tag;
}

/**
 * Проверить, существует ли клан в базе.
 */
function clanExists($tag)
{
    global $db;

    $tag = normalizeClanTag($tag);

    $stmt = $db->prepare("
        SELECT 1
        FROM clans
        WHERE tag = ?
        LIMIT 1
    ");

    $stmt->execute([$tag]);

    return (bool)$stmt->fetchColumn();
}

/**
 * Добавить клан в базу.
 */
function addClan($clan)
{
    global $db;

    $stmt = $db->prepare("
        INSERT INTO clans
        (
            tag,
            name,
            level,
            members,
            war_league,
            capital_league,
            last_sync
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            NOW()
        )
    ");

    return $stmt->execute([
        normalizeClanTag($clan['tag']),
        $clan['name'],
        $clan['clanLevel'],
        $clan['members'],
        $clan['warLeague']['name'] ?? '',
        $clan['capitalLeague']['name'] ?? ''
    ]);
}