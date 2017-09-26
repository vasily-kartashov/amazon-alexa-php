<?php

namespace Alexa\Response;

class Response
{
    public $version = '1.0';

    public $sessionAttributes = [];

    /** @var OutputSpeech|null */
    public $outputSpeech = null;

    /** @var null|Card */
    public $card = null;

    /** @var null|Reprompt */
    public $reprompt = null;

    public $shouldEndSession = false;

    public function __construct()
    {
        $this->outputSpeech = new OutputSpeech;
    }

    /**
     * Set output speech as text
     * @param string $text
     * @return Response
     */
    public function respond($text): Response
    {
        $this->outputSpeech = new OutputSpeech;
        $this->outputSpeech->text = $text;
        return $this;
    }

    /**
     * Set up response with SSML.
     * @param string $ssml
     * @return Response
     */
    public function respondSSML($ssml): Response
    {
        $this->outputSpeech = new OutputSpeech;
        $this->outputSpeech->type = 'SSML';
        $this->outputSpeech->ssml = $ssml;
        return $this;
    }

    /**
     * Set up reprompt with given text
     * @param string $text
     * @return Response
     */
    public function reprompt($text): Response
    {
        $this->reprompt = new Reprompt;
        $this->reprompt->outputSpeech->text = $text;
        return $this;
    }

    /**
     * Set up reprompt with given ssml
     * @param string $ssml
     * @return Response
     */
    public function repromptSSML($ssml): Response
    {
        $this->reprompt = new Reprompt;
        $this->reprompt->outputSpeech->type = 'SSML';
        $this->reprompt->outputSpeech->text = $ssml;
        return $this;
    }

    /**
     * Add card information
     * @param string $title
     * @param string $content
     * @return Response
     */
    public function withCard($title, $content = ''): Response
    {
        $this->card = new Card;
        $this->card->title = $title;
        $this->card->content = $content;
        return $this;
    }

    /**
     * Set if it should end the session
     * @param bool $shouldEndSession
     * @return Response
     */
    public function endSession($shouldEndSession = true): Response
    {
        $this->shouldEndSession = $shouldEndSession;
        return $this;
    }

    /**
     * Add a session attribute that will be passed in every requests.
     * @param string $key
     * @param mixed $value
     * @return Response
     */
    public function addSessionAttribute($key, $value): Response
    {
        $this->sessionAttributes[$key] = $value;
        return $this;
    }

    /**
     * Return the response as an array
     * @return array
     */
    public function render()
    {
        return [
            'version' => $this->version,
            'sessionAttributes' => $this->sessionAttributes,
            'response' => [
                'outputSpeech' => $this->outputSpeech ? $this->outputSpeech->render() : null,
                'card' => $this->card ? $this->card->render() : null,
                'reprompt' => $this->reprompt ? $this->reprompt->render() : null,
                'shouldEndSession' => $this->shouldEndSession ? true : false
            ]
        ];
    }
}
