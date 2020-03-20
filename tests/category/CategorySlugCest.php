<?php
/**
 * CategorySlugCest.php
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
namespace tests\category;

use blackcube\core\models\Category;
use blackcube\core\models\Tag;
use blackcube\core\models\Slug;
use tests\CategoryTester;
use tests\TagTester;
use blackcube\core\models\Bloc;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\helpers\Json;

/**
 * Test category
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class CategorySlugCest extends CategoryBase
{

    public function testLoad(CategoryTester $I)
    {
        $category = Category::findOne(['id' => 1]);
        $I->assertInstanceOf(Category::class, $category);
        $slug = $category->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slug);
    }

    public function testDetach(TagTester $I)
    {
        $category = Category::findOne(['id' => 1]);
        $I->assertInstanceOf(Category::class, $category);
        $slug = $category->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $slugId = $slug->id;
        $status = $category->detachSlug();
        $I->assertTrue($status);
        $removedSlug = Slug::findOne(['id' => $slugId]);
        $I->assertNull($removedSlug);

        $noSlugCategory = Category::findOne(['id' => 2]);
        $I->assertInstanceOf(Category::class, $noSlugCategory);
        $noSlug = $noSlugCategory->getSlug()->one();
        $I->assertNull($noSlug);
        $status = $noSlugCategory->detachSlug();
        $I->assertFalse($status);

    }

    public function testAttach(TagTester $I)
    {
        $noSlugCategory = Category::findOne(['id' => 2]);
        $I->assertInstanceOf(Category::class, $noSlugCategory);
        $noSlug = $noSlugCategory->getSlug()->one();
        $I->assertNull($noSlug);
        $slug = Slug::findOne(['id' => 20]);
        $I->assertInstanceOf(Category::class, $noSlugCategory);
        $status = $noSlugCategory->attachSlug($slug);
        $I->assertTrue($status);
        $noSlugCategory->refresh();
        $slugAttached = $noSlugCategory->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slugAttached);
        $I->assertEquals(20, $slugAttached->id);
    }

    public function testReAttach(TagTester $I)
    {
        $category = Category::findOne(['id' => 1]);
        $I->assertInstanceOf(Category::class, $category);
        $initialSlug = $category->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $initialSlug);
        $initialSlugId = $initialSlug->id;
        $newSlug = Slug::findOne(['id' => 20]);
        $I->assertInstanceOf(Slug::class, $newSlug);
        $status = $category->attachSlug($newSlug);
        $I->assertTrue($status);
        $attachedSlug = $category->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $attachedSlug);
        $I->assertEquals(20, $attachedSlug->id);
        $deletedSlug = Slug::findOne(['id' => $initialSlugId]);
        $I->assertNull($deletedSlug);

    }

}
