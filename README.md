# Clickatell PHP client

A simple library to send SMS through the [Clickatell](https://www.clickatell.com/) SMS gateway.

Clickatell supports two character encodings: 7-bit (up to 160 characters per message) and Unicode (up to 70 characters per message).
The choice of the encoding directly influences the number of credits debited when sending an SMS.

This library **automatically handles character set conversion**, using 7-bit whenever possible and switching to Unicode only when required.
This guarantees a perfect delivery of the original message, while minimizing credit consumption.

## Basic usage

```php
$client = new Clickatell\ClickatellClient('api-id', 'username', 'password');
$client->authenticate();
$client->send('441234567890', 'Hello world');
```

The `authenticate()` call is required *once*, whether you need to `send()` one or several SMS messages.

The phone numbers must be in E.164 international format: country code + national number without `0` prefix.
They must not include a leading `+` sign.

The message must use the UTF-8 charset.

## Sender ID

If you registered a sender ID with Clickatell, you can specify it when instantiating the library:

```php
$client = new Clickatell\ClickatellClient('api-id', 'username', 'password', 'sender-id');
```

It will then automatically be used for every `send()` call. Alternatively, you can pass it to every call individually:

```php
$client->send('441234567890', 'Hello world', 'sender-id');
```
