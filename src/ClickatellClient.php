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
     * The Guzzle HTTP client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * The GSM charset converter.
     *
     * @var MessageConverter
     */
    private $messageConverter;

    /**
     * Class constructor.
     *
     * @param string $apiId    The Clickatell API id.
     * @param string $username The Clickatell username.
     * @param string $password The Clickatell password.
     */
    public function __construct($apiId, $username, $password)
    {
        $this->apiId    = $apiId;
        $this->username = $username;
        $this->password = $password;

        $this->client = new Client();
        $this->messageConverter = new MessageConverter();
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
     * Sends a SMS on the Clickatell API.
     *
     * @param string $number  The phone number, in international format, without the leading +.
     * @param string $message The text message, in UTF-8 format.
     * @param array  $options Additional options: from, callback, concat, ...
     *
     * @return string The message ID.
     *
     * @throws \Clickatell\ClickatellException
     */
    public function send($number, $message, array $options = [])
    {
        if (! ctype_digit($number)) {
            throw new ClickatellException('Invalid phone number.');
        }

        $message = $this->messageConverter->convert($message);

        $parameters = [
            'api_id'   => $this->apiId,
            'user'     => $this->username,
            'password' => $this->password,
            'to'       => $number,
            'unicode'  => $message->isUnicode ? '1' : '0',
            'text'     => $message->data,
        ];

        $parameters += $options;

        $response = $this->post(self::SENDMSG_ENDPOINT, $parameters);

        if (preg_match('/^ID: (\S+)/', $response, $matches) == 0) {
            throw new ClickatellException('Invalid sendmsg response: ' . $response);
        }

        return $matches[1];
    }
}
