<?php
/**
 * RobotsTxtAction.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\actions;

use blackcube\core\helpers\QueryCache;
use blackcube\core\models\Sitemap;
use blackcube\core\Module;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\ViewAction;

/**
 * Generate robots.txt
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
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

    public function init()
    {
        parent::init();
        $this->sitemapRoute = 'core/sitemap-xml';
    }

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
                ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
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
                        ->cache(Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies())
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
            Yii::$app->cache->set('robots.txt', $content, Module::getInstance()->cacheDuration, QueryCache::getCmsDependencies());
        }


        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->content = $content;
        return Yii::$app->response;
    }
}
