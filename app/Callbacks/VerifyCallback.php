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



        $tag =
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
                $tag
            );



        answerCallback(
            $callback['id']
        );

    }


}