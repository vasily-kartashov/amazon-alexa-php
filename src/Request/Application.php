<?php

namespace Alexa\Request;

class Application {
	public $applicationId;

	public function __construct($data) {
		$this->applicationId = isset($data['applicationId']) ? $data['applicationId'] : null;
	}

}