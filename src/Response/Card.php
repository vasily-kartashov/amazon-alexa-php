<?php

namespace Alexa\Response;

class Card
{
    public $type = 'Simple';
    public $title = '';
    public $content = '';

    public function render(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    public static function simple(string $title, string $content): Card
    {
        $card = new Card;
        $card->title = $title;
        $card->content = $content;
        return $card;
    }
}
