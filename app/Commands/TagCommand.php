<?php


class TagCommand
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



        $parts =
            explode(
                " ",
                trim(
                    $message['text']
                    ??
                    ''
                )
            );



        $args =
            array_slice(
                $parts,
                1
            );





        $tag =
            normalizeTag(
                implode(
                    " ",
                    $args
                )
            );





        if ($tag == '') {


            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Использование:\n!тег #TAG"
            );


            return;

        }







        if (
            !playerService()
                ->exists(
                    $tag
                )
        ) {


            $player =
                clashApi()
                    ->getPlayer(
                        $tag
                    );



            if (!$player) {


                sendMessage(
                    $chat_id,
                    $thread_id,
                    "❌ Игрок не найден."
                );


                return;

            }

        }







        if (
            playerService()
                ->isLinked(
                    $tag
                )
        ) {


            sendMessage(
                $chat_id,
                $thread_id,
                "⚠️ Этот аккаунт уже привязан."
            );


            return;

        }







        $labels =
            playerVerificationService()
                ->generateLabels();







        if (
            !playerVerificationService()
                ->create(
                    $from_id,
                    $tag,
                    $labels
                )
        ) {


            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Не удалось создать проверку."
            );


            return;

        }







        $text =
            "🔐 <b>Проверка аккаунта</b>\n\n";


        $text .=
            "Установите метки:\n\n";





        foreach (
            $labels['names']
            as $name
        ) {


            $text .=
                "🏷 "
                .
                htmlspecialchars(
                    $name
                )
                .
                "\n";


        }





        $text .=
            "\nПосле установки нажмите кнопку.";






        sendMessageWithButton(
            $chat_id,
            $thread_id,
            $text,
            "✅ Готово",
            "verify_" . $tag
        );


    }


}