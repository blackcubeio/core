<?php
/**
 * TagCest.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
namespace tests\tag;

use blackcube\core\models\Tag;
use blackcube\core\models\Slug;
use tests\TagTester;
use yii\base\Model;
use yii\base\UnknownPropertyException;

/**
 * Test tag
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class TagCest extends TagBase
{

    public function testInsert(TagTester $I)
    {
        $tag = new Tag();
        $tag->name = 'test tag';
        $tag->active = true;
        $tag->categoryId = 2;
        $tag->slugId = 20;
        $I->assertTrue($tag->validate());
        $I->assertTrue($tag->save());
        $savedTag = Tag::findOne(['id' => $tag->id]);
        $I->assertInstanceOf(Tag::class, $savedTag);
        $I->assertEquals($tag->name, $savedTag->name);
        $I->assertEquals($tag->categoryId, $savedTag->categoryId);
        $I->assertEquals($tag->active, $savedTag->active);
        $I->assertEquals($tag->slugId, $savedTag->slugId);

        $erroneousTag = new Tag();
        $erroneousTag->slugId = 18;
        $erroneousTag->categoryId = 2;
        $erroneousTag->name = 'new tag';
        $erroneousTag->slugId = 17;
        $I->assertFalse($erroneousTag->validate());
        $I->assertFalse($erroneousTag->save());
    }

    public function testFind(TagTester $I)
    {
        $tag = new Tag();
        $tag->name = 'test tag';
        $tag->active = true;
        $tag->categoryId = 2;
        $tag->slugId = 20;
        $I->assertTrue($tag->save());

        $realTag = Tag::findOne(['id' => $tag->id]);
        $I->assertInstanceOf(Tag::class, $realTag);

        $activeTag = Tag::find()->where(['id' => 1])->active()->one();
        $I->assertInstanceOf(Tag::class,$activeTag);

        $voidTag = Tag::findOne(['id' => 999]);
        $I->assertNull($voidTag);

        $inactiveTag = Tag::find()->where(['id' => 13])->active()->one();
        $I->assertNull($inactiveTag);

        $inactiveTagByCategory = Tag::find()->where(['id' => 14])->active()->one();
        $I->assertNull($inactiveTagByCategory);
    }

    public function testDelete(TagTester $I)
    {
        $tag = new Tag();
        $tag->name = 'test tag';
        $tag->active = true;
        $tag->categoryId = 2;
        $tag->slugId = 20;
        $I->assertTrue($tag->save());

        $insertedId = $tag->id;
        $dbTag = Tag::findOne(['id' => $insertedId]);
        $I->assertInstanceOf(Tag::class, $dbTag);
        $dbTag->delete();

        $voidTag = Tag::findOne(['id' => $insertedId]);
        $I->assertNull($voidTag);
    }

    public function testSearch(TagTester $I)
    {
        $tags = Tag::find()->all();
        $I->assertCount(count($this->tagList), $tags);

        foreach($this->tagList as $id => $config) {
            $tag = Tag::find()->where(['id' => $id])->one();
            $I->assertInstanceOf(Tag::class, $tag);
            $I->assertEquals($config['name'], $tag->name);
            $I->assertEquals($config['active'], $tag->active);
            $I->assertEquals($config['categoryId'], $tag->categoryId);
            if (isset($config['slugId'])) {
                $I->assertEquals($config['slugId'], $tag->slugId);
            }
        }

        $tags = Tag::find()->where(['slugId' => 17])->all();
        $I->assertCount(1, $tags);
        $tags = Tag::find()->where(['slugId' => 999])->all();
        $I->assertCount(0, $tags);
    }

    public function testSlug(TagTester $I)
    {
        $slug = Slug::find()->where(['id' => 17])->active()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $tag = $slug->element;
        $I->assertInstanceOf(Tag::class, $tag);

        $slug = Slug::find()->where(['id' => 20])->active()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $tag = $slug->element;
        $I->assertNull($tag);
    }

}
