<?php

namespace Alexa\Request;

class User
{
    /** @var string|null */
    public $userId;

    /** @var string|null */
    public $accessToken;

    /**
     * User constructor.
     * @param array $data
     * @psalm-param array<string,string>
     */
    public function __construct($data)
    {
        $this->userId = $data['userId'] ?? null;
        $this->accessToken = $data['accessToken'] ?? null;
    }

    /**
     * @return string|null
     */
    public function userId()
    {
        return $this->userId;
    }

    /**
     * @return string|null
     */
    public function accessToken()
    {
        return $this->accessToken;
    }
}
