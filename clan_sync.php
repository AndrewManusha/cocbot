<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/clash_api.php';



/**
 * Синхронизация всех кланов
 */
function syncAllClans()
{
    global $db;


    $stmt = $db->query("
        SELECT tag
        FROM clans
    ");


    $clans = $stmt->fetchAll();


    foreach ($clans as $clan) {

        syncClanPlayers($clan['tag']);

    }

}



/**
 * Синхронизация участников одного клана
 */
function syncClanPlayers($clanTag)
{

    global $db;


    $members = getClanMembersFromApi($clanTag);


    if (!$members || !isset($members['items'])) {

        return false;

    }



    foreach ($members['items'] as $player) {


        $stmt = $db->prepare("

            INSERT INTO players
            (
                player_tag,
                clan_tag,
                name,
                role,
                league,
                town_hall_level,
                exp_level,
                war_stars,
                donations,
                donations_received,
                clan_capital_contributions,
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
                ?,
                ?,
                ?,
                ?,
                ?,
                NOW()
            )


            ON DUPLICATE KEY UPDATE

                clan_tag = VALUES(clan_tag),
                name = VALUES(name),
                role = VALUES(role),
                league = VALUES(league),
                town_hall_level = VALUES(town_hall_level),
                exp_level = VALUES(exp_level),
                war_stars = VALUES(war_stars),
                donations = VALUES(donations),
                donations_received = VALUES(donations_received),
                clan_capital_contributions = VALUES(clan_capital_contributions),
                last_sync = NOW()

        ");



        $stmt->execute([

            normalizeTag($player['tag']),

            $clanTag,

            $player['name'],

            $player['role'] ?? '',

            $player['leagueTier']['name'] ?? '',

            $player['townHallLevel'] ?? 0,

            $player['expLevel'] ?? 0,

            $player['warStars'] ?? 0,

            $player['donations'] ?? 0,

            $player['donationsReceived'] ?? 0,

            $player['clanCapitalContributions'] ?? 0

        ]);

    }


    return true;

}