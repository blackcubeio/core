<?php
/**
 * RouteEncoder.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\components;

use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use blackcube\core\Module;
use yii\base\NotSupportedException;
use Yii;

/**
 * Encode CMS parameters into a string
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class RouteEncoder
{

    /**
     * @var string regex pattern used to match routes
     */
    private static $routePattern = '/^(?P<type>[^-]+)-(?P<id>[0-9]+)$/';

    /**
     * @return array list of allowed types
     */
    private static function getAllowedTypes()
    {
        return [
            Node::getElementType(),
            Composite::getElementType(),
            Category::getElementType(),
            Tag::getElementType(),
            Slug::getElementType()
        ];
    }

    /**
     * @param string $type
     * @param integer $id
     * @param boolean $relative
     * @return string
     * @since XXX
     */
    public static function encode($type, $id, $relative = false)
    {
        if (in_array($type,  static::getAllowedTypes()) === false) {
            throw new NotSupportedException(Module::t('routing', 'Type {type} is not supported.', ['type' => $type]));
        }
        return (($relative === false) ? '/'.Module::getInstance()->uniqueId.'/' : '').$type.'-'.$id;
    }

    /**
     * @param string $route
     * @return array|false ['type' => 'xxx'[, 'id' => 'YYY']]
     * @since XXX
     */
    public static function decode($route)
    {
            if (preg_match(static::$routePattern, $route, $matches) === 1) {
                $absoluteRoutePrefix = '/'.Module::getInstance()->uniqueId.'/';
                if (strncmp($absoluteRoutePrefix, $matches['type'], strlen($absoluteRoutePrefix)) === 0) {
                    $matches['type'] = str_replace($absoluteRoutePrefix, '', $matches['type']);
                }
                if (in_array($matches['type'], static::getAllowedTypes()) === false) {
                    throw new NotSupportedException(Module::t('routing', 'Type {type} is not supported.', ['type' => $matches['type']]));
                }
                return [
                    'type' => $matches['type'],
                    'id' => $matches['id'],
                ];
            } else {
                return false;
            }
    }

}
