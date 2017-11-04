<?php

namespace Alexa\Request;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testValidApplicationId()
    {
        $application = new Application('a,b,c');
        $application->validateApplicationId('a');
        $application->validateApplicationId('b');
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
