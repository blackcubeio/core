<?php
/**
 * CompositeBlocCest.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
namespace tests\composite;

use blackcube\core\models\Composite;
use blackcube\core\models\CompositeBloc;
use blackcube\core\models\Slug;
use tests\CompositeTester;
use blackcube\core\models\Bloc;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\helpers\Json;

/**
 * Test composite
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class CompositeBlocCest extends CompositeBase
{

    public function testLoad(CompositeTester $I)
    {
        $composite = Composite::findOne(['id' => 1]);
        $blocs = $composite->blocs;
        $countBlocs = array_reduce($this->compositeBlocLinks, function($carry, $item) use ($composite) {
            return ($item['compositeId'] === $composite->id) ? $carry + 1 : $carry;
        }, 0);

        $I->assertCount($countBlocs, $blocs);
        foreach($this->compositeBlocLinks as $compositeBloc) {
            if ($compositeBloc['compositeId'] === $composite->id) {
                $blocIndex = $compositeBloc['order'] - 1;
                $I->assertEquals($compositeBloc['blocId'], $blocs[$blocIndex]->id);
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

    public function testMove(CompositeTester $I)
    {
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(8, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(9, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(10, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);
        $composite = Composite::find()->where(['id' => 1])->one();
        $blocs = $composite->blocs;
        $moveBloc2 = $blocs[1];
        $status = $composite->moveBloc($moveBloc2, 1);
        $I->assertTrue($status);
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(9, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(8, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(10, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);
    }

    public function testMoveEnd(CompositeTester $I)
    {
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(8, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(9, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(10, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);
        $composite = Composite::find()->where(['id' => 1])->one();
        $blocs = $composite->blocs;
        $moveBloc1 = $blocs[0];
        $status = $composite->moveBloc($moveBloc1, 3);
        $I->assertTrue($status);
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(9, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(10, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(8, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);
    }

    public function testDetach(CompositeTester $I)
    {
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(8, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(9, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(10, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);
        $composite = Composite::find()->where(['id' => 1])->one();
        $blocs = $composite->blocs;
        $blocDetach1 = $blocs[0];
        $status = $composite->detachBloc($blocDetach1);
        $I->assertTrue($status);
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(9, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(10, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
    }

    public function testAttach(CompositeTester $I)
    {
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(8, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(9, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(10, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $composite = Composite::find()->where(['id' => 1])->one();
        $status = $composite->attachBloc($blocToAttach);
        $I->assertTrue($status);
        $blocs = $composite->blocs;
        $I->assertCount(4, $blocs);
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(18, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(8, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(9, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);
        $I->assertEquals(10, $compositeBlocLinks[3]['blocId']);
        $I->assertEquals(4, $compositeBlocLinks[3]['order']);
    }

    public function testAttachEnd(CompositeTester $I)
    {
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(8, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(9, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(10, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $composite = Composite::find()->where(['id' => 1])->one();
        $status = $composite->attachBloc($blocToAttach, -1);
        $I->assertTrue($status);
        $blocs = $composite->blocs;
        $I->assertCount(4, $blocs);
        $compositeBlocLinks = CompositeBloc::find()->where(['compositeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(8, $compositeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $compositeBlocLinks[0]['order']);
        $I->assertEquals(9, $compositeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $compositeBlocLinks[1]['order']);
        $I->assertEquals(10, $compositeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $compositeBlocLinks[2]['order']);
        $I->assertEquals(18, $compositeBlocLinks[3]['blocId']);
        $I->assertEquals(4, $compositeBlocLinks[3]['order']);
    }


}
