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
		$this->sessionId = isset($data['sessionId']) ? $this->parseSessionId($data['sessionId']) : null;
                $this->new = isset($data['new']) ? $data['new'] : null;
	}
        
        /**
         * Remove "SessionId." prefix from the send session id, as it's invalid
         * as a session id (at least for default session, on file).
         * @param type $sessionId
         * @return type
         */
        protected function parseSessionId($sessionId) {
                $prefix = 'SessionId.';
                if (substr($sessionId, 0, strlen($prefix)) == $prefix) {
                        return substr($sessionId, strlen($prefix));
                } else {
                        return $sessionId;
                }
        }
        
        /**
         * Open PHP SESSION using amazon provided sessionId, for storing data about the session.
         * Session cookie won't be sent.
         */
        public function openSession() {
                ini_set('session.use_cookies', 0); # disable session cookies
                session_id($this->sessionId);
                return session_start();
        }

}