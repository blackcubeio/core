<?php
/**
 * SeoCest.php
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
namespace tests\seo;

use blackcube\core\models\Seo;
use blackcube\core\models\Slug;
use tests\SeoTester;
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
class SeoCest extends SeoBase
{

    public function testInsert(SeoTester $I)
    {
        $seo = new Seo();
        $seo->title = 'SEO Title';
        $seo->description = 'SEO Description';
        $seo->noindex = false;
        $I->assertFalse($seo->validate());
        $slug = Slug::find()->where(['id' => 20])->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $seo->slugId = $slug->id;
        $I->assertTrue($seo->validate());
        $I->assertTrue($seo->save());
        $savedSeo = Seo::findOne(['id' => $seo->id]);
        $I->assertInstanceOf(Seo::class, $savedSeo);
        $I->assertEquals($seo->title, $savedSeo->title);
        $I->assertEquals($seo->description, $savedSeo->description);
        $I->assertEquals($seo->noindex, $savedSeo->noindex);
        $I->assertFalse($savedSeo->nofollow);

        $erroneousSeo = new Seo();
        $erroneousSeo->title = 'SEO Title';
        $erroneousSeo->description = 'SEO Description';
        $erroneousSeo->noindex = false;
        $erroneousSeo->slugId = $slug->id;
        $I->assertFalse($erroneousSeo->validate());
        $I->assertFalse($erroneousSeo->save());
    }

    public function testFind(SeoTester $I)
    {
        $seo = new Seo();
        $seo->title = 'SEO Title';
        $seo->description = 'SEO Description';
        $seo->noindex = false;
        $slug = Slug::find()->where(['id' => 20])->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $seo->slugId = $slug->id;
        $I->assertTrue($seo->save());

        $realSeo = Seo::findOne(['id' => $seo->id]);
        $I->assertInstanceOf(Seo::class, $realSeo);

        $voidSeo = Seo::findOne(['id' => 999]);
        $I->assertNull($voidSeo);
    }

    public function testDelete(SeoTester $I)
    {
        $seo = new Seo();
        $seo->title = 'SEO Title';
        $seo->description = 'SEO Description';
        $seo->noindex = false;
        $seo->slugId = 20;
        $I->assertTrue($seo->save());

        $insertedId = $seo->id;
        $dbSeo = Seo::findOne(['id' => $insertedId]);
        $I->assertInstanceOf(Seo::class, $dbSeo);
        $dbSeo->delete();

        $voidSeo = Seo::findOne(['id' => $insertedId]);
        $I->assertNull($voidSeo);
    }

    public function testSearch(SeoTester $I)
    {
        $seos = Seo::find()->all();
        $I->assertCount(count($this->seoList), $seos);

        foreach($this->seoList as $id => $config) {
            $seo = Seo::find()->where(['id' => $id])->one();
            $I->assertInstanceOf(Seo::class, $seo);
            $I->assertEquals($config['slugId'], $seo->slugId);
            $I->assertEquals($config['title'], $seo->title);
            $I->assertEquals($config['description'], $seo->description);
            $I->assertEquals($config['active'], $seo->active);
        }

        $seos = Seo::find()->where(['slugId' => 3])->all();
        $I->assertCount(1, $seos);
        $seos = Seo::find()->where(['slugId' => 999])->all();
        $I->assertCount(0, $seos);
    }

}
