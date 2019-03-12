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
namespace FCMPHP\HttpClients;

use InvalidArgumentException;
use Exception;

class HttpClientsFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @param HttpClientInterface|string|null $handler
     *
     * @throws Exception                If the cURL extension or the Guzzle client aren't available (if required).
     * @throws InvalidArgumentException If the http client handler isn't "curl", "guzzle", or an instance of FCMPHP\HttpClients\HttpClientInterface.
     *
     * @return HttpClientInterface
     */
    public static function createHttpClient($handler)
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }

        if ($handler instanceof HttpClientInterface) {
            return $handler;
        }

        if ('curl' === $handler) {
            if (!extension_loaded('curl')) {
                throw new Exception('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new HttpCurlClient();
        }

        //@todo Others HTTP client like guzzle can be returned here when developed

        throw new InvalidArgumentException('The http client handler must be set to "curl" or a instance of FCMPHP\HttpClients\HttpClientInterface');
    }

    /**
     * Detect default HTTP client.
     *
     * @return HttpClientInterface
     */
    private static function detectDefaultClient()
    {
        if (extension_loaded('curl')) {
            return new HttpCurlClient();
        }

        //@todo Others HTTP client like guzzle can be returned here when developed
    }
}
