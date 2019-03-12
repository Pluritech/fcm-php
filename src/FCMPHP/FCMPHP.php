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

use FCMPHP\Exceptions\FCMPHPException;
use FCMPHP\HttpClients\HttpClientsFactory;

/**
 * Class FCMPHP
 *
 * @package FCMPHP
 */
class FCMPHP
{

    /**
     * @const string URL on FCM to send message.
     */
    const FCM_SEND_MESSAGE = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @const string FCM SERVER KEY
     */
    const FCM_SERVER_KEY = 'FCM_SERVER_KEY';

    /**
     * @var FCMPHPClient The fcm client service.
     */
    protected $client;

    /**
     * @var mixed Project FCM server key.
     */
    protected $fcm_server_key;

    /**
     * @var FCMPHPResponse|null Stores the last request made to FCM.
     */
    protected $lastResponse;

    /**
     * Instantiates a new FCMPHP super-class object.
     *
     * @param array $config
     *
     * @throws FCMPHPException
     */
    public function __construct($config = array())
    {
        $config = array_merge(array(
             'fcm_server_key'      => getenv(static::FCM_SERVER_KEY)
            ,'fcm_send_message'    => static::FCM_SEND_MESSAGE
            ,'http_client_handler' => null
        ), $config);

        if (!$config['fcm_server_key']) {
            throw new FCMPHPException('Required "fcm_server_key" key not supplied in config and could not find fallback environment variable "'. static::FCM_SERVER_KEY . '"');
        } else{
            $this->setFcmServerKey($config['fcm_server_key']);
        }

        $this->client = new FCMPHPClient(
            HttpClientsFactory::createHttpClient($config['http_client_handler'])
        );
    }

    /**
     * Returns the FacebookClient service.
     *
     * @return mixed FacebookClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the last response returned from FCM.
     *
     * @return FCMPHPResponse|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Sends a notification to FCM
     *
     * @param string                  $endpoint
     * @param array                   $params
     *
     * @return FCMPHPResponse
     *
     * @throws FCMPHPException
     */
    public function send(FCMNotification $notification){

        if (!$notification instanceof FCMNotification) {
            throw new \InvalidArgumentException('Argument for send() must be of FCMNotification.');
        }

        return $this->post(
             'send'
            ,$notification->formatBody()
        );
    }

    /**
     * Sends a POST request to FCM and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     *
     * @return FCMPHPResponse
     *
     * @throws FCMPHPException
     */
    public function post($endpoint, $params = array())
    {
        return $this->sendRequest(
            'POST',
            $endpoint,
            $params
        );
    }

    /**
     * Sends a request to FCM and returns the result.
     *
     * @param string      $method
     * @param string      $endpoint
     * @param array       $params
     *
     * @return FCMPHPResponse
     *
     * @throws FCMPHPException
     */
    public function sendRequest($method, $endpoint, $params = array())
    {
        $request = $this->request($method, $endpoint, $params);

        return $this->lastResponse = $this->client->sendRequest($request);
    }

    /**
     * Instantiates a new FCMPHPRequest entity.
     *
     * @param string      $method
     * @param string      $endpoint
     * @param array       $params
     *
     * @return FCMPHPResponse
     *
     * @throws FCMPHPException
     */
    public function request($method, $endpoint, array $params = array())
    {
        $request = new FCMPHPRequest(
             $method
            ,$endpoint
            ,$params
        );

        $request->setHeaders(array(
            'Authorization' => 'key=' . $this->getFcmServerKey()
        ));

        return $request;
    }

    /**
     * Returns the server key.
     *
     * @return mixed Fcm Server Key
     */
    public function getFcmServerKey()
    {
        return $this->fcm_server_key;
    }

    /**
     * Set fcm server key
     */
    public function setFcmServerKey($fcm_server_key)
    {
        $this->fcm_server_key = $fcm_server_key;
    }

}
