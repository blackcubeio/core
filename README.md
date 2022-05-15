Blackcube Core
==============

[![pipeline status](https://code.redcat.io/blackcube/core/badges/master/pipeline.svg)](https://code.redcat.io/blackcube/core/commits/master)
[![coverage report](https://code.redcat.io/blackcube/core/badges/master/coverage.svg)](https://code.redcat.io/blackcube/core/commits/master)

Pre-requisites
--------------

 * PHP 7.4+
   * Extension `dom`
   * Extension `fileinfo`
   * Extension `intl`
   * Extension `json`
   * Extension `mbstring`
 * Apache or NginX

Pre-flight
----------

Add blackcube core to the project

```
composer require "blackcube/core" 
```
   
Installation
------------

> **Beware**: `Blackcube core` can be used in stand alone but `Blackcube admin` is recommended 


### Inject Blackcube core in application

```php 
// main configuration file
   'container' => [
      'singletons' => [
         // local filesystem
         blackcube\core\components\Flysystem::class => [
            'class' => blackcube\core\components\FlysystemLocal::class,
            'path' => getstrenv('FILESYSTEM_LOCAL_PATH'),
         ],
         // or s3
         blackcube\core\components\Flysystem::class => [
            'class' => blackcube\core\components\FlysystemAwsS3::class,
           'key' => getstrenv('FILESYSTEM_S3_KEY'),
           'secret' => getstrenv('FILESYSTEM_S3_SECRET'),
           'bucket' => getstrenv('FILESYSTEM_S3_BUCKET'),
           'region' => getstrenv('FILESYSTEM_S3_REGION'),
           'version' => 'latest',
           'endpoint' => getstrenv('FILESYSTEM_S3_ENDPOINT'),
           'pathStyleEndpoint' => getboolenv('FILESYSTEM_S3_PATH_STYLE'),
         ],
      ]
   ],
// ...
    'bootstrap' => [
        // ... boostrapped modules
        'blackcube', // blackcube core
    ],
    'modules' => [
        // ... other modules
        'blackcube' => [
            'class' => blackcube\core\Module::class,
            'plugins' => [
               // additional plugins
            ],
            'cmsEnabledmodules' => [
               // additional modules
            ],
            'allowedParameterDomains' => ['],
            // override components if needed
            'components' => [
               'db' => ...
               'cache' => ...
               'fs' => ...
            ],
            /// end override
        ],
    ],
// ...
```

### Update DB

Add needed tables in DB

```
php yii.php migrate
```

Init database with basic stuff

```
php yii.php bc:init
```
 
> Blackcube core is now ready, you can use it
