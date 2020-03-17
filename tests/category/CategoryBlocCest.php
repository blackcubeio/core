<?php
/**
 * CategoryBlocCest.php
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
use blackcube\core\models\CategoryBloc;
use blackcube\core\models\Slug;
use tests\CategoryTester;
use blackcube\core\models\Bloc;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\helpers\Json;

/**
 * Test composite
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class CategoryBlocCest extends CategoryBase
{

    public function testLoad(CategoryTester $I)
    {
        $category = Category::findOne(['id' => 1]);
        $blocs = $category->blocs;
        $countBlocs = array_reduce($this->categoryBlocLinks, function($carry, $item) use ($category) {
            return ($item['categoryId'] === $category->id) ? $carry + 1 : $carry;
        }, 0);

        $I->assertCount($countBlocs, $blocs);
        foreach($this->categoryBlocLinks as $categoryBloc) {
            if ($categoryBloc['categoryId'] === $category->id) {
                $blocIndex = $categoryBloc['order'] - 1;
                $I->assertEquals($categoryBloc['blocId'], $blocs[$blocIndex]->id);
            }
        }
        foreach($blocs as $bloc) {
            $structuredData = Json::decode($this->blocList[$bloc->id]['data']);
            if (isset($structuredData['title']) === true) {
                $I->assertEquals($structuredData['title'], $bloc->title);
            }
            if (isset($structuredData['text']) === true) {
                $I->assertEquals($structuredData['text'], $bloc->text);
            }
        }
    }

    public function testDetach(CategoryTester $I)
    {
        $categoryBlocLinks = CategoryBloc::find()->where(['categoryId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(17, $categoryBlocLinks[0]['blocId']);
        $I->assertEquals(1, $categoryBlocLinks[0]['order']);
        $category = Category::find()->where(['id' => 1])->one();
        $blocs = $category->blocs;
        $blocDetach1 = $blocs[0];
        $status = $category->detachBloc($blocDetach1);
        $I->assertTrue($status);
        $categoryBlocLinks = CategoryBloc::find()->where(['categoryId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertCount(0, $categoryBlocLinks);
    }

    public function testAttach(CategoryTester $I)
    {
        $categoryBlocLinks = CategoryBloc::find()->where(['categoryId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(17, $categoryBlocLinks[0]['blocId']);
        $I->assertEquals(1, $categoryBlocLinks[0]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $category = Category::find()->where(['id' => 1])->one();
        $status = $category->attachBloc($blocToAttach);
        $I->assertTrue($status);
        $blocs = $category->blocs;
        $I->assertCount(2, $blocs);
        $categoryBlocLinks = CategoryBloc::find()->where(['categoryId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(18, $categoryBlocLinks[0]['blocId']);
        $I->assertEquals(1, $categoryBlocLinks[0]['order']);
        $I->assertEquals(17, $categoryBlocLinks[1]['blocId']);
        $I->assertEquals(2, $categoryBlocLinks[1]['order']);
    }

    public function testAttachEnd(CategoryTester $I)
    {
        $categoryBlocLinks = CategoryBloc::find()->where(['categoryId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(17, $categoryBlocLinks[0]['blocId']);
        $I->assertEquals(1, $categoryBlocLinks[0]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $category = Category::find()->where(['id' => 1])->one();
        $status = $category->attachBloc($blocToAttach, -1);
        $I->assertTrue($status);
        $blocs = $category->blocs;
        $I->assertCount(2, $blocs);
        $categoryBlocLinks = CategoryBloc::find()->where(['categoryId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(17, $categoryBlocLinks[0]['blocId']);
        $I->assertEquals(1, $categoryBlocLinks[0]['order']);
        $I->assertEquals(18, $categoryBlocLinks[1]['blocId']);
        $I->assertEquals(2, $categoryBlocLinks[1]['order']);
    }

}
