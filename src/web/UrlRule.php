<?php
/**
 * UrlRule.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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
use blackcube\core\Module;
use yii\base\BaseObject;
use yii\caching\CacheInterface;
use yii\web\UrlRuleInterface;
use Yii;

/**
 * This is class allow transcoding url from route to DB
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package blackcube\core\web
 *
 */
class UrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @var string  suffix used for faked url
     */
    public $suffix = null;

    private static $parsedSlugs = [];
    private static $createdSlugs = [];

    /**
     * {@inheritDoc}
     */
    public function createUrl($manager, $route, $params)
    {
        $cache = Module::getInstance()->get('cache');
        $prettyUrl = false;
        // $params = ['elementTarget' => 'tag-1']
        // $route = 'modules/controller?/action?/element-id
        $info = explode('/', $route);
        $element = array_pop($info);
        if (preg_match('/^(?P<type>tag|category|node|composite|slug)-(?P<id>[0-9]+)$/', $element, $matches) === 0) {
            return false;
        }
        $type = $matches['type'];
        $id = $matches['id'];
        if ($cache !== null) {
            $cacheId = Module::getInstance()->uniqueId.':web:urlrule:'. $route;
            if (empty($params) === false) {
                $cacheId .= '?'.http_build_query($params);
            }
            $prettyUrl = $cache->get($cacheId);
        }
        if ($prettyUrl === false) {
            $key = sha1($route);

            if (isset(static::$createdSlugs[$key]) === false) {
                static::$createdSlugs[$key] = Slug::findOneByTypeAndId($type, $id);
            }
            $slug = static::$createdSlugs[$key];
            if ($slug !== null) {
                $prettyUrl = '';
                if ($slug->host !== null && empty($slug->host) === false) {
                    $request = Yii::$app->getRequest();
                    $hostname = $request->getHostName();
                    if ($hostname !== $slug->host) {
                        $scheme = $request->getIsSecureConnection() ? 'https' : 'http';
                        $prettyUrl .= $scheme.'://'.$slug->host.'/';
                    }
                }
                $prettyUrl .= $slug->path;
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
            }
            if ($cache !== null) {
                $cacheId = Module::getInstance()->uniqueId.':web:urlrule:'. $route;
                if (empty($params) === false) {
                    $cacheId .= '?'.http_build_query($params);
                }
                /** @var CacheInterface $cache */
                $cache->set($cacheId, $prettyUrl, Module::getInstance()->cacheDuration, QueryCache::getSlugDependencies());
            }
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
        // if (empty($pathInfo) === true) {
        //     return false;
        // }
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
        $key = sha1($hostname.':'.$pathInfo);
        if (isset(static::$parsedSlugs[$key]) === false) {
            static::$parsedSlugs[$key] = Slug::findByPathinfoAndHostname($pathInfo, $hostname)
                ->active()
                ->with(['element' => function($query) { $query->active(); }])
                ->cache(Module::getInstance()->cacheDuration, QueryCache::getSlugDependencies())
                ->one();
        }
        $slug = static::$parsedSlugs[$key];

        if ($slug === null) {
            return false;
        }
        // $element = $slug->getElement()->active()->one();
        $element = $slug->element;
        if ($element !== null) {
            $elementClass = get_class($element);
            $elementId = $element->id;
        } elseif (empty($slug->targetUrl) === false) {
            $elementClass = get_class($slug);
            $elementId = $slug->id;
        } else {
            return false;
        }
        $route = RouteEncoder::encode($elementClass::getElementType(), $elementId);
        return [
            $route,
            []
        ];
    }

}
