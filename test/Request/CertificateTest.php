<?php

namespace Alexa\Request;

use PHPUnit\Framework\TestCase;

class CertificateTest extends TestCase
{
    public function testVerifyUrlWithInvalidProtocol()
    {
        $certificate = new Certificate('http://test', null);
        $this->expectException(AlexaException::class);
        $this->expectExceptionMessage('Invalid protocol');
        $certificate->verifySignatureCertificateURL();
    }

    public function testVerifyUrlWithInvalidHostname()
    {
        $certificate = new Certificate('https://test', null);
        $this->expectException(AlexaException::class);
        $this->expectExceptionMessage('Invalid hostname');
        $certificate->verifySignatureCertificateURL();
    }

    public function testVerifyUrlWithInvalidPath()
    {
        $certificate = new Certificate('https://s3.amazonaws.com:443/test', null);
        $this->expectException(AlexaException::class);
        $this->expectExceptionMessage('Invalid path');
        $certificate->verifySignatureCertificateURL();
    }

    public function testVerifyUrlWithInvalidPort()
    {
        $certificate = new Certificate('https://s3.amazonaws.com:444/echo.api/test', null);
        $this->expectException(AlexaException::class);
        $this->expectExceptionMessage('Invalid port');
        $certificate->verifySignatureCertificateURL();
    }
}
