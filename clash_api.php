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

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $curlError = curl_error($ch);

    curl_close($ch);

    echo "URL: " . $url . "<br>";
    echo "HTTP CODE: " . $httpCode . "<br>";
    
    if ($curlError) {
        echo "CURL ERROR: " . $curlError;
    }

    echo "<pre>";
    print_r($response);
    echo "</pre>";

    if ($httpCode !== 200) {
        return false;
    }

    return json_decode($response, true);
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