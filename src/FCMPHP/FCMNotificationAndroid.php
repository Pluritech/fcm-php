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

/**
 * Class FCMPHP
 *
 * @package FCMPHP
 */
class FCMNotificationAndroid extends FCMNotification
{

    /**
     * @var Click action
     */
    protected $click_action;


    /**
     * Instantiates a notification entity class object.
     *
     * @param array  $devices
     * @param string $title
     * @param string $body
     * @param string $click_action
     * @param string $sound
     * @param string $color
     * @param string $icon
     * @param string $priority
     *
     */
    public function __construct(array $config = [])
    {
    	parent::__construct($config);

        $config = array_merge([
            'click_action' => null,
        ], $config);

        $this->setClickAction($config['click_action']);
    }

    /**
     * Returns the devices target.
     *
     * @return Devices
     *
     * @throws InvalidArgumentException
     */
    public function formatBody(){

    	$body = parent::formatBody();
        
        if(!is_array($body['data'])){
            $body['data'] = json_decode($body['data'], true);
        }

        $body['data'] = array_merge($body['data'], array(
        		 "title" => $this->getTitle()
        		,"body" => $this->getBody()
        		,"click_action" => $this->getClickAction()
        		,"content-available" => $this->getContentAvailable() //This "-" is not my fault. Sorry.
        		,"sound" => $this->getSound() 
        		,"color" => $this->getColor() 
        		,"icon" => $this->getIcon() 
        	)
       	);

        $body['data'] = json_encode($body['data']);
       	return $body;
    }

    /**
     * Returns the notification clickAction.
     *
     * @return ClickAction
     */
    public function getClickAction()
    {
        return $this->click_action;
    }

    /**
     * Set the notification click_action.
     */
    private function setClickAction($click_action)
    {
        $this->click_action = $click_action;
    }
}