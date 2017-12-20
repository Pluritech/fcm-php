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

use FCMPHP\Exceptions\FCMPHPResponseException;
use FCMPHP\Exceptions\FCMPHPException;

/**
 * Class FCMPHPResponse
 *
 * @package FCMPHP
 */
class FCMPHPResponse
{
    /**
     * @var int The HTTP status code response from FCM.
     */
    protected $httpStatusCode;

    /**
     * @var array The headers returned from FCM.
     */
    protected $headers;

    /**
     * @var string The raw body of the response from FCM.
     */
    protected $body;

    /**
     * @var array The decoded body of the FCM response.
     */
    protected $decodedBody = [];

    /**
     * @var FCMPHPRequest The original request that returned this response.
     */
    protected $request;

    /**
     * @var FCMPHPException The exception thrown by this request.
     */
    protected $thrownException;

    /**
     * Creates a new Response entity.
     *
     * @param FCMPHPRequest   $request
     * @param string|null     $body
     * @param int|null        $httpStatusCode
     * @param array|null      $headers
     */
    public function __construct(FCMPHPRequest $request, $body = null, $httpStatusCode = null, array $headers = [])
    {
        $this->request = $request;
        $this->body = $body;
        $this->httpStatusCode = $httpStatusCode;
        $this->headers = $headers;

        $this->decodeBody();
    }

    /**
     * Return the original request that returned this response.
     *
     * @return FCMPHP
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Return the HTTP status code for this response.
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Return the HTTP headers for this response.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return the raw body response.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return the decoded body response.
     *
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
     * Returns true if FCM returned an error message.
     *
     * @return boolean
     */
    public function isError()
    {
        return isset($this->decodedBody['results']['error']);
    }

    /**
     * Throws the exception.
     *
     * @throws FCMPHPException
     */
    public function throwException()
    {
        throw $this->thrownException;
    }

    /**
     * Instantiates an exception to be thrown later.
     */
    public function makeException()
    {
        $this->thrownException = FCMPHPResponseException::create($this);
    }

    /**
     * Returns the exception that was thrown for this request.
     *
     * @return FCMPHPResponseÉxception|null
     */
    public function getThrownException()
    {
        return $this->thrownException;
    }

    /**
     * Convert the raw response into an array.
     *
     * FCM will return just one type of response JSON(P)
     */
    public function decodeBody()
    {

        $this->decodedBody = json_decode($this->body, true);

        if (!is_array($this->decodedBody)) {
            $this->decodedBody = [];
        }

        if ($this->isError()) {
            $this->makeException();
        }
    }

    /**
     * Return if one or more message failure on send.
     *
     * FCM will return failure on body with code 200
     */
    public function hasFailure()
    {
        if(!empty($this->decodedBody['failure'])){
            return array('count' => $this->decodedBody['failure'], 'error' => $this->decodedBody['results']);    
        } else{
            return false;
        }
    }
}
