<?php


class ClanListCommand
{


    public function handle(
        array $message
    ): void
    {


        $chat_id =
            $message['chat']['id'];


        $thread_id =
            $message['message_thread_id']
            ??
            null;





        $clans =
            clanService()
                ->all();





        if (!$clans) {


            sendMessage(
                $chat_id,
                $thread_id,
                "🏰 Кланы не добавлены."
            );


            return;

        }






        $text =
            "🏰 <b>КЛАНЫ</b>\n\n";






        foreach ($clans as $clan) {


            $text .=
                "🏰 <b>"
                .
                htmlspecialchars(
                    $clan['name']
                )
                .
                "</b>\n";



            $text .=
                "<code>#"
                .
                htmlspecialchars(
                    $clan['tag']
                )
                .
                "</code>\n\n";


        }







        sendMessage(
            $chat_id,
            $thread_id,
            $text
        );


    }


}