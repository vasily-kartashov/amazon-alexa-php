<?php

namespace Alexa\Request;

use DateTime;
use Exception;
use InvalidArgumentException;
use RuntimeException;

class Request
{
    /** @var string */
    public $requestId;

    /** @var DateTime */
    public $timestamp;

    /** @var Session */
    public $session;

    /** @var array */
    public $data;

    /** @var string */
    public $rawData;

    /** @var string|null */
    public $applicationId;

    /** @var Certificate|null */
    private $certificate;

    /** @var Application|null */
    private $application;

    /**
     * Set up Request with RequestId, timestamp (DateTime) and user (User obj.)
     * @param string $rawData
     * @param mixed $applicationId
     */
    public function __construct($rawData, $applicationId = null)
    {
        if (!is_string($rawData)) {
            throw new InvalidArgumentException('Alexa Request requires the raw JSON data to validate request signature');
        }

        // Decode the raw data into a JSON array.
        $data = json_decode($rawData, true);
        $this->data = $data;
        $this->rawData = $rawData;

        $this->requestId = $data['request']['requestId'];
        $this->timestamp = new DateTime($data['request']['timestamp']);
        $this->session = new Session($data['session']);

        $this->applicationId = (is_null($applicationId) && isset($data['session']['application']['applicationId']))
            ? $data['session']['application']['applicationId']
            : $applicationId;
    }

    /**
     * Accept the certificate validator dependency in order to allow people
     * to extend it to for example cache their certificates.
     * @param Certificate $certificate
     * @return void
     */
    public function setCertificateDependency(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * Accept the application validator dependency in order to allow people
     * to extend it.
     * @param Application $application
     * @return void
     */
    public function setApplicationDependency(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Instance the correct type of Request, based on the $json->request->type value.
     * @return \Alexa\Request\Request base class
     * @throws AlexaException
     */
    public function fromData()
    {
        $data = $this->data;

        // Instantiate a new Certificate validator if none is injected
        // as our dependency.
        if (!isset($this->certificate)) {
            $this->certificate = new Certificate($_SERVER['HTTP_SIGNATURECERTCHAINURL'], $_SERVER['HTTP_SIGNATURE']);
        }
        if ($this->application === null && $this->applicationId !== null) {
            $this->application = new Application($this->applicationId);
        }

        if ($this->application === null) {
            throw new AlexaException('Application is not initialized');
        }

        // We need to ensure that the request Application ID matches our Application ID.
        $this->application->validateApplicationId($data['session']['application']['applicationId']);

        // Validate that the request signature matches the certificate.
        $this->certificate->validateRequest($this->rawData);


        $requestType = $data['request']['type'];
        if (!class_exists('\\Alexa\\Request\\' . $requestType)) {
            throw new AlexaException('Unknown request type: ' . $requestType);
        }
        $className = '\\Alexa\\Request\\' . $requestType;

        $request = new $className($this->rawData);
        return $request;
    }
}
