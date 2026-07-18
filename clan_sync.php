<?php


function syncAllClans()
{
    return clanSyncService()
        ->syncAll();
}



function syncClanPlayers($clanTag)
{
    return clanSyncService()
        ->syncClan($clanTag);
}