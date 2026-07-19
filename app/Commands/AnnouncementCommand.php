<?php


class AnnouncementCommand
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


        $from_id =
            $message['from']['id'];





        if (
            !adminService()
                ->isAdmin(
                    $from_id
                )
        ) {


            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Недостаточно прав."
            );


            return;

        }







        $users =
            userService()
                ->all();





        if (!$users) {


            sendMessage(
                $chat_id,
                $thread_id,
                "📢 Пользователей нет."
            );


            return;

        }







        $mentions = "";





        foreach ($users as $user) {


            $mentions .=
                mentionUser($user)
                .
                " ";


        }







        sendMessage(
            $chat_id,
            $thread_id,
            "📢 <b>ОБЩЕЕ ОБЪЯВЛЕНИЕ</b>\n\n"
            .
            trim(
                $mentions
            )
        );


    }


}