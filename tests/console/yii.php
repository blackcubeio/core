<?php
/**
 * yii.php
 *
 * PHP Version 8.2+
 *
 * Initial console script
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package base
 */

use yii\console\Application;

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

// init autoloaders
require dirname(__DIR__, 2).'/vendor/autoload.php';

require dirname(__DIR__, 1).'/config/bootstrap.php';

require dirname(__DIR__, 2).'/vendor/yiisoft/yii2/Yii.php';

$config = require dirname(__DIR__, 1).'/config/console.php';
$exitCode = (new Application($config))->run();
exit($exitCode);
