<?php

namespace Alexa\Request;

class Session {
        /** @var User */
	public $user;
	public $new;
        /** @var Application */
        public $application;
        public $sessionId;

	public function __construct($data) {
		$this->user = new User($data['user']);
		$this->sessionId = isset($data['sessionId']) ? $data['sessionId'] : null;
                $this->new = isset($data['new']) ? $data['new'] : null;
	}

}