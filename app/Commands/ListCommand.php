<?php


class ListCommand
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




        $text =
            "📋 <b>КЛАН</b>\n\n";





        $users =
            userService()
                ->all();



        $admins =
            userService()
                ->admins();



        $members =
            userService()
                ->members();






        $text .=
            "👑 <b>РУКОВОДИТЕЛИ</b>\n\n";




        if (!$admins) {


            $text .=
                "— нет\n\n";


        }
        else {


            foreach ($admins as $user) {


                $text .=
                    mentionUser($user)
                    .
                    "\n";


            }


            $text .=
                "\n";


        }







        $text .=
            "👥 <b>УЧАСТНИКИ</b>\n\n";






        if (!$members) {


            $text .=
                "— нет";


        }
        else {


            foreach ($members as $user) {


                $line =
                    mentionUser($user);




                $players =
                    playerService()
                        ->getUserPlayers(
                            $user['telegram_id']
                        );





                foreach ($players as $player) {


                    $prefix =
                        $player['is_main']
                        ?
                        "⭐"
                        :
                        "";



                    $line .=
                        " "
                        .
                        $prefix
                        .
                        "<code>#"
                        .
                        htmlspecialchars(
                            $player['player_tag']
                        )
                        .
                        "</code>";

                }





                $text .=
                    $line
                    .
                    "\n";


            }


        }





        sendLongMessage(
            $chat_id,
            $thread_id,
            $text
        );


    }


}