<?php
/**
 * UrlMapper.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\web;

use blackcube\core\components\RouteEncoder;
use blackcube\core\helpers\QueryCache;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use blackcube\core\models\Type;
use blackcube\core\Module;
use blackcube\core\web\controllers\RedirectController;
use yii\base\BaseObject;
use yii\base\NotSupportedException;
use yii\caching\DbDependency;
use yii\caching\DbQueryDependency;
use yii\caching\Dependency;
use yii\db\Expression;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use ArrayAccess;
use Yii;

/**
 * This is class allow transcoding url from route to DB
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class UrlMapper extends BaseObject implements ArrayAccess
{
    const CACHE_PREFIX = 'blackcube:web:urlmapper';

    /**
     * Check if current route is handled by the mapper
     * {@inheritDoc}
     */
    public function offsetExists($offset) :bool
    {
        return (RouteEncoder::decode($offset) !== false);
    }

    /**
     * Retrieve controller for current route
     * 1. if requested route is in the map, return controller definition
     * 2. if requested route is defined in the cms return controller definitions and element ids
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $data = RouteEncoder::decode($offset);
        if ($data  === false) {
            return null;
        }
        list($controllerClass, $moduleUid, $realRoute) = static::fetchControllerForElement($data['type'], $data['id']);
        $mapper = [
            'class' => $controllerClass,
            'elementInfo' => RouteEncoder::encode($data['type'], $data['id'], true),
        ];
        if ($moduleUid !== null) {
            $mapper['moduleUid'] = $moduleUid;
        }
        if ($realRoute !== null) {
            $mapper['realRoute'] = $realRoute;
        }
        return $mapper;
    }

    /**
     * Allow backward compatibility with classic controllerMap
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value) :void
    {
        // $this->additionalMap[$offset] = $value;
    }

    /**
     * Allow backward compatibility with classic controllerMap
     * {@inheritDoc}
     */
    public function offsetUnset($offset) :void
    {
        // unset($this->additionalMap[$offset]);
    }

    private static $fetchedElements = [];
    private static $fetchedControllers = [];
    protected static function fetchControllerForElement($type, $id)
    {
        $element = null;
        switch ($type) {
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
            case Slug::getElementType():
                // Special case to handle redirect
                $query = Slug::find();
                break;
            default:
                throw new NotFoundHttpException();
                break;
        }
        $key = $type.'-'.$id;
        $key = sha1($key);
        if (isset(static::$fetchedElements[$key]) === false) {
            $element = $query
                ->andWhere(['id' => $id])
                ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
                ->active()
                ->one();
            static::$fetchedElements[$key] = $element;
        } else {
            $element = static::$fetchedElements[$key];
        }

        /* @var $element \blackcube\core\models\Node|\blackcube\core\models\Composite|\blackcube\core\models\Category|\blackcube\core\models\Tag */
        if ($element === null) {
            throw new NotFoundHttpException();
        }
        //TODO: set caching
        if ($element instanceof Slug) {
            $moduleUid = null;
            $controllerClass = RedirectController::class;
            $finalRoutePart = null;
        } elseif ($element->type !== null && empty($element->type->route) === false) {
            $key = sha1($element->type->route);
            if (isset(static::$fetchedControllers[$key]) === false) {
                list($controllerRef, ) = Yii::$app->createController($element->type->route);
                if ($controllerRef === null) {
                    throw new NotSupportedException();
                }
                $controllerId = $controllerRef->id;
                $moduleUid = $controllerRef->module->uniqueId;
                $controllerClass = get_class($controllerRef);
                $prefix = trim($moduleUid.'/'.$controllerId, '/');
                $finalRoutePart = trim(str_replace($prefix, '', $element->type->route), '/');

                static::$fetchedControllers[$key] = [$controllerClass, $moduleUid, $finalRoutePart];
            } else {
                list($controllerClass, $moduleUid, $finalRoutePart) = static::$fetchedControllers[$key];
            }

        } elseif ($element->type === null) {
            throw new BadRequestHttpException(Module::t('web', 'Element "{type}-{id}" is not routable', [
                'type' => $type,
                'id' => $id,
            ]));
        } else {
            throw new NotSupportedException();
        }
        return [$controllerClass, $moduleUid, $finalRoutePart];
    }
}
