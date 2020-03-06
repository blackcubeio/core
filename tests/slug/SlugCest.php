<?php
/**
 * SlugCest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 */
namespace tests\slug;

use blackcube\core\models\Slug;
use tests\SlugTester;

/**
 * Test slug
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
 */
class SlugCest extends SlugBase
{

    public function testInsert(SlugTester $I)
    {
        $slug = new Slug();
        $slug->path = '/test-slug.html';
        $slug->active = true;
        $I->assertTrue($slug->validate());
        $status = $slug->validate();
        $I->assertTrue($slug->save());
        $savedSlug = Slug::findOne(['id' => $slug->id]);
        $I->assertInstanceOf(Slug::class, $savedSlug);
        $I->assertEquals($slug->host, $savedSlug->host);
        $I->assertEquals($slug->path, $savedSlug->path);
        $I->assertEquals($slug->targetUrl, $savedSlug->targetUrl);
        $I->assertEquals($slug->httpCode, $savedSlug->httpCode);
        $I->assertEquals($slug->active, $savedSlug->active);

        // $erroneousSlug = new Slug();
        // $I->assertFalse($erroneousSlug->validate());
        // $I->assertFalse($erroneousSitemap->save());
    }

    public function testFind(SlugTester $I)
    {
        $slug = new Slug();
        $slug->path = '/test-slug.html';
        $slug->active = true;
        $I->assertTrue($slug->save());

        $realSlug = Slug::findOne(['id' => $slug->id]);
        $I->assertInstanceOf(Slug::class, $realSlug);

        $voidSlug = Slug::findOne(['id' => 999]);
        $I->assertNull($voidSlug);

        $voidSlug = Slug::find()->where(['id' => 6])->active()->one();
        $I->assertNull($voidSlug);

        $realSlug = Slug::find()->where(['id' => 5])->active()->one();
        $I->assertInstanceOf(Slug::class, $realSlug);
    }

    public function testDelete(SlugTester $I)
    {
        $slug = new Slug();
        $slug->path = '/test-slug.html';
        $slug->active = true;
        $I->assertTrue($slug->save());

        $insertedId = $slug->id;
        $dbSlug = Slug::findOne(['id' => $insertedId]);
        $I->assertInstanceOf(Slug::class, $dbSlug);
        $dbSlug->delete();

        $voidSlug = Slug::findOne(['id' => $insertedId]);
        $I->assertNull($voidSlug);
    }

    public function testSearch(SlugTester $I)
    {
        $slugs = Slug::find()->all();
        $I->assertCount(count($this->slugList), $slugs);

        foreach($this->slugList as $id => $config) {
            $slug = Slug::find()->where(['id' => $id])->one();
            $I->assertInstanceOf(Slug::class, $slug);
            $I->assertEquals($config['host'], $slug->host);
            $I->assertEquals($config['path'], $slug->path);
            $I->assertEquals($config['targetUrl'], $slug->targetUrl);
            $I->assertEquals($config['httpCode'], $slug->httpCode);
            $I->assertEquals($config['active'], $slug->active);
        }

        $slugs = Slug::find()->where(['host' => null])->all();
        $countNullHost = array_reduce($this->slugList, function($carry, $item) {
            return ((isset($item['host']) && $item['host'] === null) || isset($item['host']) === false) ? $carry + 1 : $carry;
        }, 0);
        $I->assertCount($countNullHost, $slugs);
        $slugs = Slug::find()->where(['host' => 'www.basehost.com'])->all();
        $countBasehostHost = array_reduce($this->slugList, function($carry, $item) {
            return (isset($item['host']) && $item['host'] === 'www.basehost.com') ? $carry + 1 : $carry;
        }, 0);
        $I->assertCount($countBasehostHost, $slugs);
        $slugs = Slug::find()->active()->all();
        $countActive = array_reduce($this->slugList, function($carry, $item) {
            return ($item['active'] === true) ? $carry + 1 : $carry;
        }, 0);
        $I->assertCount($countActive, $slugs);
        $slugs = Slug::find()->where(['host' => 'www.erroneoushost.com'])->all();
        $I->assertCount(0, $slugs);
    }

}
