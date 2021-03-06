<?php
/**
 * SitemapAction.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\actions
 */

namespace blackcube\core\web\actions;

use blackcube\core\models\Sitemap;
use blackcube\core\models\Slug;
use yii\web\Response;
use yii\web\ViewAction;
use DateTime;
use DateTimeZone;
use DOMDocument;
use XMLReader;
use Yii;

/**
 * Generate Sitemap
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\actions
 * @since XXX
 */
class SitemapAction extends ViewAction
{
    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     * @var string alias to additional sitemap file
     */
    public $additionalSitemap;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $additionalData = $this->extractAdditionalSitemap();
        $hostname = Yii::$app->request->getHostName();
        $protocol = Yii::$app->request->isSecureConnection ? 'https':'http';
        $this->dom = Yii::createObject(DOMDocument::class, ['1.0', 'UTF-8']);
        $urlSet = $this->dom->createElement('urlset');
        $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $sitemaps = Sitemap::find()->active()->with('slug');
        $timeZone = Yii::createObject(DateTimeZone::class, [Yii::$app->timeZone]);
        foreach($sitemaps->each() as $sitemap) {
            /* @var $sitemap \blackcube\core\models\Sitemap */
            $currentSlug = Slug::findByPathinfoAndHostname($sitemap->slug->path, $hostname)->active()->one();
            if ($currentSlug !== null && $currentSlug->active) {
                $element = $currentSlug->getElement()->active()->one();
                if ($element !== null) {
                    $url = $this->dom->createElement('url');
                    $currentHost = $sitemap->slug->host;
                    if ($currentHost === null) {
                        $currentHost = $protocol.'://'.$hostname;
                    } else {
                        $currentHost = $protocol.'://'.$currentHost;
                    }
                    $locString = $currentHost.'/'.$sitemap->slug->path;
                    if (isset($additionalData[$locString])) {
                        unset($additionalData[$locString]);
                    }
                    $loc = $this->dom->createElement('loc', $locString);
                    $url->appendChild($loc);
                    $datetime = Yii::createObject(DateTime::class, [$element->dateUpdate, $timeZone]);
                    $lastMod = $this->dom->createElement('lastmod', $datetime->format('c'));
                    $url->appendChild($lastMod);
                    $changeFreq = $this->dom->createElement('changefreq', $sitemap->frequency);
                    $url->appendChild($changeFreq);
                    $priority = $this->dom->createElement('priority', $sitemap->priority);
                    $url->appendChild($priority);
                    $urlSet->appendChild($url);
                }
            }
        }
        foreach($additionalData as $urlData) {
            if (isset($urlData['loc']) && empty($urlData['loc']) === false) {
                $url = $this->dom->createElement('url');
                $loc = $this->dom->createElement('loc', $urlData['loc']);
                $url->appendChild($loc);
                if (isset($urlData['lastmod']) && empty($urlData['lastmod']) === false) {
                    $lastMod = $this->dom->createElement('lastmod', $urlData['lastmod']);
                    $url->appendChild($lastMod);
                }
                if (isset($urlData['changefreq']) && empty($urlData['changefreq']) === false) {
                    $changeFreq = $this->dom->createElement('changefreq', $urlData['changefreq']);
                    $url->appendChild($changeFreq);
                }
                if (isset($urlData['priority']) && empty($urlData['priority']) === false) {
                    $priority = $this->dom->createElement('priority', $urlData['priority']);
                    $url->appendChild($priority);
                }
                $urlSet->appendChild($url);
            }
        }
        $this->dom->appendChild($urlSet);

        Yii::$app->response->format = Response::FORMAT_XML;
        Yii::$app->response->content = $this->dom->saveXML();
        return Yii::$app->response;
    }

    /**
     * Extract data from static sitemap.xml to merge
     * @return array
     */
    private function extractAdditionalSitemap()
    {
        $sitemapData = [];
        if ($this->additionalSitemap !== null) {
            $sitemapPath = Yii::getAlias($this->additionalSitemap);
            if (file_exists($sitemapPath) && is_file($sitemapPath)) {
                try {
                    $sitemapReader = XMLReader::open($sitemapPath);
                    /* @var $sitemapReader XMLReader */
                    $url = [];
                    while($sitemapReader->read()) {
                        if ($sitemapReader->name === 'urlset' && $sitemapReader->nodeType === XMLReader::END_ELEMENT) {
                            break;
                        }
                        if ($sitemapReader->name === 'url' && $sitemapReader->nodeType === XMLReader::ELEMENT) {
                            $url = [];
                        }
                        if ($sitemapReader->name === 'url' && $sitemapReader->nodeType === XMLReader::END_ELEMENT) {
                            if (isset($url['loc'])) {
                                $sitemapData[$url['loc']] = $url;
                            }
                        }
                        if ($sitemapReader->name === 'loc' && $sitemapReader->nodeType === XMLReader::ELEMENT) {
                            $url['loc'] = $sitemapReader->readString();
                        }
                        if ($sitemapReader->name === 'lastmod' && $sitemapReader->nodeType === XMLReader::ELEMENT) {
                            $url['lastmod'] = $sitemapReader->readString();
                        }
                        if ($sitemapReader->name === 'changefreq' && $sitemapReader->nodeType === XMLReader::ELEMENT) {
                            $url['changefreq'] = $sitemapReader->readString();
                        }
                        if ($sitemapReader->name === 'priority' && $sitemapReader->nodeType === XMLReader::ELEMENT) {
                            $url['priority'] = $sitemapReader->readString();
                        }
                    }
                } catch (\Exception $e) {
                    Yii::warning($e->getMessage());
                }
            }
        }
        return $sitemapData;
    }
}
