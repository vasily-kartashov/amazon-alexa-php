<?php

namespace Alexa\Request;

use RuntimeException;
use InvalidArgumentException;
use DateTime;

use Alexa\Request\Certificate;

class Request {

	public $requestId;
	public $timestamp;
	/** @var Session */
	public $session;
	public $data;
	public $rawData;

	/**
	 * Set up Request with RequestId, timestamp (DateTime) and user (User obj.)
	 * @param type $data
	 */
	public function __construct($rawData) {
		if (!is_string($rawData)) {
			throw new InvalidArgumentException('Alexa Request requires the raw JSON data to validate request signature');
		}

		// Decode the raw data into a JSON array.
		$data = json_decode($rawData, TRUE);

		$this->data = $data;
		$this->rawData = $rawData;

		$this->requestId = $data['request']['requestId'];
		$this->timestamp = new DateTime($data['request']['timestamp']);
		$this->session = new Session($data['session']);

		$certificate = new Certificate($_SERVER['HTTP_SIGNATURECERTCHAINURL'], $_SERVER['HTTP_SIGNATURE']);
		$certificate->validateRequest($rawData);
	}

	/**
	 * Instance the correct type of Request, based on the $jons->request->type
	 * value.
	 * @param type $data
	 * @return \Alexa\Request\Request   base class
	 * @throws RuntimeException
	 */
	public function fromData() {
		$data = $this->data;
		$requestType = $data['request']['type'];

		if (!class_exists('\\Alexa\\Request\\' . $requestType)) {
			throw new RuntimeException('Unknown request type: ' . $requestType);
		}

		$className = '\\Alexa\\Request\\' . $requestType;

		$request = new $className($data);
		return $request;
	}

}
