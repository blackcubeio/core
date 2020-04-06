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
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Tag;
use yii\base\BaseObject;
use ArrayAccess;
use yii\web\NotFoundHttpException;
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

    public $defaultController = 'Blackcube';

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
            list ($controller, $action) = static::fetchControllerForElement($data);
            if (empty($controller) === true) {
                $controller = $this->defaultController;
            }
            if ($this->controllerNamespace !== null) {
                $class = $this->controllerNamespace . '\\' . $controller.'Controller';
            } else {
                $class = $controller.'Controller';
            }
            $mappedController = [
                'class' => $class,
                'elementType' => $data['type'],
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

    protected static function fetchControllerForElement($data)
    {
        $element = null;
        switch ($data['type']) {
            case Node::getElementType():
                $query = Node::find();
                break;
            case Composite::getElementType():
                $query = Composite::find();
                break;
            case Category::getElementType():
                $query = Category::find();
                break;
            case Tag::getElementType():
                $query = Tag::find();
                break;
            default:
                throw new NotFoundHttpException();
                break;
        }
        $element = $query->where(['id' => $data['id']])->active()->one();
        /* @var $element \blackcube\core\models\Node|\blackcube\core\models\Composite|\blackcube\core\models\Category|\blackcube\core\models\Tag */
        if ($element === null) {
            throw new NotFoundHttpException();
        }
        return [$element->getController(), $element->getAction()];
    }
}
