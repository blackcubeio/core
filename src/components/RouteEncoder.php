<?php
/**
 * RouteEncoder.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 */

namespace blackcube\core\components;

use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use Yii;

/**
 * Encode CMS parameters into a string
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\components
 * @since XXX
 */
class RouteEncoder
{

    /**
     * @var array routes
     */
    private static $routes = [];

    /**
     * @var string regex pattern used to match routes
     */
    private static $routePattern = '/^blackcube-(?P<type>[^-]+)-(?P<id>[0-9]+)(?P<action>.*)$/';

    /**
     * @var string route prefix
     */
    private static $routePrefix = '/blackcube-';

    /**
     * @param string $type
     * @param null|integer $id
     * @return string
     * @since XXX
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
     * @since XXX
     */
    public static function decode($route)
    {
        if (isset(static::$routes[$route]) === false)
        {
            if (preg_match(static::$routePattern, $route, $matches) === 1) {
                if (in_array($matches['type'], [Node::getElementType(), Category::getElementType(), Composite::getElementType(), Tag::getElementType(), Slug::getElementType()]) === false) {
                    static::$routes[$route] = false;
                } else {
                    static::$routes[$route] = [
                        'type' => $matches['type'],
                    ];
                    if (isset($matches['id']) === true) {
                        static::$routes[$route]['id'] = $matches['id'];
                    }
                    if (isset($matches['action']) === true) {
                        $action = ltrim($matches['action'], '/');
                        $action = (empty($action) === false) ? $action : null;
                        static::$routes[$route]['action'] = $action;
                    }
                }
            } else {
                static::$routes[$route] = false;
            }
        }
        return static::$routes[$route];
    }

}
