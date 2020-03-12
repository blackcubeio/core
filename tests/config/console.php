<?php
/**
 * main.php
 *
 * PHP version 7.1+
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2018 Ibitux
 * @license https://www.ibitux.com/license license
 * @version XXX
 * @link https://www.ibitux.com
 * @package console\config
 */

use yii\console\controllers\MigrateController;
$config = require 'common.php';

$config['basePath'] = dirname(__DIR__);
$config['id'] = 'blackcubeio/cms-console';
$config['name'] = 'Blackcube.io CMS console application';

$config['controllerMap'] = [
    'migrate' => [
        'class' => MigrateController::class,
        'migrationNamespaces' => [
            'blackcube\core\migrations'
        ],
    ],
];
// $config['params'] = [];

return $config;
