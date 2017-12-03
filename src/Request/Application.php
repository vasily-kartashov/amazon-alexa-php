<?php

/**
 * The application abstraction layer to provide Application ID validation to
 * Alexa requests. Any implementations might provide their own implementations
 * via the $request->setApplicationAbstraction() function but must provide the
 * validateApplicationId() function.
 */

namespace Alexa\Request;

class Application
{
    /** @var string[] */
    public $applicationId;

    /** @var string|null */
    public $requestApplicationId;

    /**
     * Application constructor.
     * @param string $applicationId
     */
    public function __construct(string $applicationId)
    {
        $this->applicationId = preg_split('/,/', $applicationId);
    }

    /**
     * @param string $requestApplicationId
     * @return void
     */
    public function setRequestApplicationId($requestApplicationId)
    {
        $this->requestApplicationId = $requestApplicationId;
    }

    /**
     * Validate that the request Application ID matches our Application. This is required as per Amazon requirements.
     * @param string $requestApplicationId Application ID from the Request (typically found in $data['session']['application']
     * @return void
     * @throws AlexaException
     * @todo it would be much easier to return booleans and not throwing exceptions all the time
     */
    public function validateApplicationId($requestApplicationId = '')
    {
        if (empty($requestApplicationId)) {
            $requestApplicationId = $this->requestApplicationId;
        }
        if (!in_array($requestApplicationId, $this->applicationId)) {
            throw new AlexaException('Application Id not matched');
        }
    }
}
