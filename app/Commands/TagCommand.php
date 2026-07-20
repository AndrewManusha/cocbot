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





        $inputTag =
            normalizeTag(
                implode(
                    " ",
                    $args
                )
            );





        if ($inputTag === '') {


            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Использование:\n!тег #TAG"
            );


            return;

        }









        /*
        =====================================
        ПОЛУЧАЕМ ИГРОКА ЧЕРЕЗ API
        =====================================
        */


        $player =
            clashApi()
                ->getPlayer(
                    $inputTag
                );





        if (!$player) {


            sendMessage(
                $chat_id,
                $thread_id,
                "❌ Игрок с таким тегом не найден."
            );


            return;

        }









        /*
        =====================================
        БЕРЕМ НАСТОЯЩИЙ TAG ИЗ API
        =====================================
        */


        $tag =
            normalizeTag(
                $player['tag']
            );









        /*
        =====================================
        ПРОВЕРКА ПРИВЯЗКИ
        =====================================
        */


        if (
            userPlayerRepository()
                ->exists(
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









        /*
        =====================================
        ГЕНЕРАЦИЯ LABELS
        =====================================
        */


        $labels =
            playerVerificationService()
                ->generateLabels();









        /*
        =====================================
        СОЗДАНИЕ ПРОВЕРКИ
        =====================================
        */


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









        /*
        =====================================
        ТЕКСТ ПРОВЕРКИ
        =====================================
        */


        $text =
            "🔐 <b>Проверка аккаунта</b>\n\n";



        $text .=
            "👤 Игрок: <b>"
            .
            htmlspecialchars(
                $player['name']
            )
            .
            "</b>\n";



        $text .=
            "🎮 Тег: <code>#"
            .
            htmlspecialchars(
                $tag
            )
            .
            "</code>\n\n";



        $text .=
            "Установите следующие метки:\n\n";





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
            "\nПосле установки нажмите кнопку проверки.";









        /*
        =====================================
        ОТПРАВКА СООБЩЕНИЯ
        =====================================
        */


        $response =
            telegram()
                ->sendMessage(
                    $chat_id,
                    $thread_id,
                    $text,
                    [

                        'reply_markup' =>

                            json_encode(

                                [

                                    'inline_keyboard' =>

                                        [

                                            [

                                                [

                                                    'text' =>
                                                        '✅ Проверить',

                                                    'callback_data' =>
                                                        'verify_' . $tag

                                                ]

                                            ]

                                        ]

                                ],

                                JSON_UNESCAPED_UNICODE

                            )

                    ]
                );









        /*
        =====================================
        СОХРАНЯЕМ MESSAGE ID
        =====================================
        */


        if (
            isset(
                $response['result']['message_id']
            )
        ) {


            playerVerificationService()
                ->setMessage(

                    $tag,

                    $chat_id,

                    $response['result']['message_id']

                );

        }


    }


}