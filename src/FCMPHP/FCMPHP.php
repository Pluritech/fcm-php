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

use FCMPHP\Exceptions\FCMPushException;
use FCMPHP\Exceptions\FCMPHPResponseException;
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
     * @var FacebookApp The FacebookApp entity.
     */
    protected $app;

    /**
     * @var Project FCM server key.
     */
    protected $fcm_server_key;


    /**
     * Instantiates a new FCMPHP super-class object.
     *
     * @param array $config
     *
     * @throws FCMPushException
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
             'fcm_send_message'    => static::FCM_SEND_MESSAGE
            ,'http_client_handler' => null
        ], $config);

        if (!$config['fcm_server_key']) {
            throw new FCMPushException('Required "fcm_server_key" key not supplied in config');
        }

        $this->client = new FCMPHPClient(
        	HttpClientsFactory::createHttpClient($config['http_client_handler'])
        );

    }

    /**
     * Returns the FacebookApp entity.
     *
     * @return FacebookApp
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Returns the FacebookClient service.
     *
     * @return FacebookClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the OAuth 2.0 client service.
     *
     * @return OAuth2Client
     */
    public function getOAuth2Client()
    {
        if (!$this->oAuth2Client instanceof OAuth2Client) {
            $app = $this->getApp();
            $client = $this->getClient();
            $this->oAuth2Client = new OAuth2Client($app, $client, $this->defaultGraphVersion);
        }

        return $this->oAuth2Client;
    }

    /**
     * Returns the last response returned from Graph.
     *
     * @return FacebookResponse|FacebookBatchResponse|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Returns the URL detection handler.
     *
     * @return UrlDetectionInterface
     */
    public function getUrlDetectionHandler()
    {
        return $this->urlDetectionHandler;
    }

    /**
     * Changes the URL detection handler.
     *
     * @param UrlDetectionInterface $urlDetectionHandler
     */
    private function setUrlDetectionHandler(UrlDetectionInterface $urlDetectionHandler)
    {
        $this->urlDetectionHandler = $urlDetectionHandler;
    }

    /**
     * Returns the default AccessToken entity.
     *
     * @return AccessToken|null
     */
    public function getDefaultAccessToken()
    {
        return $this->defaultAccessToken;
    }

    /**
     * Sets the default access token to use with requests.
     *
     * @param AccessToken|string $accessToken The access token to save.
     *
     * @throws \InvalidArgumentException
     */
    public function setDefaultAccessToken($accessToken)
    {
        if (is_string($accessToken)) {
            $this->defaultAccessToken = new AccessToken($accessToken);

            return;
        }

        if ($accessToken instanceof AccessToken) {
            $this->defaultAccessToken = $accessToken;

            return;
        }

        throw new \InvalidArgumentException('The default access token must be of type "string" or Facebook\AccessToken');
    }

    /**
     * Returns the default Graph version.
     *
     * @return string
     */
    public function getDefaultGraphVersion()
    {
        return $this->defaultGraphVersion;
    }

    /**
     * Returns the redirect login helper.
     *
     * @return FacebookRedirectLoginHelper
     */
    public function getRedirectLoginHelper()
    {
        return new FacebookRedirectLoginHelper(
            $this->getOAuth2Client(),
            $this->persistentDataHandler,
            $this->urlDetectionHandler,
            $this->pseudoRandomStringGenerator
        );
    }

    /**
     * Returns the JavaScript helper.
     *
     * @return FacebookJavaScriptHelper
     */
    public function getJavaScriptHelper()
    {
        return new FacebookJavaScriptHelper($this->app, $this->client, $this->defaultGraphVersion);
    }

    /**
     * Returns the canvas helper.
     *
     * @return FacebookCanvasHelper
     */
    public function getCanvasHelper()
    {
        return new FacebookCanvasHelper($this->app, $this->client, $this->defaultGraphVersion);
    }

    /**
     * Returns the page tab helper.
     *
     * @return FacebookPageTabHelper
     */
    public function getPageTabHelper()
    {
        return new FacebookPageTabHelper($this->app, $this->client, $this->defaultGraphVersion);
    }

    /**
     * Sends a GET request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function get($endpoint, $accessToken = null, $eTag = null, $graphVersion = null)
    {
        return $this->sendRequest(
            'GET',
            $endpoint,
            $params = [],
            $accessToken,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Sends a POST request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function post($endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null)
    {
        return $this->sendRequest(
            'POST',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Sends a DELETE request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function delete($endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null)
    {
        return $this->sendRequest(
            'DELETE',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Sends a request to Graph for the next page of results.
     *
     * @param GraphEdge $graphEdge The GraphEdge to paginate over.
     *
     * @return GraphEdge|null
     *
     * @throws FacebookSDKException
     */
    public function next(GraphEdge $graphEdge)
    {
        return $this->getPaginationResults($graphEdge, 'next');
    }

    /**
     * Sends a request to Graph for the previous page of results.
     *
     * @param GraphEdge $graphEdge The GraphEdge to paginate over.
     *
     * @return GraphEdge|null
     *
     * @throws FacebookSDKException
     */
    public function previous(GraphEdge $graphEdge)
    {
        return $this->getPaginationResults($graphEdge, 'previous');
    }

    /**
     * Sends a request to Graph for the next page of results.
     *
     * @param GraphEdge $graphEdge The GraphEdge to paginate over.
     * @param string    $direction The direction of the pagination: next|previous.
     *
     * @return GraphEdge|null
     *
     * @throws FacebookSDKException
     */
    public function getPaginationResults(GraphEdge $graphEdge, $direction)
    {
        $paginationRequest = $graphEdge->getPaginationRequest($direction);
        if (!$paginationRequest) {
            return null;
        }

        $this->lastResponse = $this->client->sendRequest($paginationRequest);

        // Keep the same GraphNode subclass
        $subClassName = $graphEdge->getSubClassName();
        $graphEdge = $this->lastResponse->getGraphEdge($subClassName, false);

        return count($graphEdge) > 0 ? $graphEdge : null;
    }

    /**
     * Sends a request to Graph and returns the result.
     *
     * @param string                  $method
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function sendRequest($method, $endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null)
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;
        $request = $this->request($method, $endpoint, $params, $accessToken, $eTag, $graphVersion);

        return $this->lastResponse = $this->client->sendRequest($request);
    }

    /**
     * Sends a batched request to Graph and returns the result.
     *
     * @param array                   $requests
     * @param AccessToken|string|null $accessToken
     * @param string|null             $graphVersion
     *
     * @return FacebookBatchResponse
     *
     * @throws FacebookSDKException
     */
    public function sendBatchRequest(array $requests, $accessToken = null, $graphVersion = null)
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;
        $batchRequest = new FacebookBatchRequest(
            $this->app,
            $requests,
            $accessToken,
            $graphVersion
        );

        return $this->lastResponse = $this->client->sendBatchRequest($batchRequest);
    }

    /**
     * Instantiates an empty FacebookBatchRequest entity.
     *
     * @param  AccessToken|string|null $accessToken  The top-level access token. Requests with no access token
     *                                               will fallback to this.
     * @param  string|null             $graphVersion The Graph API version to use.
     * @return FacebookBatchRequest
     */
    public function newBatchRequest($accessToken = null, $graphVersion = null)
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;

        return new FacebookBatchRequest(
            $this->app,
            [],
            $accessToken,
            $graphVersion
        );
    }

    /**
     * Instantiates a new FacebookRequest entity.
     *
     * @param string                  $method
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookRequest
     *
     * @throws FacebookSDKException
     */
    public function request($method, $endpoint, array $params = [], $accessToken = null, $eTag = null, $graphVersion = null)
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;

        return new FacebookRequest(
            $this->app,
            $accessToken,
            $method,
            $endpoint,
            $params,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Factory to create FacebookFile's.
     *
     * @param string $pathToFile
     *
     * @return FacebookFile
     *
     * @throws FacebookSDKException
     */
    public function fileToUpload($pathToFile)
    {
        return new FacebookFile($pathToFile);
    }

    /**
     * Factory to create FacebookVideo's.
     *
     * @param string $pathToFile
     *
     * @return FacebookVideo
     *
     * @throws FacebookSDKException
     */
    public function videoToUpload($pathToFile)
    {
        return new FacebookVideo($pathToFile);
    }

    /**
     * Upload a video in chunks.
     *
     * @param int $target The id of the target node before the /videos edge.
     * @param string $pathToFile The full path to the file.
     * @param array $metadata The metadata associated with the video file.
     * @param string|null $accessToken The access token.
     * @param int $maxTransferTries The max times to retry a failed upload chunk.
     * @param string|null $graphVersion The Graph API version to use.
     *
     * @return array
     *
     * @throws FacebookSDKException
     */
    public function uploadVideo($target, $pathToFile, $metadata = [], $accessToken = null, $maxTransferTries = 5, $graphVersion = null)
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;

        $uploader = new FacebookResumableUploader($this->app, $this->client, $accessToken, $graphVersion);
        $endpoint = '/'.$target.'/videos';
        $file = $this->videoToUpload($pathToFile);
        $chunk = $uploader->start($endpoint, $file);

        do {
            $chunk = $this->maxTriesTransfer($uploader, $endpoint, $chunk, $maxTransferTries);
        } while (!$chunk->isLastChunk());

        return [
          'video_id' => $chunk->getVideoId(),
          'success' => $uploader->finish($endpoint, $chunk->getUploadSessionId(), $metadata),
        ];
    }

    /**
     * Attempts to upload a chunk of a file in $retryCountdown tries.
     *
     * @param FacebookResumableUploader $uploader
     * @param string $endpoint
     * @param FacebookTransferChunk $chunk
     * @param int $retryCountdown
     *
     * @return FacebookTransferChunk
     *
     * @throws FacebookSDKException
     */
    private function maxTriesTransfer(FacebookResumableUploader $uploader, $endpoint, FacebookTransferChunk $chunk, $retryCountdown)
    {
        $newChunk = $uploader->transfer($endpoint, $chunk, $retryCountdown < 1);

        if ($newChunk !== $chunk) {
            return $newChunk;
        }

        $retryCountdown--;

        // If transfer() returned the same chunk entity, the transfer failed but is resumable.
        return $this->maxTriesTransfer($uploader, $endpoint, $chunk, $retryCountdown);
    }
}
