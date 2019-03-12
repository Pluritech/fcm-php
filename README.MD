# Google FCM for PHP (v1)

This repository contains the open source PHP SDK that allows you to access the Google FCM Platform from your PHP app.


## Installation

The Google FCM PHP SDK can be installed with [Composer](https://getcomposer.org/). Run this command:

```sh
composer require danielprr/fcm-php
```

## Usage

> **Note:** This version of the Google FCM SDK for PHP requires PHP 5.3 or greater.

Simple Send push example.

```php
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed

$FCMPHP = new \FCMPHP\FCMPHP([
    'fcm_server_key' => '{server-key}'
]);

$FCMNotification = new \FCMPHP\FCMNotification();

$FCMNotification->setTitle('{push-title}');
$FCMNotification->setBody('{push-description}');
$FCMNotification->setPriority('{push-priority}'); //high or normal
$FCMNotification->setDevices([ 
     '{device-id}'
    ,'{device-id}'
]); //One or more devices

$request = $FCMPHP->send($FCMNotification);

//Http error
if($request->getHttpStatusCode() != 200){
    $code = $request->getHttpStatusCode();
    $body = $request->getBody();
}

//One or more failure on send push to FCM
if($failure = $request->hasFailure()){
    $count = $failure['count'];
    $error = $failure['error'];
}

```

## Documentation

In progress.


## Tests

In progress.


## Contributing

Feel free to make your pull request, chip in to suggestion or report issues.


## License

MIT © [Guilherme Valentim](mailto:valentim.guilherme@gmail.com)