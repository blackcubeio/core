<?php
/**
 * RouteEncoder.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use yii\helpers\Inflector;
use Yii;

/**
 * Encode CMS parameters into a string
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 *
 */

class RouteEncoder
{

    private static $routes = [];

    private static $routePattern = '/^blackcube-(?P<type>[a-zA-Z]+)(-(?P<id>\d+))?$/';

    private static $routePrefix = 'blackcube-';

    /**
     * @param string $type
     * @param null|integer $id
     * @return string
     */
    public static function encode($type, $id = null)
    {
        $route = static::$routePrefix.$type;
        if ($id !== null) {
            $route .= '-'.$id;
        }
        return $route;
    }

    /**
     * @param string $route
     * @return array|false ['type' => 'xxx'[, 'id' => 'YYY']]
     */
    public static function decode($route)
    {
        if (isset(static::$routes[$route]) === false)
        {
            if (preg_match(static::$routePattern, $route, $matches) === 1) {
                static::$routes[$route] = [
                    'type' => $matches['type'],
                ];
                if (isset($matches['id']) === true) {
                    static::$routes[$route]['id'] = $matches['id'];
                }
            } else {
                static::$routes[$route] = false;
            }
        }
        return static::$routes[$route];
    }

}
