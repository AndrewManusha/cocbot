<?php


class VerifyCallback
{


    public function handle(
        array $callback
    ): void
    {


        $data =
            $callback['data']
            ??
            '';





        if (
            !str_starts_with(
                $data,
                'verify_'
            )
        ) {

            return;

        }






        $playerTag =
            str_replace(
                'verify_',
                '',
                $data
            );





        $telegramId =
            $callback['from']['id'];









        playerVerificationService()
            ->verify(

                $telegramId,

                $playerTag

            );









        answerCallback(
            $callback['id']
        );


    }


}