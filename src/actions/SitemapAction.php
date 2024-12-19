<?php
/**
 * SitemapAction.php
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

use blackcube\core\models\Sitemap;
use blackcube\core\models\Slug;
use DateTime;
use DateTimeZone;
use DOMDocument;
use XMLReader;
use Yii;
use yii\web\Response;
use yii\web\ViewAction;

/**
 * Generate Sitemap
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class SitemapAction extends ViewAction
{
    /**
     * @var DOMDocument
     */
    private $dom = null;

    /**
     * @var string alias to additional sitemap file
     */
    public $additionalSitemapAlias = null;

    public $addHeaderXRobotsTag = true;

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
        $timeZone = Yii::createObject(DateTimeZone::class, [Yii::$app->timeZone]);
        $sitemapilterQuery = Sitemap::find()->active()->select('[[slugId]]');
        $slugsQuery = Slug::find()->active()->with(['sitemap', 'seo'])
            ->andWhere(['in', '[[id]]', $sitemapilterQuery])
            ->andWhere(['or',
                ['host' => $hostname],
                ['host' => null]
            ]);
        foreach ($slugsQuery->each() as $currentSlug) {
            $sitemap = $currentSlug->sitemap;
            $noIndex = false;
            if ($currentSlug->seo !== null && $currentSlug->seo->active === true) {
                $noIndex = (bool)$currentSlug->seo->noindex;
            }
            $element = $currentSlug->getElement()->active()->one();
            if ($element !== null && $noIndex === false) {
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
        if ($this->addHeaderXRobotsTag === true) {
            Yii::$app->response->headers->add('X-Robots-Tag', 'noindex');
        }

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
        if ($this->additionalSitemapAlias !== null) {
            $sitemapPath = Yii::getAlias($this->additionalSitemapAlias);
            if (file_exists($sitemapPath) && is_file($sitemapPath)) {
                try {
                    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
                        $sitemapReader = new XMLReader();
                        $sitemapReader->open($sitemapPath);
                    } else {
                        $sitemapReader = XMLReader::open($sitemapPath);
                    }

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
