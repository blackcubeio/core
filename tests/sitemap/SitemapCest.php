<?php
/**
 * SitemapCest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 */
namespace tests\sitemap;

use blackcube\core\models\Sitemap;
use blackcube\core\models\Slug;
use tests\SitemapTester;
use yii\base\Model;
use yii\base\UnknownPropertyException;

/**
 * Test sitemap
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class SitemapCest extends SitemapBase
{

    public function testInsert(SitemapTester $I)
    {
        $sitemap = new Sitemap();
        $sitemap->frequency = 'daily';
        $sitemap->priority = 0.2;
        $sitemap->active = true;
        $I->assertFalse($sitemap->validate());
        $sitemap->slugId = 6;
        $I->assertTrue($sitemap->validate());
        $I->assertTrue($sitemap->save());
        $savedSitemap = Sitemap::findOne(['id' => $sitemap->id]);
        $I->assertInstanceOf(Sitemap::class, $savedSitemap);
        $I->assertEquals($sitemap->frequency, $savedSitemap->frequency);
        $I->assertEquals($sitemap->priority, $savedSitemap->priority);
        $I->assertEquals($sitemap->active, $savedSitemap->active);

        $erroneousSitemap = new Sitemap();
        $erroneousSitemap->frequency = 'daily';
        $erroneousSitemap->priority = 0.2;
        $erroneousSitemap->slugId = 6;
        $I->assertFalse($erroneousSitemap->validate());
        $I->assertFalse($erroneousSitemap->save());
    }

    public function testFind(SitemapTester $I)
    {
        $sitemap = new Sitemap();
        $sitemap->frequency = 'daily';
        $sitemap->priority = 0.2;
        $sitemap->active = true;
        $sitemap->slugId = 6;
        $I->assertTrue($sitemap->save());

        $realSitemap = Sitemap::findOne(['id' => $sitemap->id]);
        $I->assertInstanceOf(Sitemap::class, $realSitemap);

        $voidSitemap = Sitemap::findOne(['id' => 999]);
        $I->assertNull($voidSitemap);
    }

    public function testDelete(SitemapTester $I)
    {
        $sitemap = new Sitemap();
        $sitemap->frequency = 'daily';
        $sitemap->priority = 0.2;
        $sitemap->active = true;
        $sitemap->slugId = 6;
        $I->assertTrue($sitemap->save());

        $insertedId = $sitemap->id;
        $dbSitemap = Sitemap::findOne(['id' => $insertedId]);
        $I->assertInstanceOf(Sitemap::class, $dbSitemap);
        $dbSitemap->delete();

        $voidSitemap = Sitemap::findOne(['id' => $insertedId]);
        $I->assertNull($voidSitemap);
    }

    public function testSearch(SitemapTester $I)
    {
        $sitemaps = Sitemap::find()->all();
        $I->assertCount(count($this->sitemapList), $sitemaps);

        foreach($this->sitemapList as $id => $config) {
            $sitemap = Sitemap::find()->where(['id' => $id])->one();
            $I->assertInstanceOf(Sitemap::class, $sitemap);
            $I->assertEquals($config['frequency'], $sitemap->frequency);
            $I->assertEquals($config['priority'], $sitemap->priority);
            $I->assertEquals($config['active'], $sitemap->active);
            $I->assertEquals($config['slugId'], $sitemap->slugId);
        }

        $sitemaps = Sitemap::find()->where(['slugId' => 3])->all();
        $I->assertCount(1, $sitemaps);
        $sitemaps = Sitemap::find()->where(['slugId' => 999])->all();
        $I->assertCount(0, $sitemaps);
    }

}
