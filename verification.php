<?php


function verifyPlayerAccount(
    $telegram_id,
    $player_tag,
    $chat_id,
    $thread_id
)
{

    playerVerificationService()
        ->verify(
            $telegram_id,
            $player_tag,
            $chat_id,
            $thread_id
        );

}