<?php

namespace Clickatell\Tests;

use Clickatell\Message;
use Clickatell\MessageConverter;
use Clickatell\ClickatellException;
use PHPUnit\Framework\TestCase;

class MessageConverterTest extends TestCase
{
    public function testConvert()
    {
        $messageConverter = new MessageConverter();
        $message = $messageConverter->convert('message');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertSame('message', $message->data);
        $this->assertFalse($message->isUnicode);
    }
}
