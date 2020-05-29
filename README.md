Blackcube Core
==============

[![pipeline status](https://code.redcat.io/blackcube/core/badges/master/pipeline.svg)](https://code.redcat.io/blackcube/core/commits/master)
[![coverage report](https://code.redcat.io/blackcube/core/badges/master/coverage.svg)](https://code.redcat.io/blackcube/core/commits/master)

Pre-requisites
--------------

 * PHP 7.2+
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
// ...
    'bootstrap' => [
        // ... boostrapped modules
        'blackcube', // blackcube core
    ],
    'modules' => [
        // ... other modules
        'blackcube' => [
            'class' => blackcube\core\Module::class,
        ],
    ],
// ...
```

### Update DB

Add needed tables in DB

```
php yii.php bcore:migrate
```

Init database with basic stuff

```
php yii.php bcore:init
```
 
> Blackcube core is now ready, you can use it
