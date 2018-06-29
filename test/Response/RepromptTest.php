<?php

namespace Alexa\Response;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class RepromptTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testCreatingReprompt()
    {
        $text = base64_encode(random_bytes(12));
        $reprompt = new Reprompt(OutputSpeech::plainText($text));

        Assert::assertEquals([
            'outputSpeech' => [
                'type' => 'PlainText',
                'text' => $text
            ]
        ], $reprompt->render());
    }
}
