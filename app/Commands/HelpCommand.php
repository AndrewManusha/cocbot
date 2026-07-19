<?php


class HelpCommand
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



        $text = "

📖 <b>Команды клана</b>


👥 Для всех:

!список

Показать состав клана


!тег #TAG

Привязать Clash аккаунт



👑 Руководство:

!клан добавить #TAG

Добавить клан


!объявление

Общее объявление

";



        sendMessage(
            $chat_id,
            $thread_id,
            $text
        );

    }


}