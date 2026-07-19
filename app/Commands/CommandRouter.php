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


                (new HelpCommand())
                    ->handle(
                        $message
                    );


            break;





            case "список":

            case "игроки":


                (new ListCommand())
                    ->handle(
                        $message
                    );


            break;





            case "тег":


                (new TagCommand())
                    ->handle(
                        $message
                    );


            break;





            case "объявление":

            case "обьявление":


                (new AnnouncementCommand())
                    ->handle(
                        $message
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


                    (new ClanAddCommand())
                        ->handle(

                            $message,

                            array_slice(
                                $args,
                                1
                            )

                        );


                }
                else {


                    (new ClanListCommand())
                        ->handle(
                            $message
                        );


                }


            break;


        }


    }


}