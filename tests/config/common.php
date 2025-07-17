<?php
/**
 * common.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package app\config
 */

use blackcube\core\components\FlysystemLocal;
use creocoder\flysystem\AwsS3Filesystem;
use creocoder\flysystem\LocalFilesystem;
use yii\caching\CacheInterface;
use yii\caching\DummyCache;
use yii\db\Connection;
use yii\db\mysql\Schema as MysqSchema;
use yii\db\pgsql\Schema as PgsqlSchema;
use yii\i18n\Formatter;
use yii\log\FileTarget;
use yii\rbac\DbManager;
use blackcube\core\components\Flysystem;

Yii::setAlias('@tmpfs', dirname(__DIR__));
$config = [
    'sourceLanguage' => 'en',
    'language' => 'en-US',
    'timezone' => 'Europe/Paris',
    'extensions' => require dirname(__DIR__, 2) . '/vendor/yiisoft/extensions.php',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@blackcube/core' => dirname(__DIR__, 2).'/src',
        '@data' => dirname(__DIR__, 2).'/data',
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'version' => '3.x',
    'bootstrap' => [
        'log',
        'blackcube',
    ],
    'container' => [
        'definitions' => [
        ],
        'singletons' => [
            /**/
            Connection::class => [
                'charset' => 'utf8',
                'dsn' => getstrenv('DB_DRIVER').':host=' . getstrenv('DB_HOST') . ';port=' . getstrenv('DB_PORT') . ';dbname=' . getstrenv('DB_DATABASE'),
                'username' => getstrenv('DB_USER'),
                'password' => getstrenv('DB_PASSWORD'),
                'tablePrefix' => getstrenv('DB_TABLE_PREFIX'),
                'enableSchemaCache' => getboolenv('DB_SCHEMA_CACHE'),
                'schemaCacheDuration' => getintenv('DB_SCHEMA_CACHE_DURATION'),
                // 'on afterOpen' => function($event) {
                //     // $event->sender refers to the DB connection
                //     $event->sender->createCommand("SET time_zone = '".Yii::$app->timeZone."'")->execute();
                // }
            ],
            /**/
            CacheInterface::class => DummyCache::class,
        ],
    ],
    'modules' => [
        'blackcube' => [
            'class' => blackcube\core\Module::class,
        ],
    ],
    'components' => [

        'db' => Connection::class,
        'cache' => CacheInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning', 'profile'],
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'blackcube*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@app/i18n',
                    'fileMap' => [
                        'blackcube/models' => 'models.php',
                    ]
                ]
            ]
        ]
    ],
    'params' => [
    ],
];

if (getstrenv('DB_DRIVER') === 'pgsql') {
    $config['components']['db']['schemaMap'] = [
        getstrenv('DB_DRIVER') => [
            'class' => getstrenv('DB_DRIVER') === 'pgsql' ? PgsqlSchema::class : MysqSchema::class,
            'defaultSchema' => getstrenv('DB_SCHEMA')
        ]
    ];
}

/**/
if (getstrenv('FILESYSTEM_TYPE') === 'local') {
    $config['container']['singletons'][Flysystem::class] = [
        'class' => \blackcube\core\components\FlysystemLocal::class,
        'path' => getstrenv('FILESYSTEM_LOCAL_PATH'),
    ];
    $config['components']['fs'] = Flysystem::class;
} elseif (getstrenv('FILESYSTEM_TYPE') === 's3') {
    $config['container']['singletons'][Flysystem::class] = [
        '__class' => \blackcube\core\components\FlysystemAwsS3::class,
        'key' => getstrenv('FILESYSTEM_S3_KEY'),
        'secret' => getstrenv('FILESYSTEM_S3_SECRET'),
        'bucket' => getstrenv('FILESYSTEM_S3_BUCKET'),
        'region' => getstrenv('FILESYSTEM_S3_REGION'),
        'version' => 'latest',
        'endpoint' => getstrenv('FILESYSTEM_S3_ENDPOINT'),
        'pathStyleEndpoint' => getstrenv('FILESYSTEM_S3_PATH_STYLE'),
    ];
    $config['components']['fs'] = Flysystem::class;
}
/**/

return $config;
