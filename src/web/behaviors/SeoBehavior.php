<?php
/**
 * SeoBehavior.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\behaviors
 */
namespace blackcube\core\web\behaviors;

use blackcube\core\helpers\QueryCache;
use blackcube\core\models\Bloc;
use blackcube\core\models\Seo;
use blackcube\core\models\Slug;
use blackcube\core\Module;
use blackcube\core\web\controllers\BlackcubeController;
use blackcube\core\web\controllers\BlackcubeControllerEvent;
use blackcube\core\web\helpers\Html;
use yii\base\Behavior;
use yii\base\ErrorException;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use Yii;
use yii\web\Application;
use yii\web\View;

/**
 * Set seo data in front page
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\behaviors
 * @since XXX
 */
class SeoBehavior extends Behavior
{
    public function events() :array
    {
        return [
            BlackcubeController::EVENT_AFTER_ELEMENT => 'registerSeo',
        ];
    }

    /**
     * Register analytics elements
     * @param BlackcubeControllerEvent $event
     * @throws \yii\base\InvalidConfigException
     */
    public function registerSeo(BlackcubeControllerEvent $event) :void
    {
        $slugId = $event->element->slugId;
        $seo = Seo::find()->active()
            ->with('slug', 'canonicalSlug')
            ->andWhere(['slugId' => $slugId])
            ->one();
        if ($seo !== null) {
            /* @var \blackcube\core\models\Seo $seo */
            $metaRobots = [];
            if ($seo->noindex) {
                $metaRobots[] = 'noindex';
            }
            if ($seo->nofollow) {
                $metaRobots[] = 'nofollow';
            }
            if ($seo->canonicalSlug !== null) {
                $event->controller->view->registerLinkTag([
                    'rel' => 'canonical',
                    'href' => Yii::$app->request->hostInfo . '/' . ltrim($seo->canonicalSlug->path, '/')
                ], 'canonical');
            }
            if (count($metaRobots) > 0) {
                $event->controller->view->registerMetaTag([
                    'name' => 'robots',
                    'content' => implode(',', $metaRobots)
                ], 'robots');
            }

            if (empty($seo->title) === false) {
                $event->controller->view->registerMetaTag([
                    'name' => 'title',
                    'content' => $seo->title
                ], 'title');
            }
            if (empty($seo->description) === false) {
                $event->controller->view->registerMetaTag([
                    'name' => 'description',
                    'content' => $seo->description
                ], 'description');
            }
            if ($seo->og) {
                $event->controller->view->registerMetaTag([
                    'property' => 'og:type',
                    'content' => $seo->ogType
                ], 'og:type');
                if (empty($seo->title) === false) {
                    $event->controller->view->registerMetaTag([
                        'property' => 'og:title',
                        'content' => $seo->title
                    ], 'og:title');
                }
                if (empty($seo->image) === false) {
                    $event->controller->view->registerMetaTag([
                        'property' => 'og:image',
                        'content' => Yii::$app->request->hostInfo. '/' . ltrim(Html::cacheImage($seo->image),'/')
                    ], 'og:image');
                }
                $event->controller->view->registerMetaTag([
                    'property' => 'og:url',
                    'content' => Yii::$app->request->absoluteUrl
                ], 'og:url');
                if (empty($seo->description) === false) {
                    $event->controller->view->registerMetaTag([
                        'property' => 'og:description',
                        'content' => $seo->description
                    ], 'og:description');
                }
            }
            if ($seo->twitter) {
                $event->controller->view->registerMetaTag([
                    'name' => 'twitter:card',
                    'content' => $seo->twitterCard
                ], 'twitter:card');
                if (empty($seo->image) === false) {
                    $event->controller->view->registerMetaTag([
                        'name' => 'twitter:image',
                        'content' => Yii::$app->request->hostInfo. '/' . ltrim(Html::cacheImage($seo->image), '/')
                    ], 'twitter:image');
                }
                if (empty($seo->title) === false) {
                    $event->controller->view->registerMetaTag([
                        'name' => 'twitter:title',
                        'content' => $seo->title
                    ], 'twitter:title');
                }
                if (empty($seo->description) === false) {
                    $event->controller->view->registerMetaTag([
                        'name' => 'twitter:description',
                        'content' => $seo->description
                    ], 'twitter:description');
                }

            }
        }


    }
}
