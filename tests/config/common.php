<?php
/**
 * common.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package app\config
 */

use creocoder\flysystem\AwsS3Filesystem;
use creocoder\flysystem\LocalFilesystem;
use yii\db\Connection;
use yii\db\mysql\Schema as MysqSchema;
use yii\db\pgsql\Schema as PgsqlSchema;
use yii\i18n\Formatter;
use yii\log\FileTarget;
use yii\rbac\DbManager;
Yii::setAlias('@tmpfs', dirname(__DIR__));
$config = [
    'sourceLanguage' => 'en',
    'language' => 'en-US',
    'timezone' => 'Europe/Paris',
    'extensions' => require dirname(__DIR__, 2) . '/vendor/yiisoft/extensions.php',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@blackcube/core' => dirname(__DIR__, 2).'/src',
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'version' => $_ENV['APP_VERSION'],
    'bootstrap' => [
        'log',
        'blackcube',
    ],
    'modules' => [
        'blackcube' => [
            'class' => blackcube\core\Module::class,
        ],
    ],
    'components' => [
        'db' => [
            'class' => Connection::class,
            'charset' => 'utf8',
            'dsn' => $_ENV['DB_DRIVER'].':host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'tablePrefix' => $_ENV['DB_TABLE_PREFIX'],
            'enableSchemaCache' => $_ENV['DB_SCHEMA_CACHE'],
            'schemaCacheDuration' => $_ENV['DB_SCHEMA_CACHE_DURATION'],
        ],
        'cache' => [
            'class' => yii\caching\DummyCache::class,
            // 'class' => yii\caching\DbCache::class,
        ],
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

if ($_ENV['DB_DRIVER'] === 'pgsql') {
    $config['components']['db']['schemaMap'] = [
        $_ENV['DB_DRIVER'] => [
            'class' => $_ENV['DB_DRIVER'] === 'pgsql' ? PgsqlSchema::class : MysqSchema::class,
            'defaultSchema' => $_ENV['DB_SCHEMA']
        ]
    ];
}

/**/
if ($_ENV['FILESYSTEM_TYPE'] === 'local') {
    $config['components']['fs'] = [
        'class' => LocalFilesystem::class,
        'path' => $_ENV['FILESYSTEM_LOCAL_PATH'],
        'cache' => ($_ENV['FILESYSTEM_CACHE'] == 1) ? 'cache' : null,
        'cacheKey' => ($_ENV['FILESYSTEM_CACHE'] == 1) ? 'flysystem' : null,
        'cacheDuration' => ($_ENV['FILESYSTEM_CACHE'] == 1) ? $_ENV['FILESYSTEM_CACHE_DURATION'] : null,
    ];
} elseif ($_ENV['FILESYSTEM_TYPE'] === 's3') {
    $config['components']['fs'] = [
        'class' => AwsS3Filesystem::class,
        'key' => $_ENV['FILESYSTEM_S3_KEY'],
        'secret' => $_ENV['FILESYSTEM_S3_SECRET'],
        'bucket' => $_ENV['FILESYSTEM_S3_BUCKET'],
        'region' => 'us-east-1',
        'version' => 'latest',
        'endpoint' => $_ENV['FILESYSTEM_S3_ENDPOINT'],
        'pathStyleEndpoint' => ($_ENV['FILESYSTEM_S3_PATH_STYLE'] == 1) ? true : false,
        'cache' => ($_ENV['FILESYSTEM_CACHE'] == 1) ? 'cache' : null,
        'cacheKey' => ($_ENV['FILESYSTEM_CACHE'] == 1) ? 'flysystem' : null,
        'cacheDuration' => ($_ENV['FILESYSTEM_CACHE'] == 1) ? $_ENV['FILESYSTEM_CACHE_DURATION'] : null,
    ];
}
/**/

return $config;
