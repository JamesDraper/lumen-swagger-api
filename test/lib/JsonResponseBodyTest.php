<?php
declare(strict_types=1);

namespace Lib\Test;

use Lib\JsonResponseBody;

use PHPUnit\Framework\TestCase;

final class JsonResponseBodyTest extends TestCase
{
    public function testItShouldCreateASuccessJsonResponseBody(): void
    {
        $vars = [
            'one'   => 'two',
            'three' => 'four'
        ];

        $jsonResponseBody = JsonResponseBody::createSuccess($vars);

        $responseStr = '{"success":true,"vars":{"one":"two","three":"four"},"errs":[]}';

        $this->assertInstanceOf(JsonResponseBody::class, $jsonResponseBody);
        $this->assertSame($responseStr, $jsonResponseBody->toString());
    }

    public function testItShouldCreateAFailureJsonResponseBody(): void
    {
        $errs = [
            'one',
            'two',
            'three',
            'four'
        ];

        $jsonResponseBody = JsonResponseBody::createFailure($errs);

        $responseStr = '{"success":false,"vars":{},"errs":["one","two","three","four"]}';

        $this->assertInstanceOf(JsonResponseBody::class, $jsonResponseBody);
        $this->assertSame($responseStr, $jsonResponseBody->toString());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage All $vars must have a name and a value, got a number.
     */
    public function testItShouldThrowAnExceptionIfTheVarsAreNotAnAssociativeArray(): void
    {
        JsonResponseBody::createSuccess([
            'one',
            'two',
            'three',
            'four'
        ]);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage No $errs can have names, the $errs array must be numeric.
     */
    public function testItShouldThrowAnExceptionIfTheErrsAreAnAssociativeArray(): void
    {
        JsonResponseBody::createFailure([
            'one'   => 'two',
            'three' => 'four'
        ]);
    }
}
