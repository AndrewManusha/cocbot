<?php


class Router
{


    public function handle(array $update): void
    {


        // CALLBACK QUERY

        if (
            isset($update['callback_query'])
        ) {

            $this->handleCallback(
                $update['callback_query']
            );

            return;

        }



        // MESSAGE

        if (
            isset($update['message'])
        ) {

            $this->handleMessage(
                $update['message']
            );

            return;

        }


    }




    private function handleCallback(array $callback): void
    {


        $data =
            $callback['data'] ?? '';



        if (
            str_starts_with(
                $data,
                'verify_'
            )
        ) {


            $playerTag =
                str_replace(
                    'verify_',
                    '',
                    $data
                );



            $chat_id =
                $callback['message']['chat']['id'];



            $thread_id =
                $callback['message']['message_thread_id']
                ?? null;



            $telegram_id =
                $callback['from']['id'];



            verifyPlayerAccount(
                $telegram_id,
                $playerTag,
                $chat_id,
                $thread_id
            );



            answerCallback(
                $callback['id']
            );


        }


    }





    private function handleMessage(array $message): void
    {


        if (
            !isset(
                $message['from']
            )
        ) {

            return;

        }



        registerUser(
            $message['from']
        );



        processCommand(
            $message
        );


    }


}