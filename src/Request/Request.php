<?php

namespace Alexa\Request;

use RuntimeException;
use InvalidArgumentException;
use DateTime;

abstract class Request {
	const TIMESTAMP_VALID_TOLERANCE_SECONDS = 30;

	public $requestId;
	public $timestamp;
	public $user;

        /**
         * Set up Request with RequestId, timestamp (DateTime) and user (User obj.)
         * @param type $data
         */
	public function __construct($data) {
		$this->requestId = $data['request']['requestId'];
		$this->timestamp = new DateTime($data['request']['timestamp']);
		$this->user = new User($data['session']['user']);
	}

        /**
         * Instance the corrct type of Request, based on the $jons->request->type
         * value.
         * @param type $data
         * @return \Alexa\Request\Request   base class
         * @throws RuntimeException
         */
	public static function fromData($data) {
		$requestType = $data['request']['type'];

		if (!class_exists('\\Alexa\\Request\\' . $requestType)) {
			throw new RuntimeException('Unknown request type: ' . $requestType);
		}

		$className = '\\Alexa\\Request\\' . $requestType;

		$request = new $className($data);
		return $request;
	}

        /**
         * Check if request is valid, if not throws an exception
         * @throws InvalidArgumentException
         */
	public function validate() {
		$this->validateTimestamp();
	}

        /**
         * Verify the Amazon Certificate
         * @throws InvalidArgumentException
         * @author Emanuele Corradini <emanuele@evensi.com>
         */
        private function validateSignatureCertificate() {
            $this->verifySignatureCertificateURL();
            $this->validateCertificate();

        }

        const SIGNATURE_VALID_PROTOCOL = 'https';
        const SIGNATURE_VALID_HOSTNAME = 's3.amazonaws.com';
        const SIGNATURE_VALID_PATH = '/echo.api/';
        const SIGNATURE_VALID_PORT = 443;

        /**
         * Verify URL of the certificate
         * @throws InvalidArgumentException
         * @author Emanuele Corradini <emanuele@evensi.com>
         */
        private function verifySignatureCertificateURL() {
            $URL = parse_url($_SERVER['HTTP_SIGNATURECERTCHAINURL']);
            if($URL['scheme'] !== static::SIGNATURE_VALID_PROTOCOL) {
                throw new InvalidArgumentException('Protocol isn\'t secure. Request isn\'t from Alexa.');
            } elseif($URL['host'] !== static::SIGNATURE_VALID_HOSTNAME) {
                throw new InvalidArgumentException('Certificate isn\'t from Amazon. Request isn\'t from Alexa.');
            } elseif(strrpos($URL['path'], static::SIGNATURE_VALID_PATH, -strlen($URL['path'])) !== false) {
                throw new InvalidArgumentException('Certificate isn\'t in "'.static::SIGNATURE_VALID_PATH.'" folder. Request isn\'t from Alexa.');
            } elseif(isset($URL['port']) && $URL['port'] !== static::SIGNATURE_VALID_PORT) {
                throw new InvalidArgumentException('Port isn\'t ' . static::SIGNATURE_VALID_PORT. '. Request isn\'t from Alexa.');
            }
        }

        /**
         * Validate Amazon certificate file
         * @author Emanuele Corradini <emanuele@evensi.com>
         */
        private function validateCertificate() {

        }

        /**
         * Check if request is whithin the allowed time.
         * @throws InvalidArgumentException
         */
	private function validateTimestamp() {
		$now = new DateTime;
		$differenceInSeconds = $now->getTimestamp() - $this->timestamp->getTimestamp();

		if ($differenceInSeconds > self::TIMESTAMP_VALID_TOLERANCE_SECONDS) {
			throw new InvalidArgumentException('Request timestamp was too old. Possible replay attack.');
		}
	}
}
