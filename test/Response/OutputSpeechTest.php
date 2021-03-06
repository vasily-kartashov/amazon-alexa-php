<?php

namespace Alexa\Response;

use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class OutputSpeechTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testPlainTextCreation()
    {
        $text = random_bytes(12);
        $speech = OutputSpeech::plainText($text);

        Assert::assertEquals([
            'type' => 'PlainText',
            'text' => $text
        ], $speech->render());
    }

    /**
     * @throws \Exception
     */
    public function testSsmlCreation()
    {
        $ssml = '<speech>' . base64_encode(random_bytes(12)) . '</speech>';
        $speech = OutputSpeech::ssml($ssml);

        Assert::assertEquals([
            'type' => 'SSML',
            'ssml' => $ssml
        ], $speech->render());
    }

    public function testInvalidSsmlCreation()
    {
        $ssml = '<speech>' . base64_encode(random_bytes(12)) . '</speach>';
        $this->expectException(InvalidArgumentException::class);
        OutputSpeech::ssml($ssml);
    }
}
