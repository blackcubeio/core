<?php
/**
 * Seo.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 */

namespace blackcube\core\web\helpers;

use blackcube\core\models\Slug;
use yii\web\View;
use DateTime;
use Yii;

/**
 * Seo helpers to handle Blackcube SEO fields
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 * @since XXX
 */
class Seo
{
    /**
     * Register SEO Metatags
     * @param View $view
     * @param Slug $slug
     */
    public static function register(View $view, Slug $slug)
    {
        if ($slug !== null) {
            if ($slug->seo !== null && $slug->seo->active) {
                if (empty($slug->seo->title) === false) {
                    $view->title = $slug->seo->title;
                }
                $robots = [];
                if ($slug->seo->noindex) {
                    $robots[] = 'noindex';
                }
                if ($slug->seo->nofollow) {
                    $robots[] = 'nofollow';
                }
                if (empty($robots) === false) {
                    $view->registerMetaTag(['name' => 'robots', 'content' => implode(', ', $robots)], 'meta-robots');
                }
                if (empty($slug->seo->description) === false) {
                    $view->registerMetaTag(['name' => 'description', 'content' => $slug->seo->description], 'meta-description');
                }
                $image = null;
                if ($slug->seo->image !== null && empty($slug->seo->image) === false) {
                    $images = preg_split('/[, ]+/', $slug->seo->image, PREG_SPLIT_NO_EMPTY);
                    if (is_array($images) && isset($images[0])) {
                        $image = Html::cacheImage($images[0], 1200, 630);
                        $image = Yii::$app->request->getHostInfo().$image;
                    }
                }
                $currentUrl = Yii::$app->request->absoluteUrl;
                if ($slug->seo->og) {
                    $view->registerMetaTag(['property' => 'og:type', 'content' => $slug->seo->ogType], 'meta-og:type');
                    if (empty($slug->seo->title) === false) {
                        $view->registerMetaTag(['property' => 'og:title', 'content' => $slug->seo->title], 'meta-og:title');
                    }
                    if (empty($slug->seo->description) === false) {
                        $view->registerMetaTag(['property' => 'og:description', 'content' => $slug->seo->description], 'meta-og:description');
                    }
                    if ($image !== null) {
                        $view->registerMetaTag(['property' => 'og:image', 'content' => $image], 'meta-og:image');
                    }
                    $view->registerMetaTag(['property' => 'og:url', 'content' => $currentUrl], 'meta-og:url');
                }
                if ($slug->seo->twitter) {
                    $view->registerMetaTag(['property' => 'twitter:card', 'content' => $slug->seo->twitterCard], 'meta-twitter:card');
                    if (empty($slug->seo->title) === false) {
                        $view->registerMetaTag(['property' => 'twitter:title', 'content' => $slug->seo->title], 'meta-twitter:title');
                    }
                    if (empty($slug->seo->description) === false) {
                        $view->registerMetaTag(['property' => 'twitter:description', 'content' => $slug->seo->description], 'meta-twitter:description');
                    }
                    $view->registerMetaTag(['property' => 'twitter:url', 'content' => $currentUrl], 'meta-twitter:url');
                    if ($image !== null) {
                        $view->registerMetaTag(['property' => 'twitter:image', 'content' => $image], 'meta-twitter:image');
                    }
                }
            }
        }
    }
}
