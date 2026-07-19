<?php


class Router
{


    private CallbackHandler $callbackHandler;



    public function __construct()
    {

        $this->callbackHandler =
            new CallbackHandler();

    }




    public function handle(array $update): void
    {


        // CALLBACK QUERY

        if (
            isset($update['callback_query'])
        ) {


            $this->handleCallback(
                $update['callback_query']
            );


            return;

        }



        // MESSAGE

        if (
            isset($update['message'])
        ) {


            $this->handleMessage(
                $update['message']
            );


            return;

        }


    }





    private function handleCallback(
        array $callback
    ): void
    {

        $this->callbackHandler->handle(
            $callback
        );

    }






    private function handleMessage(
        array $message
    ): void
    {


        if (
            !isset(
                $message['from']
            )
        ) {

            return;

        }



        registerUser(
            $message['from']
        );



        commandRouter()
            ->handle(
                $message
            );


    }


}