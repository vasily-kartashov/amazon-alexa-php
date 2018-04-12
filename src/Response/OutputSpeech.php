<?php

namespace Alexa\Response;

use Exception;
use InvalidArgumentException;

class OutputSpeech
{
    const TYPE_PLAIN_TEXT = 'PlainText';
    const TYPE_SSML = 'SSML';

    /** @var string */
    public $type = OutputSpeech::TYPE_PLAIN_TEXT;

    /** @var string */
    public $text = '';

    /** @var string */
    public $ssml = '';

    /**
     * @return string[]
     * @psalm-return array{type:string,text:string}|array{type:string,ssml:string}
     * @throws Exception
     */
    public function render(): array
    {
        switch ($this->type) {
            case OutputSpeech::TYPE_PLAIN_TEXT:
                /** @psalm-suppress InvalidReturnStatement */
                return [
                    'type' => OutputSpeech::TYPE_PLAIN_TEXT,
                    'text' => $this->text
                ];
            case OutputSpeech::TYPE_SSML:
                /** @psalm-suppress InvalidReturnStatement */
                return [
                    'type' => OutputSpeech::TYPE_SSML,
                    'ssml' => $this->ssml
                ];
            default:
                throw new Exception('Unsupported type: ' . $this->type);
        }
    }

    public static function plainText(string $text): OutputSpeech
    {
        $speech = new OutputSpeech;
        $speech->type = OutputSpeech::TYPE_PLAIN_TEXT;
        $speech->text = $text;
        return $speech;
    }

    public static function ssml(string $ssml): OutputSpeech
    {
        $speech = new OutputSpeech;
        $speech->type = OutputSpeech::TYPE_SSML;
        libxml_use_internal_errors(true);
        $result = simplexml_load_string($ssml);
        if ($result !== false) {
            foreach (libxml_get_errors() as $error) {
                throw new InvalidArgumentException($error->message);
            }
        }
        $speech->ssml = $ssml;
        return $speech;
    }
}
