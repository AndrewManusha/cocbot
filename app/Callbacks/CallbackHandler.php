<?php


class CallbackHandler
{


    private VerifyCallback $verifyCallback;



    public function __construct()
    {

        $this->verifyCallback =
            new VerifyCallback();

    }





    public function handle(
        array $callback
    ): void
    {

        $data =
            $callback['data'] ?? '';



        if (
            str_starts_with(
                $data,
                'verify_'
            )
        ) {


            $this->verifyCallback->handle(
                $callback
            );


            return;

        }


    }


}