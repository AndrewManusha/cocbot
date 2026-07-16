<?php

class Message
{

    private int|string|null $chatId = null;

    private ?int $threadId = null;

    private string $text = '';

    private ?Keyboard $keyboard = null;



    public function chat(
        int|string $chatId
    ): self
    {

        $this->chatId =
            $chatId;


        return $this;

    }



    public function thread(
        ?int $threadId
    ): self
    {

        $this->threadId =
            $threadId;


        return $this;

    }




    public function text(
        string $text
    ): self
    {

        $this->text =
            $text;


        return $this;

    }




    public function button(
        string $text,
        string $callbackData
    ): self
    {

        if ($this->keyboard === null) {

            $this->keyboard =
                new Keyboard();

        }


        $this->keyboard->addButton(

            new Button(
                $text,
                $callbackData
            )

        );


        return $this;

    }




    public function keyboard(
        Keyboard $keyboard
    ): self
    {

        $this->keyboard =
            $keyboard;


        return $this;

    }





    public function send(): array
    {

        $options = [];



        if ($this->keyboard !== null) {

            $options['reply_markup'] =
                json_encode(
                    $this->keyboard->toArray(),
                    JSON_UNESCAPED_UNICODE
                );

        }



        return telegram()->sendMessage(

            $this->chatId,

            $this->threadId,

            $this->text,

            $options

        );

    }

}