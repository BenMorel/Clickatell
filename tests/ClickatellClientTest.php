<?php

namespace Clickatell\Tests;

use Clickatell\ClickatellClient;
use Clickatell\ClickatellException;
use PHPUnit\Framework\TestCase;

class ClickatellClientTest extends TestCase
{
    public function testConstructor()
    {
        $clickatell = new ClickatellClient('api_id', 'username', 'password');

        $this->assertInstanceOf(ClickatellClient::class, $clickatell);
    }

    public function testSendOnInvalidCredentialsShouldReturnClickatellException()
    {
        $clickatell = new ClickatellClient('api_id', 'username', 'password');

        $this->expectException(ClickatellException::class);
        $this->expectExceptionMessage('Invalid or missing api_id');

        $clickatell->send('0912345678', 'message');
    }

    public function testSendOnInvalidPhoneNumberShouldReturnClickatellException()
    {
        $clickatell = new ClickatellClient('api_id', 'username', 'password');

        $this->expectException(ClickatellException::class);
        $this->expectExceptionMessage('Invalid phone number.');

        $clickatell->send('invalid_phone_number', 'message');
    }
}
