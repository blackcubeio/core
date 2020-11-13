<?php
/**
 * main.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2018 Ibitux
 * @license http://www.ibitux.com/license license
 * @version XXX
 * @link http://www.ibitux.com
 * @package webapp\config
 */

use yii\web\AssetManager;
use yii\web\JsonParser;

$config = require 'common.php';

// $config['basePath'] = dirname(dirname(__DIR__)).'/webapp';
$config['id'] = 'blackcube/cms';
$config['name'] = 'Blackcube.io CMS test application';

// $config['controllerNamespace'] = 'webapp\controllers';

$config['components']['request'] = [
    'cookieValidationKey' => $_ENV['YII_COOKIE_VALIDATION_KEY'],
    'parsers' => [
        'application/json' => JsonParser::class,
    ],
];

/*/
$config['components']['session'] = [
    'class' => 'yii\web\DbSession',
];
/**/

$config['components']['assetManager'] = [
    'class' => AssetManager::class,
    'linkAssets' => true,
];
$config['components']['urlManager'] = [
    'enablePrettyUrl' => false,
    'showScriptName' => false,
    'rules' => [
        [
            'pattern' => '',
            'route' => 'site/index'
        ],
        [
            'pattern' => 'maintenance',
            'route' => 'technical/maintenance'
        ],
    ],
];

// $config['params'] = [];

return $config;
