<?php
/**
 * TagBlocCest.php
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
use blackcube\core\models\TagBloc;
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
class TagBlocCest extends TagBase
{

    public function testLoad(TagTester $I)
    {
        $tag = Tag::findOne(['id' => 1]);
        $blocs = $tag->blocs;
        $countBlocs = array_reduce($this->tagBlocLinks, function($carry, $item) use ($tag) {
            return ($item['tagId'] === $tag->id) ? $carry + 1 : $carry;
        }, 0);

        $I->assertCount($countBlocs, $blocs);
        foreach($this->tagBlocLinks as $tagBloc) {
            if ($tagBloc['tagId'] === $tag->id) {
                $blocIndex = $tagBloc['order'] - 1;
                $I->assertEquals($tagBloc['blocId'], $blocs[$blocIndex]->id);
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

    public function testDetach(TagTester $I)
    {
        $tagBlocLinks = TagBloc::find()->where(['tagId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(16, $tagBlocLinks[0]['blocId']);
        $I->assertEquals(1, $tagBlocLinks[0]['order']);
        $tag = Tag::find()->where(['id' => 1])->one();
        $blocs = $tag->blocs;
        $blocDetach1 = $blocs[0];
        $status = $tag->detachBloc($blocDetach1);
        $I->assertTrue($status);
        $tagBlocLinks = TagBloc::find()->where(['tagId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertCount(0, $tagBlocLinks);
    }

    public function testAttach(TagTester $I)
    {
        $tagBlocLinks = TagBloc::find()->where(['tagId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(16, $tagBlocLinks[0]['blocId']);
        $I->assertEquals(1, $tagBlocLinks[0]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $tag = Tag::find()->where(['id' => 1])->one();
        $status = $tag->attachBloc($blocToAttach);
        $I->assertTrue($status);
        $blocs = $tag->blocs;
        $I->assertCount(2, $blocs);
        $tagBlocLinks = TagBloc::find()->where(['tagId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(18, $tagBlocLinks[0]['blocId']);
        $I->assertEquals(1, $tagBlocLinks[0]['order']);
        $I->assertEquals(16, $tagBlocLinks[1]['blocId']);
        $I->assertEquals(2, $tagBlocLinks[1]['order']);
    }

    public function testAttachEnd(TagTester $I)
    {
        $tagBlocLinks = TagBloc::find()->where(['tagId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(16, $tagBlocLinks[0]['blocId']);
        $I->assertEquals(1, $tagBlocLinks[0]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $tag = Tag::find()->where(['id' => 1])->one();
        $status = $tag->attachBloc($blocToAttach, -1);
        $I->assertTrue($status);
        $blocs = $tag->blocs;
        $I->assertCount(2, $blocs);
        $tagBlocLinks = TagBloc::find()->where(['tagId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(16, $tagBlocLinks[0]['blocId']);
        $I->assertEquals(1, $tagBlocLinks[0]['order']);
        $I->assertEquals(18, $tagBlocLinks[1]['blocId']);
        $I->assertEquals(2, $tagBlocLinks[1]['order']);
    }

}
