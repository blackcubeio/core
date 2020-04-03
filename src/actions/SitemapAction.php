<?php
/**
 * SitemapAction.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2017 Ibitux
 * @license http://www.ibitux.com/license license
 * @version 1.3.1
 * @link http://www.ibitux.com
 * @package application\actions
 */

namespace blackcube\core\actions;

use blackcube\core\models\Seo;
use blackcube\core\models\Sitemap;
use blackcube\core\models\Slug;
use blackcube\core\Module;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ViewAction;
use Yii;
use DOMDocument;
use DateTime;

/**
 * generate Sitemap
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2017 Ibitux
 * @license http://www.ibitux.com/license license
 * @version 1.3.1
 * @link http://www.ibitux.com
 * @package application\actions
 * @since 1.3.0
 */
class SitemapAction extends ViewAction
{
    private $dom;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $hostname = Yii::$app->request->getHostName();
        $protocol = Yii::$app->request->isSecureConnection ? 'https':'http';
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $urlSet = $this->dom->createElement('urlset');
        $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $sitemaps = Sitemap::find()->active()->with('slug');
        foreach($sitemaps->each() as $sitemap) {
            /* @var $sitemap \blackcube\core\models\Sitemap */
            $currentSlug = Slug::findOneByPathinfoAndHostname($sitemap->slug->path, $hostname);
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
                    $datetime = new DateTime($element->dateUpdate);
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
