<?php

namespace Alexa\Request;

class User
{
    public $userId;
    public $accessToken;

    public function __construct($data)
    {
        $this->userId = $data['userId'] ?? null;
        $this->accessToken = $data['accessToken'] ?? null;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function accessToken()
    {
        return $this->accessToken;
    }
}
