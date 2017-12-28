<?php

namespace Alexa\Request;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testValidApplicationId()
    {
        $application = new Application('a,b,c');
        /** @noinspection PhpUnhandledExceptionInspection */
        $application->validateApplicationId('a');
        /** @noinspection PhpUnhandledExceptionInspection */
        $application->validateApplicationId('b');
        /** @noinspection PhpUnhandledExceptionInspection */
        $application->validateApplicationId('c');

        Assert::assertTrue(true);
    }

    /**
     * @expectedException \Alexa\Request\AlexaException
     */
    public function testInvalidApplicationId()
    {
        $application = new Application('a,b,c');
        $application->validateApplicationId('d');
    }
}
