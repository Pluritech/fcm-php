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

use FCMPHP\Url\FCMPHPUrlManipulator;
use FCMPHP\Http\RequestBodyJsonEncoded;
use FCMPHP\Exceptions\FCMPHPException;

/**
 * Class Request
 *
 * @package FCMPHP
 */
class FCMPHPRequest
{

    /**
     * @var string The HTTP method for this request.
     */
    protected $method;

    /**
     * @var string The fcm endpoint for this request.
     */
    protected $endpoint;

    /**
     * @var array The headers to send with this request.
     */
    protected $headers = array();

    /**
     * @var array The parameters to send with this request.
     */
    protected $params = array();


    /**
     * Creates a new Request entity.
     *
     * @param string|null             $method
     * @param string|null             $endpoint
     * @param array|null              $params
     */
    public function __construct($method = null, $endpoint = null, array $params = array())
    {
        $this->setMethod($method);
        $this->setEndpoint($endpoint);
        $this->setParams($params);
    }

    /**
     * Set the HTTP method for this request.
     *
     * @param string
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    /**
     * Return the HTTP method for this request.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Validate that the HTTP method is set.
     *
     * @throws FCMPHPException
     */
    public function validateMethod()
    {
        if (!$this->method) {
            throw new FCMPHPException('HTTP method not specified.');
        }

        if (!in_array($this->method, array('GET', 'POST', 'DELETE'))) {
            throw new FCMPHPException('Invalid HTTP method specified.');
        }
    }

    /**
     * Set the endpoint for this request.
     *
     * @param string
     *
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Return the endpoint for this request.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Generate and return the headers for this request.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the headers for this request.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Set the params for this request.
     *
     * @param array $params
     *
     * @throws FCMPHPException
     */
    public function setParams($params = array())
    {
        $this->params = $params;
    }

    /**
     * Returns the body of the request as json encoded.
     *
     * @return RequestBodyJsonEncoded
     */
    public function getJsonEncodedBody()
    {
        $params = $this->getPostParams();

        return new RequestBodyJsonEncoded($params);
    }

    /**
     * Generate and return the params for this request.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Only return params on POST requests.
     *
     * @return array
     */
    public function getPostParams()
    {
        if ($this->getMethod() === 'POST') {
            return $this->getParams();
        }

        return array();
    }

    /**
     * Generate and return the URL for this request.
     *
     * @return string
     */
    public function getUrl()
    {
        $this->validateMethod();

        $endpoint = FCMPHPUrlManipulator::forceSlashPrefix($this->getEndpoint());

        $url = $endpoint;

        if ($this->getMethod() !== 'POST') {
            $params = $this->getParams();
            $url = FCMPHPUrlManipulator::appendParamsToUrl($url, $params);
        }

        return $url;
    }
}
