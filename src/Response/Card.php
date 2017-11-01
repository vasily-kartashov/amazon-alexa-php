<?php

namespace Alexa\Response;

class Card
{
    /** @var string */
    public $type = 'Simple';

    /** @var string */
    public $title = '';

    /** @var string */
    public $content = '';

    /**
     * @return array<string,string>
     */
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
