<?php

/**
 * The application abstraction layer to provide Application ID validation to
 * Alexa requests. Any implementations might provide their own implementations
 * via the $request->setApplicationAbstraction() function but must provide the
 * validateApplicationId() function.
 */

namespace Alexa\Request;

use Exception;
use InvalidArgumentException;

class Application
{
    /** @var string[] */
    public $applicationId;

    /** @var string|null */
    public $requestApplicationId;

    /**
     * Application constructor.
     * @param string $applicationId
     * @throws \Exception
     */
    public function __construct($applicationId)
    {
        $tokens = preg_split('/,/', $applicationId);
        if ($applicationId === false) {
            throw new Exception('Invalid application ID');
        }
        $this->applicationId = $tokens;
    }

    /**
     * @param string $applicationId
     * @return void
     */
    public function setRequestApplicationId($applicationId)
    {
        $this->requestApplicationId = $applicationId;
    }

    /**
     * Validate that the request Application ID matches our Application. This is required as per Amazon requirements.
     * @param string $requestApplicationId Application ID from the Request (typically found in $data['session']['application']
     * @return void
     */
    public function validateApplicationId($requestApplicationId = '')
    {
        if (empty($requestApplicationId)) {
            $requestApplicationId = $this->requestApplicationId;
        }
        if (!in_array($requestApplicationId, $this->applicationId)) {
            throw new InvalidArgumentException('Application Id not matched');
        }
    }
}
