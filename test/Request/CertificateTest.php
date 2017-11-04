<?php

namespace Alexa\Request;

use PHPUnit\Framework\TestCase;

class CertificateTest extends TestCase
{
    /**
     * @expectedException \Alexa\Request\AlexaException
     * @expectedExceptionMessage Invalid protocol
     */
    public function testVerifyUrlWithInvalidProtocol()
    {
        $certificate = new Certificate('http://test', null);
        $certificate->verifySignatureCertificateURL();
    }

    /**
     * @expectedException \Alexa\Request\AlexaException
     * @expectedExceptionMessage Invalid hostname
     */
    public function testVerifyUrlWithInvalidHostname()
    {
        $certificate = new Certificate('https://test', null);
        $certificate->verifySignatureCertificateURL();
    }

    /**
     * @expectedException \Alexa\Request\AlexaException
     * @expectedExceptionMessage Invalid path
     */
    public function testVerifyUrlWithInvalidPath()
    {
        $certificate = new Certificate('https://s3.amazonaws.com:443/test', null);
        $certificate->verifySignatureCertificateURL();
    }

    /**
     * @expectedException \Alexa\Request\AlexaException
     * @expectedExceptionMessage Invalid port
     */
    public function testVerifyUrlWithInvalidPort()
    {
        $certificate = new Certificate('https://s3.amazonaws.com:444/echo.api/test', null);
        $certificate->verifySignatureCertificateURL();
    }
}
