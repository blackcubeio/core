<?php
/**
 * UrlRule.php
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

use blackcube\core\components\PreviewManager;
use blackcube\core\components\RouteEncoder;
use blackcube\core\models\Category;
use blackcube\core\models\Composite;
use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use blackcube\core\models\Tag;
use Yii;
use yii\base\BaseObject;
use yii\db\Expression;
use yii\db\Query;
use yii\web\UrlRuleInterface;

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
        $previewManager = Yii::createObject(PreviewManager::class);
        $routeToPrettyUrl = false;
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
            //TODO: handle preview (active)
            $query = null;
            switch($type) {
                case Slug::TYPE_COMPOSITE:
                    $query = Composite::find()->where(['slugId' => $id]);
                    break;
                case Slug::TYPE_NODE:
                    $query = Node::find()->where(['slugId' => $id]);
                    break;
                case Slug::TYPE_CATEGORY:
                    $query = Category::find()->where(['slugId' => $id]);
                    break;
                case Slug::TYPE_TAG:
                    $query = Tag::find()->where(['slugId' => $id]);
                    break;
            }
            if ($query !== null) {
                if ($previewManager->check() === false) {
                    $query->andWhere(['active' => true]);
                    if ($type === Slug::TYPE_COMPOSITE || $type === Slug::TYPE_NODE) {
                        $query->andWhere([['<=', 'dateStart', new Expression('NOW()')]]);
                        $query->andWhere([['>=', 'dateEnd', new Expression('NOW()')]]);
                    }
                } else {
                    $simulateDate = $previewManager->getSimulateDate();
                    if (($simulateDate !== null) && ($type === Slug::TYPE_COMPOSITE || $type === Slug::TYPE_NODE)) {
                        $query->andWhere([['<=', 'dateStart', $simulateDate]]);
                        $query->andWhere([['>=', 'dateEnd', $simulateDate]]);
                    }

                }

            }
            $slugQuery = Slug::find()->where([
                'targetId' => $id,
                'target' => $type,
            ]);
            if ($previewManager->check() === false) {
                $slugQuery->andWhere(['active' => true]);
            }
            $slug = $slugQuery->one();
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
        $previewManager = Yii::createObject(PreviewManager::class);
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

            $slug = Slug::findOneByPathinfoAndHostname($pathInfo, $hostname);
            if ($slug === null) {
                //TODO: check if finding an action is correct...
                if (strpos($pathInfo, '/') !== false) {
                    $parts = explode('/', $pathInfo);
                    $action = array_pop($parts);
                    $pathInfo = implode('/', $parts);
                    //TODO: handle preview (active)
                    $slug = Slug::findOneByPathinfoAndHostname($pathInfo, $hostname);
                }
            }
            if ($slug !== null) {
                $route = RouteEncoder::encode($slug->target, $slug->targetId);
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
        return $prettyUrlToRoute;
    }

    /**
     * @param Slug $slug
     * @return Category|Composite|Node|Tag|null
     * @throws \yii\base\InvalidConfigException
     */
    protected function searchElement(Slug $slug)
    {
        /*/
            select 'composite' type, id from composites where slugId = 1 and active = true and dateStart <= NOW() and dateEnd >= NOW()
            union select 'node' type, id from nodes where slugId = 1 and active = true and dateStart <= NOW() and dateEnd >= NOW()
            union select 'tag' type, id from tags where slugId = 1 and active = true
            union select 'category' type, id from categories where slugId = 1 and active = true;
        /**/
        $previewManager = Yii::createObject(PreviewManager::class);

        $compositeQuery = new Query();
        $compositeQuery->select(['"composite" AS type', 'id'])
            ->from(Composite::tableName())
            ->where(['slugId' => $slug->id]);
        $nodeQuery = new Query();
        $nodeQuery->select(['"node" AS type', 'id'])
            ->from(Node::tableName())
            ->where(['slugId' => $slug->id]);
        $tagQuery = new Query();
        $tagQuery->select(['"tag" AS type', 'id'])
            ->from(Tag::tableName())
            ->where(['slugId' => $slug->id]);
        $categoryQuery = new Query();
        $categoryQuery->select(['"category" AS type', 'id'])
            ->from(Category::tableName())
            ->where(['slugId' => $slug->id]);

        if ($previewManager->check() === false) {
            $compositeQuery->andWhere([
                'active' => true,
            ]);
            $compositeQuery->andWhere([['<=', 'dateStart', new Expression('NOW()')]]);
            $compositeQuery->andWhere([['>=', 'dateEnd', new Expression('NOW()')]]);
            $nodeQuery->andWhere([
                'active' => true,
            ]);
            $nodeQuery->andWhere([['<=', 'dateStart', new Expression('NOW()')]]);
            $nodeQuery->andWhere([['>=', 'dateEnd', new Expression('NOW()')]]);
            $tagQuery->andWhere([
                'active' => true,
            ]);
            $categoryQuery->andWhere([
                'active' => true,
            ]);
        } else {
            $simulateDate = $previewManager->getSimulateDate();
            if ($simulateDate !== null) {
                $compositeQuery->andWhere([['<=', 'dateStart', $simulateDate]]);
                $compositeQuery->andWhere([['>=', 'dateEnd', $simulateDate]]);
                $nodeQuery->andWhere([['<=', 'dateStart', $simulateDate]]);
                $nodeQuery->andWhere([['>=', 'dateEnd', $simulateDate]]);
            }
        }
        $compositeQuery->union($nodeQuery)
            ->union($tagQuery)
            ->union($categoryQuery);
        $result = $compositeQuery->one();
        $element = null;
        if ($result !== false && isset($result['type']) && isset($result['id'])) {
            switch ($result['type']) {
                case 'composite':
                    $element = Composite::findOne(['id' => $result['id']]);
                    break;
                case 'node':
                    $element = Node::findOne(['id' => $result['id']]);
                    break;
                case 'tag':
                    $element = Tag::findOne(['id' => $result['id']]);
                    break;
                case 'category':
                    $element = Category::findOne(['id' => $result['id']]);
                    break;
            }
        }
        return $element;

    }
}
