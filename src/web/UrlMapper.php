<?php
/**
 * UrlMapper.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web
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
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web
 * @since XXX
 */
class UrlMapper extends BaseObject implements ArrayAccess
{
    const CACHE_PREFIX = 'blackcube:web:urlmapper';

    /**
     * @var int
     */
    public static $CACHE_EXPIRE = 3600;

    /**
     * Check if current route is handled by the mapper
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return (RouteEncoder::decode($offset) !== false);
    }

    /**
     * Retrieve controller for current route
     * 1. if requested route is in the map, return controller definition
     * 2. if requested route is defined in the cms return controller definitions and element ids
     * {@inheritDoc}
     */
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
    public function offsetSet($offset, $value)
    {
        // $this->additionalMap[$offset] = $value;
    }

    /**
     * Allow backward compatibility with classic controllerMap
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        // unset($this->additionalMap[$offset]);
    }

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
        /*/
        if (Module::getInstance()->cache !== null) {
            $cacheDependency = QueryCache::getCmsDependencies();
            $query->cache(static::$CACHE_EXPIRE, $cacheDependency);
        }
        /**/
        $element = $query->andWhere(['id' => $id])->active()->one();
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
            list($controllerRef, ) = Yii::$app->createController($element->type->route);
            if ($controllerRef === null) {
                throw new NotSupportedException();
            }
            $controllerId = $controllerRef->id;
            $moduleUid = $controllerRef->module->uniqueId;
            $controllerClass = get_class($controllerRef);
            $prefix = trim($moduleUid.'/'.$controllerId, '/');
            $finalRoutePart = trim(str_replace($prefix, '', $element->type->route), '/');
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
