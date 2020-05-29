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
use blackcube\core\Module;
use yii\web\Response;
use yii\web\ViewAction;
use DateTime;
use DateTimeZone;
use DOMDocument;
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
     * @inheritdoc
     */
    public function run()
    {
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
                    $loc = $this->dom->createElement('loc', $currentHost.'/'.$sitemap->slug->path);
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
        $this->dom->appendChild($urlSet);

        Yii::$app->response->format = Response::FORMAT_XML;
        Yii::$app->response->content = $this->dom->saveXML();
        return Yii::$app->response;
    }

}
