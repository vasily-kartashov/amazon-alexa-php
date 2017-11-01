<?php

namespace Alexa\Request;

class LaunchRequest extends Request
{
    /** @var string */
    public $applicationId;

    public function __construct($rawData)
    {
        parent::__construct($rawData);
        $this->applicationId = $this->data['session']['application']['applicationId'];
    }
}
