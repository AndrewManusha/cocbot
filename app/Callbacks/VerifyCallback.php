<?php


class VerifyCallback
{


    public function handle(
        array $callback
    ): void
    {

        $data =
            $callback['data'] ?? '';



        $playerTag =
            str_replace(
                'verify_',
                '',
                $data
            );



        $chatId =
            $callback['message']['chat']['id'];



        $threadId =
            $callback['message']['message_thread_id']
            ?? null;



        $telegramId =
            $callback['from']['id'];





        playerVerificationService()
            ->verify(
                $telegramId,
                $playerTag,
                $chatId,
                $threadId
            );





        answerCallback(
            $callback['id']
        );

    }


}