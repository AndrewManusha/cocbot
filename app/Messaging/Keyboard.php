<?php

class Keyboard
{
    private array $rows = [];


    public function addButton(
        Button $button
    ): self
    {

        $this->rows[][] =
            $button;


        return $this;

    }



    public function addRow(
        Button ...$buttons
    ): self
    {

        $row = [];


        foreach ($buttons as $button) {

            $row[] = $button;

        }


        $this->rows[] =
            $row;


        return $this;

    }




    public function toArray(): array
    {

        $keyboard = [];


        foreach ($this->rows as $row) {

            $buttons = [];


            foreach ($row as $button) {

                $buttons[] =
                    $button->toArray();

            }


            $keyboard[] =
                $buttons;

        }


        return [

            'inline_keyboard' =>
                $keyboard

        ];

    }
}