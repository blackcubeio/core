Blackcube Core
==============

[![Latest Stable Version](https://poser.pugx.org/blackcube/core/v)](https://packagist.org/packages/blackcube/core) 
[![Total Downloads](https://poser.pugx.org/blackcube/core/downloads)](https://packagist.org/packages/blackcube/core) 
[![Latest Unstable Version](https://poser.pugx.org/blackcube/core/v/unstable)](https://packagist.org/packages/blackcube/core) 
[![License](https://poser.pugx.org/blackcube/core/license)](https://packagist.org/packages/blackcube/core) 
[![PHP Version Require](https://poser.pugx.org/blackcube/core/require/php)](https://packagist.org/packages/blackcube/core)

[![pipeline status](https://code.redcat.io/blackcube/core/badges/devel-3.x/pipeline.svg)](https://code.redcat.io/blackcube/core/commits/devel-3.x)
[![coverage report](https://code.redcat.io/blackcube/core/badges/devel-3.x/coverage.svg)](https://code.redcat.io/blackcube/core/commits/devel-3.x)

Pre-requisites
--------------

 * PHP 8.2+
   * Extension `dom`
   * Extension `fileinfo`
   * Extension `intl`
   * Extension `json`
   * Extension `mbstring`
   * Extension `xmlreader`
   * Extension `dom`

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

Add translations for new code
-----------------------------

Managing translations can be a little tricky, here is a way to do it:

### Prepare `msgconfig.php`

`msgconfig.php` is a configuration file for `yii` message command, it should be located in the root of the project.
```php
return [
    // string, required, root directory of all source files
    'sourcePath' => __DIR__ . DIRECTORY_SEPARATOR . 'src',
    // array, required, list of language codes that the extracted messages
    // should be translated to. For example, ['zh-CN', 'de'].
    'languages' => ['en'],
    // string, the name of the function for translating messages.
    // Defaults to 'Yii::t'. This is used as a mark to find the messages to be
    // translated. You may use a string for single function name or an array for
    // multiple function names.
    'translator' => 'Module::t',
    // boolean, whether to sort messages by keys when merging new messages
    // with the existing ones. Defaults to false, which means the new (untranslated)
    // messages will be separated from the old (translated) ones.
    'sort' => false,
    // boolean, whether to remove messages that no longer appear in the source code.
    // Defaults to false, which means these messages will NOT be removed.
    'removeUnused' => false,
    // boolean, whether to mark messages that no longer appear in the source code.
    // Defaults to true, which means each of these messages will be enclosed with a pair of '@@' marks.
    'markUnused' => true,
    // array, list of patterns that specify which files (not directories) should be processed.
    // If empty or not set, all files will be processed.
    // See helpers/FileHelper::findFiles() for pattern matching rules.
    // If a file/directory matches both a pattern in "only" and "except", it will NOT be processed.
    'only' => ['*.php'],
    // array, list of patterns that specify which files/directories should NOT be processed.
    // If empty or not set, all files/directories will be processed.
    // See helpers/FileHelper::findFiles() for pattern matching rules.
    // If a file/directory matches both a pattern in "only" and "except", it will NOT be processed.
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
    ],

    /**/
    // 'po' output format is for saving messages to gettext po files.
    'format' => 'po',
    // Root directory containing message translations.
    'messagePath' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src/i18n',
    // Name of the file that will be used for translations.
    'catalog' => 'messages',
    // boolean, whether the message file should be overwritten with the merged messages
    'overwrite' => true,
    /**/
];
```

Execute the command

```
php yii.php message src/i18n/msgconfig.php
```

Once done, you can update the translations in `src/i18n/en/messages.po`

> **Beware**: `src/i18n/en/messages.po` is the only file that should be updated, move it to `src/i18n/messages.pot` and revert back `src/i18n/en/messages.po` to its original state
> Change msgctxt "XXX" to msgctxt "blackcube/core/XXX" to match Blackcube core context in `src/i18n/messages.pot`

```bash
mv src/i18n/en/messages.po src/i18n/messages.pot
git checkout src/i18n/en/messages.po
sed -i 's/msgctxt "([^"]+)"/msgctxt "blackcube\/core\/$1"/g' src/i18n/messages.pot
```

Once done, you can update/add translations in `src/i18n/<lang>/messages.po`using poedit or any other tool.

Running tests
-------------

```
# once databe is installed and pre-populated with `php yii.php bc:init`
./vendor/bin/codecept run
```
