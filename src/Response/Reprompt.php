<?php

namespace Alexa\Response;

use Exception;

class Reprompt
{
    /** @var OutputSpeech */
    public $outputSpeech;

    public function __construct(OutputSpeech $speech = null)
    {
        $this->outputSpeech = $speech ?? new OutputSpeech;
    }

    /**
     * @return array<string,array<string,string>>
     * @psalm-return array{outputSpeech:array{type:string,text:string}|array{type:string,ssml:string}}
     * @throws Exception
     */
    public function render(): array
    {
        return [
            'outputSpeech' => $this->outputSpeech->render()
        ];
    }
}
