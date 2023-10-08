<?php
/**
 * RobotsTxtAction.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package webapp\actions
 */

namespace blackcube\core\web\actions;

use blackcube\core\helpers\QueryCache;
use blackcube\core\models\Sitemap;
use blackcube\core\models\Slug;
use yii\web\Response;
use yii\web\ViewAction;
use yii\helpers\Url;
use DateTime;
use DateTimeZone;
use Yii;

/**
 * Generate robots.txt
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package webapp\actions
 * @since XXX
 */
class RobotsTxtAction extends ViewAction
{

    /**
     * @var string route to sitemap
     */
    public $sitemapRoute = null;

    /**
     * @var string sitemap url will be overriden by sitemapRoute if sitemapRoute is defined
     */
    public $sitemapUrl = null;

    /**
     * @var string user agent
     */
    public $userAgent = '*';

    /**
     * @var array disallowed paths
     */
    public $disallowed = null;

    /**
     * @var array allowed paths
     */
    public $allowed = null;

    /**
     * @var array additional lines
     */
    public $additionalLines = [];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $content = Yii::$app->cache->get('robots.txt');
        if ($content === false) {
            $hostname = Yii::$app->request->getHostName();
            $robotsTxtContent = [];
            if ($this->sitemapRoute !== null) {
                $this->sitemapUrl = Url::to([$this->sitemapRoute], true);
            }
            if ($this->sitemapUrl !== null) {
                $robotsTxtContent[] = 'Sitemap: '.$this->sitemapUrl;
                $robotsTxtContent[] = '';
            }
            $robotsTxtContent[] = 'User-agent: '.$this->userAgent;
            if ($this->disallowed !== null && count($this->disallowed) > 0) {
                foreach($this->disallowed as $disallowed) {
                    $robotsTxtContent[] = 'Disallow: '.$disallowed;
                }
            }
            if ($this->allowed !== null && count($this->allowed) > 0) {
                foreach($this->allowed as $allowed) {
                    $robotsTxtContent[] = 'Allow: '.$allowed;
                }
            }
            $sitemaps = Sitemap::find()
                ->cache(3600, QueryCache::getCmsDependencies())
                ->active()
                ->with(['slug', 'slug.seo']);

            foreach($sitemaps->each() as $sitemap) {
                $currentSlug = $sitemap->slug;
                if ($currentSlug !== null && $currentSlug->active) {
                    $noIndex = false;
                    if ($currentSlug->seo !== null && $currentSlug->seo->active === true) {
                        $noIndex = (bool)$currentSlug->seo->noindex;
                    }
                    $element = $currentSlug->getElement()
                        ->cache(3600, QueryCache::getCmsDependencies())
                        ->active()
                        ->one();
                    if ($element !== null && $noIndex === true) {
                        $robotsTxtContent[] = 'Disallow: '.Url::to([$element->getRoute()]);
                    }
                }
            }
            foreach ($this->additionalLines as $additionalLine) {
                $robotsTxtContent[] = $additionalLine;
            }
            $content = implode("\n", $robotsTxtContent);
            Yii::$app->cache->set('robots.txt', $content, 3600, QueryCache::getCmsDependencies());
        }


        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->content = $content;
        return Yii::$app->response;
    }
}
