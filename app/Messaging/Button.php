<?php

class Button
{
    private string $text;
    private string $callbackData;
    private ?string $url;


    public function __construct(
        string $text,
        string $callbackData = '',
        ?string $url = null
    )
    {
        $this->text = $text;
        $this->callbackData = $callbackData;
        $this->url = $url;
    }


    public function toArray(): array
    {
        $button = [

            'text' => $this->text

        ];


        if ($this->url !== null) {

            $button['url'] = $this->url;

        }
        else {

            $button['callback_data'] =
                $this->callbackData;

        }


        return $button;
    }
}