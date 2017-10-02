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

    public function withOutputSpeech(OutputSpeech $speech): Response
    {
        $this->outputSpeech = $speech;
        return $this;
    }

    public function withReprompt(OutputSpeech $speech): Response
    {
        $this->reprompt = new Reprompt($speech);
        return $this;
    }








    /**
     * Set output speech as text
     * @param string $text
     * @return Response
     * @deprecated See withOutputSpeech()
     */
    public function respond(string $text): Response
    {
        $this->outputSpeech = OutputSpeech::plainText($text);
        return $this;
    }

    /**
     * Set up response with SSML.
     * @param string $ssml
     * @return Response
     * @deprecated see withOutputSpeech
     */
    public function respondSSML(string $ssml): Response
    {
        $this->outputSpeech = OutputSpeech::ssml($ssml);
        return $this;
    }

    /**
     * Set up reprompt with given text
     * @param string $text
     * @return Response
     * @deprecated see withReprompt
     */
    public function reprompt($text): Response
    {
        $this->reprompt = new Reprompt(OutputSpeech::plainText($text));
        return $this;
    }

    /**
     * Set up reprompt with given ssml
     * @param string $ssml
     * @return Response
     * @deprecated see withReprompt
     */
    public function repromptSSML($ssml): Response
    {
        $this->reprompt = new Reprompt(OutputSpeech::ssml($ssml));
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
