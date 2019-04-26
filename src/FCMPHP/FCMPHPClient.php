<?php
/**
 * Copyright (c) 2011-2018 Guilherme Valentim
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
namespace FCMPHP;

use FCMPHP\HttpClients\HttpClientInterface;
use FCMPHP\HttpClients\HttpCurlClient;
use FCMPHP\Exceptions\FCMPHPException;

/**
 * Class FCMPHPClient
 *
 * @package FCMPHP
 */
class FCMPHPClient
{
    /**
     * @const string Production API URL.
     */
    const BASE_FCM_URL = 'https://fcm.googleapis.com/fcm';

    /**
     * @const int The timeout in seconds for a normal request.
     */
    const DEFAULT_REQUEST_TIMEOUT = 60;

    /**
     * @var HttpClientInterface HTTP client handler.
     */
    protected $httpClientHandler;

    /**
     * @var int The number of calls that have been made to FCM.
     */
    public static $requestCount = 0;

    /**
     * Instantiates a new FCMPHPClient object.
     *
     * @param HttpClientInterface|null $httpClientHandler
     */
    public function __construct(HttpClientInterface $httpClientHandler = null)
    {
        $this->httpClientHandler = $httpClientHandler ?: $this->detectHttpClientHandler();
    }

    /**
     * Sets the HTTP client handler.
     *
     * @param HttpClientInterface $httpClientHandler
     */
    public function setHttpClientHandler(HttpClientInterface $httpClientHandler)
    {
        $this->httpClientHandler = $httpClientHandler;
    }

    /**
     * Returns the HTTP client handler.
     *
     * @return HttpClientInterface
     */
    public function getHttpClientHandler()
    {
        return $this->httpClientHandler;
    }

    /**
     * Detects which HTTP client handler to use.
     *
     * @return HttpClientInterface
     */
    public function detectHttpClientHandler()
    {
        return new HttpCurlClient();
    }

    /**
     * Returns the base FCM URL.
     *
     * @return string
     */
    public function getBaseFCMUrl()
    {
        return static::BASE_FCM_URL;
    }

    /**
     * Prepares the request for sending to the client handler.
     *
     * @param FCMPHPRequest $request
     *
     * @return array
     */
    public function prepareRequestMessage(FCMPHPRequest $request)
    {
        $url = $this->getBaseFCMUrl() . $request->getUrl();

        $requestBody = $request->getJsonEncodedBody();

        $request->setHeaders(array(
            'Content-Type' => 'application/json',
        ));

        return array(
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $requestBody->getBody(),
        );
    }

    /**
     * Makes the request to FCM and returns the result.
     *
     * @param FCMPHPRequest $request
     *
     * @return FCMPHPResponse
     *
     * @throws FCMPHPException
     */
    public function sendRequest(FCMPHPRequest $request)
    {
        list($url, $method, $headers, $body) = $this->prepareRequestMessage($request);

        $timeOut = static::DEFAULT_REQUEST_TIMEOUT;

        // Should throw `FCMPHPEsxception` exception on HTTP client error.
        // Don't catch to allow it to bubble up.
        $rawResponse = $this->httpClientHandler->send($url, $method, $body, $headers, $timeOut);

        static::$requestCount++;

        $returnResponse = new FCMPHPResponse(
            $request,
            $rawResponse->getBody(),
            $rawResponse->getHttpResponseCode(),
            $rawResponse->getHeaders()
        );

        if ($returnResponse->isError()) {
            throw $returnResponse->getThrownException();
        }

        return $returnResponse;
    }
}
