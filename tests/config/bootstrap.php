<?php
/**
 * bootstrap.php
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
use Dotenv\Dotenv;

try {
    $dotEnv = Dotenv::createImmutable(dirname(__DIR__, 2));
    // $dotEnv = new Dotenv(dirname(dirname(__DIR__)));
    $dotEnv->safeLoad();
    $dotEnv->required([
        'YII_ENV',
        'APP_ENV',
        'APP_VERSION',
        'DB_DRIVER',
        'DB_DATABASE',
        'DB_USER',
        'DB_HOST',
        'DB_PASSWORD',
        'DB_SCHEMA',
        'DB_TABLE_PREFIX',
    ]);
    $dotEnv->required('YII_COOKIE_VALIDATION_KEY')->notEmpty();
    $dotEnv->required('DB_DRIVER')->allowedValues(['mysql', 'pgsql']);
    $dotEnv->required('DB_SCHEMA_CACHE')->isBoolean();
    $dotEnv->required('DB_SCHEMA_CACHE_DURATION')->isInteger();
    $dotEnv->required('FILESYSTEM_TYPE')->allowedValues(['local', 's3']);
    $dotEnv->required('FILESYSTEM_CACHE')->isBoolean();
    $dotEnv->required('FILESYSTEM_CACHE_DURATION')->isInteger();
    // $dotEnv->required('SENTRY_DSN')->notEmpty();
    // $dotEnv->required('SENTRY_ENABLED')->isBoolean();
    // $dotEnv->required('SENTRY_CONTEXT')->isBoolean();
} catch (Exception $e) {
    die('Application not configured');
}

// get wanted debug
$debug = getenv('YII_DEBUG');
if ($debug === 'true' || $debug == 1) {
    $debug = true;
}
if ($debug === true) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// get if app is in maintenance mode
$maintenance = getenv('YII_MAINTENANCE');
if ($maintenance === 'true' || $maintenance == 1) {
    defined('YII_MAINTENANCE') or define('YII_MAINTENANCE', true);
} else {
    defined('YII_MAINTENANCE') or define('YII_MAINTENANCE', false);
}

$currentEnvironment = getenv('YII_ENV');
defined('YII_ENV') or define('YII_ENV', $currentEnvironment);
