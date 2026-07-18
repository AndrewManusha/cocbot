<?php


// =====================================
// CLAN SERVICE FUNCTIONS
// =====================================


function normalizeClanTag($tag)
{
    return clanRepository()
        ->normalizeTag($tag);
}




function clanExists($tag)
{
    return clanRepository()
        ->exists($tag);
}




function addClan($clan)
{
    return clanRepository()
        ->create($clan);
}




function getClans()
{
    return clanRepository()
        ->all();
}