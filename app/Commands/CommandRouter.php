<?php


class CommandRouter
{


    public function handle(
        array $message
    ): void
    {

        $text =
            trim(
                $message['text']
                ??
                ''
            );



        if (
            $text === ''
            ||
            (
                $text[0] !== '!'
                &&
                $text[0] !== '/'
            )
        ) {

            return;

        }





        $parts =
            explode(
                " ",
                $text
            );



        $command =
            mb_strtolower(
                str_replace(
                    ['!','/'],
                    '',
                    $parts[0]
                )
            );



        $args =
            array_slice(
                $parts,
                1
            );





        switch ($command) {


            case "помощь":

            case "команды":


                commandHelp(

                    $message['chat']['id'],

                    $message['message_thread_id']
                    ??
                    null

                );

            break;





            case "список":

            case "игроки":


                commandList(

                    $message['chat']['id'],

                    $message['message_thread_id']
                    ??
                    null

                );

            break;





            case "тег":


                commandTag(

                    $message['chat']['id'],

                    $message['message_thread_id']
                    ??
                    null,

                    $message['from']['id'],

                    $args

                );

            break;





            case "объявление":

            case "обьявление":


                commandAnnouncement(

                    $message['chat']['id'],

                    $message['message_thread_id']
                    ??
                    null,

                    $message['from']['id']

                );

            break;





            case "клан":



                if (

                    isset($args[0])

                    &&

                    mb_strtolower($args[0])

                    ==

                    "добавить"

                ) {


                    commandAddClan(

                        $message,

                        array_slice(

                            $args,

                            1

                        )

                    );


                }
                else {


                    commandClans(

                        $message['chat']['id'],

                        $message['message_thread_id']
                        ??
                        null

                    );


                }


            break;


        }


    }


}