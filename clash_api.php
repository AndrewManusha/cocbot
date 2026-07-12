<?php

require_once __DIR__ . '/config.php';

/**
 * Выполняет GET-запрос к Clash of Clans API.
 *
 * @param string $endpoint
 * @return array|false
 */
function clashApiRequest($endpoint)
{
    $url = 'https://api.clashofclans.com/v1/' . ltrim($endpoint, '/');

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . CLASH_API_TOKEN,
            'Accept: application/json'
        ]
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode !== 200) {
        return false;
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        return false;
    }

    return $data;
}

/**
 * Привести тег к стандартному виду.
 *
 * #abc123 -> ABC123
 *
 * @param string $tag
 * @return string
 */
function normalizeTag($tag)
{
    $tag = strtoupper(trim($tag));
    $tag = str_replace('#', '', $tag);

    return $tag;
}

/**
 * Получить информацию о клане.
 *
 * @param string $tag
 * @return array|false
 */
function getClanFromApi($tag)
{
    $tag = normalizeTag($tag);

    return clashApiRequest("clans/%23{$tag}");
}

/**
 * Получить информацию об игроке.
 *
 * @param string $tag
 * @return array|false
 */
function getPlayerFromApi($tag)
{
    $tag = normalizeTag($tag);

    return clashApiRequest("players/%23{$tag}");
}

/**
 * Получить текущую войну клана.
 *
 * @param string $tag
 * @return array|false
 */
function getCurrentWarFromApi($tag)
{
    $tag = normalizeTag($tag);

    return clashApiRequest("clans/%23{$tag}/currentwar");
}

/**
 * Получить участников клана.
 *
 * @param string $tag
 * @return array|false
 */
function getClanMembersFromApi($tag)
{
    $tag = normalizeTag($tag);

    return clashApiRequest("clans/%23{$tag}/members");
}

/**
 * Получить текущую КВЛ.
 *
 * @param string $tag
 * @return array|false
 */
function getCurrentWarLeagueGroupFromApi($tag)
{
    $tag = normalizeTag($tag);

    return clashApiRequest("clans/%23{$tag}/currentwar/leaguegroup");
}


// =====================================
// LABELS ДЛЯ ВЕРИФИКАЦИИ
// =====================================

function getVerificationLabels()
{

    return [

        57000000 => 'Clan Wars',
        57000001 => 'Clan War League',
        57000002 => 'Trophy Pushing',
        57000003 => 'Friendly Wars',
        57000004 => 'Clan Games',
        57000005 => 'Builder Base',
        57000006 => 'Base Designing',
        57000007 => 'Farming',
        57000008 => 'Active Donator',
        57000009 => 'Active Daily',
        57000010 => 'Hungry Learner',
        57000011 => 'Friendly',
        57000012 => 'Talkative',
        57000013 => 'Teacher',
        57000014 => 'Competitive',
        57000015 => 'Veteran',
        57000016 => 'Newbie',
        57000017 => 'Amateur Attacker',
        57000018 => 'Clan Capital'

    ];

}



// =====================================
// ГЕНЕРАЦИЯ 3 LABELS
// =====================================

function generateVerificationLabels()
{

    $labels = getVerificationLabels();


    $ids = array_rand(
        $labels,
        3
    );


    sort($ids);


    return [

        'ids' => $ids,

        'names' => [

            $labels[$ids[0]],

            $labels[$ids[1]],

            $labels[$ids[2]]

        ]

    ];

}



// =====================================
// ПОЛУЧИТЬ LABEL IDS ИГРОКА
// =====================================

function getPlayerLabelIds($player)
{

    $ids = [];


    if (
        empty($player['labels'])
    ) {

        return $ids;

    }


    foreach ($player['labels'] as $label) {

        $ids[] = (int)$label['id'];

    }


    sort($ids);


    return $ids;

}



// =====================================
// ПРОВЕРКА LABELS
// =====================================

function checkPlayerVerificationLabels(
    $player,
    $requiredLabels
)
{

    $current =
        getPlayerLabelIds($player);



    $required =
        explode(
            ',',
            $requiredLabels
        );


    $required =
        array_map(
            'intval',
            $required
        );


    sort($required);


    return $current === $required;

}