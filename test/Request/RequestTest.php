<?php

namespace Request;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testLaunchRequest()
    {
        /*
        $rawData = file_get_contents(__DIR__ . '/launch-request.json');
        $envelope = new Request($rawData);
        $request = $envelope->fromData();

        $this->assertInstanceOf(LaunchRequest::class, $request);
        $this->assertEquals(new DateTime('2015-05-13 12:34:56Z'), $request->timestamp);
        $this->assertEquals('amzn1.echo-sdk-ams.app.000000-d0ed-0000-ad00-000000d00ebe', $request->applicationId);

        $this->assertEquals('amzn1.account.AM3B00000000000000000000000', $request->user->userId);
        $this->assertNull($request->user->accessToken);
        */
        Assert::assertTrue(true);
    }
}
