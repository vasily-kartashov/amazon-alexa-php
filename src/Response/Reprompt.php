<?php

namespace Alexa\Response;

class Reprompt
{
    public $outputSpeech;

    public function __construct(OutputSpeech $speech = null)
    {
        $this->outputSpeech = $speech ?? new OutputSpeech;
    }

    public function render(): array
    {
        return [
            'outputSpeech' => $this->outputSpeech->render()
        ];
    }
}
