<?php

namespace Alexa\Response;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testSimpleCard()
    {
        $title = base64_encode(random_bytes(12));
        $content = base64_encode(random_bytes(12));
        $card = Card::simple($title, $content);

        Assert::assertEquals([
            'type' => 'Simple',
            'title' => $title,
            'content' => $content
        ], $card->render());
    }
}
