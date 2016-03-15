<?php

namespace Alexa\Response;

class Response {
	public $version = '1.0';
	public $sessionAttributes = array();

	public $outputSpeech = null;
	public $card = null;
	public $reprompt = null;
	public $shouldEndSession = false;

	public function __construct() {
		$this->outputSpeech = new OutputSpeech;
	}

        /**
         * Set output speech
         * @param type $text
         * @return \Alexa\Response\Response
         */
	public function respond($text) {
		$this->outputSpeech = new OutputSpeech;
		$this->outputSpeech->text = $text;

		return $this;
	}

        /**
         * Set up repompt and set text
         * @param type $text
         * @return \Alexa\Response\Response
         */
	public function reprompt($text) {
		$this->reprompt = new Reprompt;
		$this->reprompt->outputSpeech->text = $text;

		return $this;
	}

        /**
         * Add card information
         * @param type $title
         * @param type $content
         * @return \Alexa\Response\Response
         */
	public function withCard($title, $content = '') {
		$this->card = new Card;
		$this->card->title = $title;
		$this->card->content = $content;
		
		return $this;
	}

        /**
         * Set if it should end the session
         * @param type $shouldEndSession
         * @return \Alexa\Response\Response
         */
	public function endSession($shouldEndSession = true) {
		$this->shouldEndSession = $shouldEndSession;

		return $this;
	}
        
        /**
         * Add a session attribute that will be passed in every requests.
         * @param string $key
         * @param mixed $value
         */
        public function addSessionAttribute($key, $value) {
                $this->sessionAttributes[$key] = $value;
        }

        /**
         * Return the response as an array for JSON-ification
         * @return type
         */
	public function render() {
		return array(
			'version' => $this->version,
			'sessionAttributes' => $this->sessionAttributes,
			'response' => array(
				'outputSpeech' => $this->outputSpeech ? $this->outputSpeech->render() : null,
				'card' => $this->card ? $this->card->render() : null,
				'reprompt' => $this->reprompt ? $this->reprompt->render() : null,
				'shouldEndSession' => $this->shouldEndSession ? true : false
			)
		);
	}
}