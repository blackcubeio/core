<?php
/**
 * CategoryCest.php
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
use blackcube\core\models\Slug;
use tests\CategoryTester;
use yii\base\Model;
use yii\base\UnknownPropertyException;

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
class CategoryCest extends CategoryBase
{

    public function testInsert(CategoryTester $I)
    {
        $category = new Category();
        $category->name = 'test category';
        $category->active = true;
        $category->languageId = 'fr-FR';
        $category->slugId = 19;
        $I->assertTrue($category->validate());
        $I->assertTrue($category->save());
        $savedCategory = Category::findOne(['id' => $category->id]);
        $I->assertInstanceOf(Category::class, $savedCategory);
        $I->assertEquals($category->name, $savedCategory->name);
        $I->assertEquals($category->languageId, $savedCategory->languageId);
        $I->assertEquals($category->active, $savedCategory->active);
        $I->assertEquals($category->slugId, $savedCategory->slugId);

        $erroneousCategory = new Category();
        $erroneousCategory->slugId = 18;
        $erroneousCategory->languageId = 'fr-FR';
        $erroneousCategory->name = 'new category';
        $erroneousCategory->slugId = 18;
        $I->assertFalse($erroneousCategory->validate());
        $I->assertFalse($erroneousCategory->save());
    }

    public function testFind(CategoryTester $I)
    {
        $category = new Category();
        $category->name = 'test category';
        $category->active = true;
        $category->languageId = 'fr-FR';
        $category->slugId = 19;
        $I->assertTrue($category->save());

        $realCategory = Category::findOne(['id' => $category->id]);
        $I->assertInstanceOf(Category::class, $realCategory);

        $activeCategory = Category::find()->where(['id' => 1])->active()->one();
        $I->assertInstanceOf(Category::class,$activeCategory);

        $voidCategory = Category::findOne(['id' => 999]);
        $I->assertNull($voidCategory);

        $inactiveCategory = Category::find()->where(['id' => 7])->active()->one();
        $I->assertNull($inactiveCategory);
    }

    public function testDelete(CategoryTester $I)
    {
        $category = new Category();
        $category->name = 'test category';
        $category->active = true;
        $category->languageId = 'fr-FR';
        $category->slugId = 19;
        $I->assertTrue($category->save());

        $insertedId = $category->id;
        $dbCategory = Category::findOne(['id' => $insertedId]);
        $I->assertInstanceOf(Category::class, $dbCategory);
        $dbCategory->delete();

        $voidCategory = Category::findOne(['id' => $insertedId]);
        $I->assertNull($voidCategory);
    }

    public function testSearch(CategoryTester $I)
    {
        $categories = Category::find()->all();
        $I->assertCount(count($this->categoryList), $categories);

        foreach($this->categoryList as $id => $config) {
            $category = Category::find()->where(['id' => $id])->one();
            $I->assertInstanceOf(Category::class, $category);
            $I->assertEquals($config['name'], $category->name);
            $I->assertEquals($config['languageId'], $category->languageId);
            $I->assertEquals($config['active'], $category->active);
            if (isset($config['slugId'])) {
                $I->assertEquals($config['slugId'], $category->slugId);
            }
        }

        $categories = Category::find()->where(['slugId' => 18])->all();
        $I->assertCount(1, $categories);
        $categories = Category::find()->where(['slugId' => 999])->all();
        $I->assertCount(0, $categories);
    }

    public function testSlug(CategoryTester $I)
    {
        $slug = Slug::find()->where(['id' => 18])->active()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $category = $slug->element;
        $I->assertInstanceOf(Category::class, $category);

        $slug = Slug::find()->where(['id' => 19])->active()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $category = $slug->element;
        $I->assertNull($category);
    }

}
