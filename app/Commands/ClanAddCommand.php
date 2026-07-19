<?php


class ClanAddCommand
{


    public function handle(
        array $message,
        array $args
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
                "❌ Нет прав."
            );


            return;

        }







        if (
            empty($args[0])
        ) {


            sendMessage(
                $chat_id,
                $thread_id,
                "Использование:\n!клан добавить #TAG"
            );


            return;

        }







        $tag =
            normalizeTag(
                $args[0]
            );








        $result =
            clanService()
                ->addByTag(
                    $tag
                );








        sendMessage(
            $chat_id,
            $thread_id,
            $result['message']
        );


    }


}