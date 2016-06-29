<?php

namespace Alexa\Request;

class SessionEndedRequest extends Request {
	public $reason;

	public function __construct($rawData) {
		parent::__construct($rawData);

		$this->reason = $data['request']['reason'];
	}
}
