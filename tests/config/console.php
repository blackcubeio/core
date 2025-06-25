<?php
/**
 * main.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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
