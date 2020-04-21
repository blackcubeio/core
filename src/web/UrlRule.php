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
use blackcube\core\models\Slug;
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
     * @var string route prefix for CMS
     */
    public $routePrefix = 'blackcube';

    /**
     * @var string route separator
     */
    public $routeSeparator = '-';

    /**
     * @var string  suffix used for faked url
     */
    public $suffix;

    /**
     * {@inheritDoc}
     */
    public function createUrl($manager, $route, $params)
    {
        $routeToPrettyUrl = false;
        if (preg_match('/blackcube-([^-]+)-([0-9]+)(.*)/', $route, $matches) > 0) {
            $route = 'blackcube-'.$matches[1].'-'.$matches[2].$matches[3];
        } else {
            return false;
        }
        $action = null;
        if (strpos($route, '/') !== false) {
            $data = explode('/', $route);
            $action = array_pop($data);
            $route = implode('/', $data);
        }
        $extractedRoute = RouteEncoder::decode($route);
        //TODO: make better code
        $type = null;
        $id = null;
        if (is_array($extractedRoute) === true && isset($extractedRoute['type']) === true) {
            $type = $extractedRoute['type'];
            if (isset($extractedRoute['id']) === true) {
                $id = $extractedRoute['id'];
            }
        }
        if ($type !== null && $id !== null) {
            $slug = Slug::findOneByTypeAndId($type, $id);
            if ($slug !== null) {
                $routeToPrettyUrl = $slug->path;
                if ($action !== null) {
                    $routeToPrettyUrl .= '/' . $action;
                }
                if ((empty($params) === false ) && ($query = http_build_query($params)) !== '') {
                    $routeToPrettyUrl .= '?' . $query;
                }
            }
        }
        return $routeToPrettyUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function parseRequest($manager, $request)
    {
        $prettyUrlToRoute = false;
        $pathInfo = $request->getPathInfo();
        $hostname = $request->getHostName();
        $action = null;
        if (empty($pathInfo) === false) {
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
            if ($slug !== null) {
                $element = $slug->getElement()->active()->one();
                if ($element !== null) {
                    $elementClass = get_class($element);
                    $route = RouteEncoder::encode($elementClass::getElementType(), $element->id);
                    if ($route !== false) {
                        if ($action !== null) {
                            $route .= '/'.$action;
                        }
                        $prettyUrlToRoute = [
                            $route,
                            []
                        ];
                    }
                } elseif (empty($slug->targetUrl) === false) {
                    $elementClass = get_class($slug);
                    $route = RouteEncoder::encode($elementClass::getElementType(), $slug->id);
                    if ($route !== false) {
                        if ($action !== null) {
                            $route .= '/'.$action;
                        }
                        $prettyUrlToRoute = [
                            $route,
                            []
                        ];
                    }
                }
            }
        }
        return $prettyUrlToRoute;
    }

}
