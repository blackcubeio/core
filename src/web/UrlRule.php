<?php
/**
 * UrlRule.php
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
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;
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
 *
 */
class UrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @var string  suffix used for faked url
     */
    public $suffix;

    /**
     * {@inheritDoc}
     */
    public function createUrl($manager, $route, $params)
    {
        $type = null;
        $id = null;
        $action = null;
        $decodedRoute = RouteEncoder::decode($route);
        if ($decodedRoute === false) {
            return false;
        }
        if ($type === null || $id === null) {
            return false;
        }
        $slug = Slug::findOneByTypeAndId($type, $id);
        if ($slug === null) {
            return false;
        }
        $prettyUrl = $slug->path;
        if ($action !== null) {
            $prettyUrl .= '/' . $action;
        }
        if ($this->suffix === null) {
            $this->suffix = $manager->suffix;
        }
        $suffix = (string) $this->suffix;
        if (empty($suffix) === false) {
            $prettyUrl .= $suffix;
        }
        if ((empty($params) === false ) && ($query = http_build_query($params)) !== '') {
            $prettyUrl .= '?' . $query;
        }
        return $prettyUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        $hostname = $request->getHostName();
        $action = null;
        if (empty($pathInfo) === true) {
            return false;
        }
        if ($this->suffix === null) {
            $this->suffix = $manager->suffix;
        }
        $suffix = (string) $this->suffix;
        if (empty($suffix) === false) {
            $suffixLength = strlen($suffix);
            if (substr($pathInfo, - $suffixLength) === $suffix) {
                $pathInfo = substr($pathInfo, 0, - $suffixLength);
            }
        }

        $slug = Slug::findByPathinfoAndHostname($pathInfo, $hostname)->active()->one();
        if ($slug === null) {
            return false;
        }
        /*/
        //TODO: check if overriding an action in the URL is correct... I'm not sure
        if ($slug === null) {
            if (strpos($pathInfo, '/') !== false) {
                $parts = explode('/', $pathInfo);
                $action = array_pop($parts);
                $pathInfo = implode('/', $parts);
                //TODO: handle preview (active)
                $slug = Slug::findByPathinfoAndHostname($pathInfo, $hostname)->active()->one();
            }
        }
        /**/
        $element = $slug->getElement()->active()->one();
        if ($element !== null) {
            $elementClass = get_class($element);
            $route = RouteEncoder::encode($elementClass::getElementType(), $element->id);
            if ($action !== null) {
                $route .= '/'.$action;
            }
            $prettyUrl = [
                $route,
                []
            ];
        } elseif (empty($slug->targetUrl) === false) {
            $elementClass = get_class($slug);
            $route = RouteEncoder::encode($elementClass::getElementType(), $slug->id);
            if ($action !== null) {
                $route .= '/'.$action;
            }
            $prettyUrl = [
                $route,
                []
            ];
        } else {
            return false;
        }
        return $prettyUrl;
    }

}
