<?php

namespace Alexa\Response;

class OutputSpeech {
	public $type = 'PlainText';
	public $text = '';

	public function render() {
		return array(
			'type' => $this->type,
			'text' => $this->text
		);
	}
}