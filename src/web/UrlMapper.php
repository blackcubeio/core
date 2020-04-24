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
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use blackcube\core\models\Type;
use blackcube\core\Module;
use blackcube\core\web\controllers\RedirectController;
use yii\base\BaseObject;
use yii\caching\DbDependency;
use yii\caching\DbQueryDependency;
use yii\caching\Dependency;
use yii\db\Expression;
use yii\db\Query;
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
     * @var string
     */
    public $routePrefix = 'blackcube';

    /**
     * @var string
     */
    public $defaultController = 'Blackcube';

    /**
     * @var string
     */
    public $routeSeparator = '-';

    /**
     * @var string
     */
    public $controllerNamespace;

    /**
     * @var array 
     */
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
            if ($data['type'] === Slug::getElementType()) {
                $mappedController = [
                    'class' => $controller,
                    'slugId' => $data['id'],
                ];
            } else {
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
            }
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
            case Slug::getElementType():
                // Special case to handle redirect
                return [RedirectController::class, null];
                break;
            default:
                throw new NotFoundHttpException();
                break;
        }
        if (Module::getInstance()->cache !== null) {
            $cacheQuery = Yii::createObject(Query::class);
            $maxQueryResult = Node::find()->select('[[dateUpdate]] as date')
                ->union(Composite::find()->select('[[dateUpdate]] as date'))
                ->union(Category::find()->select('[[dateUpdate]] as date'))
                ->union(Tag::find()->select('[[dateUpdate]] as date'))
                ->union(Slug::find()->select('[[dateUpdate]] as date'))
                ->union(Type::find()->select('[[dateUpdate]] as date'));
            $expression = Yii::createObject(Expression::class, ['MAX(date)']);
            $cacheQuery->select($expression)->from($maxQueryResult);
            $cacheDependency = Yii::createObject([
                'class' => DbQueryDependency::class,
                'db' => Module::getInstance()->db,
                'query' => $cacheQuery,
                'reusable' => true,
            ]);
            $query->cache(static::$CACHE_EXPIRE, $cacheDependency);
        }
        $element = $query->andWhere(['id' => $data['id']])->active()->one();
        /* @var $element \blackcube\core\models\Node|\blackcube\core\models\Composite|\blackcube\core\models\Category|\blackcube\core\models\Tag */
        if ($element === null) {
            throw new NotFoundHttpException();
        }
        return [$element->getController(), $element->getAction()];
    }
}
