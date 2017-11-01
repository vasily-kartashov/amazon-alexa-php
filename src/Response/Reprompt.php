<?php

namespace Alexa\Response;

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
     */
    public function render(): array
    {
        return [
            'outputSpeech' => $this->outputSpeech->render()
        ];
    }
}
