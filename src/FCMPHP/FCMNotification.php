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
    const DEFAULT_BADGE = '0';

    /**
     * @const string notification icon.
     */
    const DEFAULT_CONTENT_AVAILABLE = true;

    /**
     * @var 
     */
    protected $devices; //Analisar entidade

    /**
     * @var mixed Notification title
     */
    protected $title;

    /**
     * @var mixed Notification body
     */
    protected $body;

    /**
     * @var mixed Content available
     */
    protected $content_available;

    /**
     * @var mixed Sound
     */
    protected $sound; //Analisar entidade

    /**
     * @var mixed Color
     */
    protected $color;
    /**
     * @var mixed badge
     */
    protected $badge;

    /**
     * @var mixed Icon
     */
    protected $icon;

    /**
     * @var mixed Priority
     */
    protected $priority;

    /**
     * @var mixed Data non structured
     */
    protected $data;
    
    /**
     * @var string Topic message
     */
    protected $topic;

    /**
     * Instantiates a notification entity class object.
     *
     * @param array  $devices
     * @param string $title
     * @param string $body
     * @param string $sound
     * @param string $color
     * @param string $icon
     * @param string $badge
     * @param string $priority
     *
     */
    public function __construct(array $config = array())
    {
        $config = array_merge(array(
            'devices' => array(),
            'title' => null,
            'body' => null,
            'badge' => static::DEFAULT_BADGE,
            'content_available' => static::DEFAULT_CONTENT_AVAILABLE,
            'sound' => static::DEFAULT_SOUND,
            'color' => static::DEFAULT_COLOR,
            'icon' => static::DEFAULT_ICON,
            'priority' => static::DEFAULT_PRIORITY,
            'data' => array(),
            'topic' => null
        ), $config);

        $this->setDevices($config['devices']);
        $this->setTitle($config['title']);
        $this->setBody($config['body']);
        $this->setContentAvailable($config['content_available']);
        $this->setSound($config['sound']);
        $this->setColor($config['color']);
        $this->setIcon($config['icon']);
        $this->setPriority($config['priority']);
        $this->setData($config['data']);
        $this->setBadge($config['badge']);
        $this->setTopic($config['topic']);
    }
    
    /**
     * Format body
     *
     * @return mixed Devices
     *
     * @throws \InvalidArgumentException
     */
    public function formatBody(){

        if (!$this->getDevices() && !$this->getTopic()) {
            throw new \InvalidArgumentException('You must set at least one device or set a topic to send a notification..');
        }

        if (!$this->getTitle()) {
            throw new \InvalidArgumentException('You need set the notification title FCMNotification.title.');
        }

        if (!$this->getBody()) {
            throw new \InvalidArgumentException('You need set the notification body FCMNotification.body.');
        }
        
        $notification = array(
            "priority" => $this->getPriority(),
            "data" => $this->getData(),
            "notification" => array(
                "title" => $this->getTitle(),
                "body" => $this->getBody(),
                "sound" => $this->getSound()
            )
        );
        
        if ($this->getDevices()) {
            $notification['registration_ids'] = $this->getDevices();
        } else if ($this->getTopic()) {
            $notification['to'] = "/topics/{$this->getTopic()}";
        }

        return $notification;
    }

    /**
     * Returns the devices target.
     *
     * @return mixed Devices
     */
    public function getDevices()
    {
        return $this->devices;
    }
    
    /**
     * Returns defined topic
     * 
     * @return string Topic
     */
    public function getTopic()
    {
        return $this->topic;
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
     * @return mixed Title
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
     * @return mixed Body
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
     * @return mixed Content available
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
     * @return mixed Sound
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
     * @return mixed Color
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
     * Returns the notification badge.
     *
     * @return mixed Color
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * Set the notification badge.
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }

    /**
     * Returns the notification icon.
     *
     * @return mixed icon
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
     * @return mixed priority
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
     * @return mixed data
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Define topic message
     *
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
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
           $this->data = (Object) array();
        }
        //to fix capacitor notification integration that converts 1° level with equal
        $this->data = array('object' => $data);
    }
}