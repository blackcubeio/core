<?php
/**
 * TagSlugCest.php
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
namespace tests\tag;

use blackcube\core\models\Tag;
use blackcube\core\models\Slug;
use tests\TagTester;
use blackcube\core\models\Bloc;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\helpers\Json;

/**
 * Test tag
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class TagSlugCest extends TagBase
{

    public function testLoad(TagTester $I)
    {
        $tag = Tag::findOne(['id' => 1]);
        $I->assertInstanceOf(Tag::class, $tag);
        $slug = $tag->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slug);
    }

    public function testDetach(TagTester $I)
    {
        $tag = Tag::findOne(['id' => 1]);
        $I->assertInstanceOf(Tag::class, $tag);
        $slug = $tag->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $slugId = $slug->id;
        $status = $tag->detachSlug();
        $I->assertTrue($status);
        $removedSlug = Slug::findOne(['id' => $slugId]);
        $I->assertNull($removedSlug);

        $noSlugTag = Tag::findOne(['id' => 2]);
        $I->assertInstanceOf(Tag::class, $noSlugTag);
        $noSlug = $noSlugTag->getSlug()->one();
        $I->assertNull($noSlug);
        $status = $noSlugTag->detachSlug();
        $I->assertFalse($status);

    }

    public function testAttach(TagTester $I)
    {
        $noSlugTag = Tag::findOne(['id' => 2]);
        $I->assertInstanceOf(Tag::class, $noSlugTag);
        $noSlug = $noSlugTag->getSlug()->one();
        $I->assertNull($noSlug);
        $slug = Slug::findOne(['id' => 20]);
        $I->assertInstanceOf(Tag::class, $noSlugTag);
        $status = $noSlugTag->attachSlug($slug);
        $I->assertTrue($status);
        $noSlugTag->refresh();
        $slugAttached = $noSlugTag->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slugAttached);
        $I->assertEquals(20, $slugAttached->id);
    }

    public function testReAttach(TagTester $I)
    {
        $tag = Tag::findOne(['id' => 1]);
        $I->assertInstanceOf(Tag::class, $tag);
        $initialSlug = $tag->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $initialSlug);
        $initialSlugId = $initialSlug->id;
        $newSlug = Slug::findOne(['id' => 20]);
        $I->assertInstanceOf(Slug::class, $newSlug);
        $status = $tag->attachSlug($newSlug);
        $I->assertTrue($status);
        $attachedSlug = $tag->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $attachedSlug);
        $I->assertEquals(20, $attachedSlug->id);
        $deletedSlug = Slug::findOne(['id' => $initialSlugId]);
        $I->assertNull($deletedSlug);

    }

}
