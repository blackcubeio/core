<?php
/**
 * UrlMapper.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web
 */

namespace blackcube\core\web;

use blackcube\core\components\RouteEncoder;
use yii\base\BaseObject;
use ArrayAccess;
use Yii;

/**
 * This is class allow transcoding url from route to DB
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package app\models
 *
 */
class UrlMapper extends BaseObject implements ArrayAccess
{
    const CACHE_PREFIX = 'blackcube:web:urlmapper';

    public static $CACHE_EXPIRE = 3600;

    public $routePrefix = 'blackcube';

    public $routeSeparator = '-';

    public $controllerNamespace;
    public $additionalMap = [];

    /**
     * Check if current route is handled by the mapper
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return (isset($this->additionalMap[$offset]) === true) || (RouteEncoder::decode($offset) !== false);
    }

    /**
     * Retrieve controller for current route
     * 1. if requested route is in the map, return controller definition
     * 2. if requested route is defined in the cms return controller definitions and element ids
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        $mappedController = null;
        if (isset($this->additionalMap[$offset]) === true) {
            $mappedController = $this->additionalMap[$offset];
        } elseif (($data = RouteEncoder::decode($offset)) !== false) {
            // $data = ['type' => 'elementType', 'id' => 1234]
            list ($controller, $action) = static::findController($data);
            if ($this->controllerNamespace !== null) {
                $class = $this->controllerNamespace . '\\' . $controller . 'Controller';
            } else {
                $class = $controller . 'Controller';
            }
            $mappedController = [
                'class' => $class,
                'elementId' => $data['id'],
            ];
            if (empty($action) === false) {
                $mappedController['defaultAction'] = $action;
            }
        }
        return $mappedController;
    }

    /**
     * Allow backward compatibility with classic controllerMap
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->additionalMap[$offset] = $value;
    }

    /**
     * Allow backward compatibility with classic controllerMap
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->additionalMap[$offset]);
    }

    protected static function findController($data)
    {
        $controller = null;
        $action = null;
        if ($data !== false) {
            list($controller, $action) = static::fetchControllerForElement($data);
        }
    }

    protected static function fetchControllerForElement($data)
    {
        //TODO: fetch in DB table types the controller associated to selected element
        // if (isset($data['type']))
    }
}