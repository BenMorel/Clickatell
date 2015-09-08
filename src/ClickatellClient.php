<?php

namespace Clickatell;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

/**
 * Sends SMS messages on the Clickatell gateway.
 */
class ClickatellClient
{
    const AUTH_ENDPOINT    = 'https://api.clickatell.com/http/auth';
    const SENDMSG_ENDPOINT = 'https://api.clickatell.com/http/sendmsg';

    /**
     * The Clickatell API id.
     *
     * @var string
     */
    private $apiId;

    /**
     * The Clickatell username.
     *
     * @var string
     */
    private $username;

    /**
     * The Clickatell password.
     *
     * @var string
     */
    private $password;

    /**
     * A default sender id, or null if not provided.
     *
     * @var string|null
     */
    private $defaultSenderId;

    /**
     * The Guzzle HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * The GSM charset converter.
     *
     * @var CharsetConverter
     */
    private $charsetConverter;

    /**
     * The Clickatell session id, or null if not yet authenticated.
     *
     * @var string|null
     */
    private $sessionId = null;

    /**
     * Class constructor.
     *
     * @param string      $apiId           The Clickatell API id.
     * @param string      $username        The Clickatell username.
     * @param string      $password        The Clickatell password.
     * @param string|null $defaultSenderId A default sender id (optional).
     */
    public function __construct($apiId, $username, $password, $defaultSenderId = null)
    {
        $this->apiId = $apiId;
        $this->username = $username;
        $this->password = $password;
        $this->defaultSenderId = $defaultSenderId;

        $this->client = new Client();
        $this->charsetConverter = new CharsetConverter();
    }

    /**
     * Performs a POST request on the Clickatell API.
     *
     * @param string $url        The request URL.
     * @param array  $parameters The POST parameters.
     *
     * @return string
     *
     * @throws \Clickatell\ClickatellException
     */
    private function post($url, array $parameters)
    {
        try {
            $response = $this->client->post($url, [
                RequestOptions::FORM_PARAMS => $parameters
            ]);
        }
        catch (RequestException $e) {
            throw new ClickatellException('The HTTP request failed.', 0, $e);
        }

        return (string) $response->getBody();
    }

    /**
     * Authenticates with the Clickatell API.
     *
     * This needs to be called only once before any number of send() calls.
     * This should be called again after any pause has been done,
     * to avoid the risk of using an expired session (such as between multiple batch job runs).
     *
     * @return void
     *
     * @throws \Clickatell\ClickatellException
     */
    public function authenticate()
    {
        $response = $this->post(self::AUTH_ENDPOINT, [
            'api_id'   => $this->apiId,
            'user'     => $this->username,
            'password' => $this->password
        ]);

        if (preg_match('/^OK: (\S+)/', $response, $matches) == 0) {
            throw new ClickatellException('Invalid auth response: ' . $response);
        }

        $this->sessionId = $matches[1];
    }

    /**
     * Sends a SMS on the Clickatell API.
     *
     * @param string      $number  The phone number, in international format, without the leading +.
     * @param string      $message The text message, in UTF-8 format.
     * @param string|null $sender  The Sender ID, or null to use the default.
     *
     * @return string The message ID.
     *
     * @throws \Clickatell\ClickatellException
     */
    public function send($number, $message, $sender = null)
    {
        if ($this->sessionId === null) {
            throw new ClickatellException('You need to authenticate() before send()ing a message.');
        }

        if (! ctype_digit($number)) {
            throw new ClickatellException('Invalid phone number.');
        }

        if (! mb_check_encoding($message, 'UTF-8')) {
            throw new ClickatellException('The message must be in UTF-8 format.');
        }

        // Convert the message to UCS-2 big endian.
        $message = mb_convert_encoding($message, 'UCS-2BE', 'UTF-8');

        // Attempt to convert the message to the GSM charset.
        $gsmCharsetMessage= $this->charsetConverter->convert($message);

        $parameters = [
            'session_id' => $this->sessionId,
            'to'         => $number,
            'concat'     => 10 // Maximum number of messages allowed.
        ];

        if ($sender === null) {
            $sender = $this->defaultSenderId;
        }

        if ($sender !== null) {
            $parameters['from'] = $sender;
        }

        if ($gsmCharsetMessage !== null) {
            // The conversion succeeded, send in the GSM format.
            $parameters['unicode'] = '0';
            $parameters['text'] = $gsmCharsetMessage;
        }
        else {
            // The conversion failed, send in the Unicode format, hex-encoded.
            $parameters['unicode'] = '1';
            $parameters['text'] = bin2hex($message);
        }

        $response = $this->post(self::SENDMSG_ENDPOINT, $parameters);

        if (preg_match('/^ID: (\S+)/', $response, $matches) == 0) {
            throw new ClickatellException('Invalid sendmsg response: ' . $response);
        }

        return $matches[1];
    }
}
