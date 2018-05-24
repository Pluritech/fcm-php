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
class FCMNotification
{

    /**
     * @const string priority on FCM to send message. Can be normal or high
     */
    const DEFAULT_PRIORITY = 'normal';

    /**
     * @const string sound on receive push.
     */
    const DEFAULT_SOUND = 'default';

    /**
     * @const string notification color.
     */
    const DEFAULT_COLOR = '#dd0000';

    /**
     * @const string notification icon.
     */
    const DEFAULT_ICON = 'fcm_push_icon';

    /**
     * @const string notification icon.
     */
    const DEFAULT_CONTENT_AVAILABLE = true;

    /**
     * @var 
     */
    protected $devices; //Analisar entidade

    /**
     * @var Notification title
     */
    protected $title;

    /**
     * @var Notification body
     */
    protected $body;

    /**
     * @var Content available
     */
    protected $content_available;

    /**
     * @var Sound
     */
    protected $sound; //Analisar entidade

    /**
     * @var Color
     */
    protected $color;

    /**
     * @var Icon
     */
    protected $icon;

    /**
     * @var Priority
     */
    protected $priority;

    /**
     * @var Data non structured
     */
    protected $data;

    /**
     * Instantiates a notification entity class object.
     *
     * @param array  $devices
     * @param string $title
     * @param string $body
     * @param string $sound
     * @param string $color
     * @param string $icon
     * @param string $priority
     *
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'devices' => array(),
            'title' => null,
            'body' => null,
            'content_available' => static::DEFAULT_CONTENT_AVAILABLE,
            'sound' => static::DEFAULT_SOUND,
            'color' => static::DEFAULT_COLOR,
            'icon' => static::DEFAULT_ICON,
            'priority' => static::DEFAULT_PRIORITY,
            'data' => array(),
        ], $config);

        $this->setDevices($config['devices']);
        $this->setTitle($config['title']);
        $this->setBody($config['body']);
        $this->setContentAvailable($config['content_available']);
        $this->setSound($config['sound']);
        $this->setColor($config['color']);
        $this->setIcon($config['icon']);
        $this->setPriority($config['priority']);
        $this->setData($config['data']);
    }

    /**
     * Format body
     *
     * @return Devices
     *
     * @throws InvalidArgumentException
     */
    public function formatBody(){

        if (!$this->getDevices()) {
            throw new \InvalidArgumentException('You need set one or more devices to send notification.');
        }

        if (!$this->getTitle()) {
            throw new \InvalidArgumentException('You need set the notification title FCMNotification.title.');
        }

        if (!$this->getBody()) {
            throw new \InvalidArgumentException('You need set the notification body FCMNotification.body.');
        }

        return array(
            "registration_ids" => $this->getDevices()
            ,"priority" => $this->getPriority()
            ,"data" => $this->getData()
        );
    }

    /**
     * Returns the devices target.
     *
     * @return Devices
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Set devices target.
     */
    private function setDevices($devices = array())
    {
        if (!is_array($devices)) {
            throw new \InvalidArgumentException('Devices must be array.');
        } 
        
        $this->devices = $devices;
    }

    /**
     * Returns the notification title.
     *
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the notification title.
     */
    private function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the notification body.
     *
     * @return Body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the notification body.
     */
    private function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Returns the notification content_available.
     *
     * @return content_available
     */
    public function getContentAvailable()
    {
        return $this->content_available;
    }

    /**
     * Set the notification content_available.
     */
    private function setContentAvailable($content_available)
    {
        $this->content_available = $content_available;
    }

    /**
     * Returns the notification sound.
     *
     * @return Sound
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * Set the notification sound.
     */
    private function setSound($sound)
    {
        $this->sound = $sound;
    }

    /**
     * Returns the notification color.
     *
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set the notification color.
     */
    private function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * Returns the notification color.
     *
     * @return Color
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set the notification icon.
     */
    private function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Returns the notification priority.
     *
     * @return priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the notification priority.
     */
    private function setPriority($priority)
    {
        if (!in_array($priority, array('high', 'normal'))) {
            throw new \InvalidArgumentException('Priority must be \'high\' or \'normal\'.');
        }
        $this->priority = $priority;
    }

    /**
     * Returns the notification data.
     *
     * @return data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the notification data.
     */
    private function setData($data = array())
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Data must be array.');
        }

        if(empty($data)){ //To fix json_encode
            $data = (Object) array();
        }

        $this->data = $data;
    }
}