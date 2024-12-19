<?php
/**
 * Element.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\components;

use blackcube\core\helpers\QueryCache;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use blackcube\core\Module;
use yii\base\InvalidArgumentException;
use yii\base\NotSupportedException;
use Yii;

/**
 * retrieve element frop route
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class Element
{

    public static function query(string $route, bool $active = true)
    {
        try {
            $decodedRoute = RouteEncoder::decode($route);
        } catch (\Exception $e) {
            return null;
        }
        if ($decodedRoute === false) {
            return null;
        }
        switch ($decodedRoute['type']) {
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
                throw new InvalidArgumentException();
                break;
        }
        $query->andWhere(['id' => $decodedRoute['id']]);
        if ($active === true) {
            $query->active();
        }
        return $query;
    }
    public static function instanciate(string $route, bool $active = true)
    {
        $query = static::query($route, $active);
        if ($query !== null) {
            $element = $query->one();
        } else {
            $element = null;
        }
        return $element;
    }
}
