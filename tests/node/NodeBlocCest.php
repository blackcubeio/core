<?php
/**
 * NodeBlocCest.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
namespace tests\node;

use blackcube\core\models\Node;
use blackcube\core\models\NodeBloc;
use blackcube\core\models\Slug;
use tests\NodeTester;
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
class NodeBlocCest extends NodeBase
{

    public function testLoad(NodeTester $I)
    {
        $node = Node::findOne(['id' => 1]);
        $blocs = $node->blocs;
        $countBlocs = array_reduce($this->nodeBlocLinks, function($carry, $item) use ($node) {
            return ($item['nodeId'] === $node->id) ? $carry + 1 : $carry;
        }, 0);

        $I->assertCount($countBlocs, $blocs);
        foreach($this->nodeBlocLinks as $nodeBloc) {
            if ($nodeBloc['nodeId'] === $node->id) {
                $blocIndex = $nodeBloc['order'] - 1;
                $I->assertEquals($nodeBloc['blocId'], $blocs[$blocIndex]->id);
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

    public function testMove(NodeTester $I)
    {
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(6, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
        $I->assertEquals(7, $nodeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $nodeBlocLinks[1]['order']);
        $node = Node::find()->where(['id' => 1])->one();
        $blocs = $node->blocs;
        $moveBloc1 = $blocs[0];
        $status = $node->moveBloc($moveBloc1, -1);
        $I->assertTrue($status);
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(7, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
        $I->assertEquals(6, $nodeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $nodeBlocLinks[1]['order']);
    }

    public function testDetach(NodeTester $I)
    {
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(6, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
        $I->assertEquals(7, $nodeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $nodeBlocLinks[1]['order']);
        $node = Node::find()->where(['id' => 1])->one();
        $blocs = $node->blocs;
        $blocDetach1 = $blocs[0];
        $status = $node->detachBloc($blocDetach1);
        $I->assertTrue($status);
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(7, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
    }

    public function testAttach(NodeTester $I)
    {
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(6, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
        $I->assertEquals(7, $nodeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $nodeBlocLinks[1]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $node = Node::find()->where(['id' => 1])->one();
        $status = $node->attachBloc($blocToAttach);
        $I->assertTrue($status);
        $blocs = $node->blocs;
        $I->assertCount(3, $blocs);
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(18, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
        $I->assertEquals(6, $nodeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $nodeBlocLinks[1]['order']);
        $I->assertEquals(7, $nodeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $nodeBlocLinks[2]['order']);
    }

    public function testAttachEnd(NodeTester $I)
    {
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(6, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
        $I->assertEquals(7, $nodeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $nodeBlocLinks[1]['order']);

        $blocToAttach = Bloc::findOne(['id' => 18]);
        $I->assertInstanceOf(Bloc::class, $blocToAttach);
        $node = Node::find()->where(['id' => 1])->one();
        $status = $node->attachBloc($blocToAttach, -1);
        $I->assertTrue($status);
        $blocs = $node->blocs;
        $I->assertCount(3, $blocs);
        $nodeBlocLinks = NodeBloc::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(6, $nodeBlocLinks[0]['blocId']);
        $I->assertEquals(1, $nodeBlocLinks[0]['order']);
        $I->assertEquals(7, $nodeBlocLinks[1]['blocId']);
        $I->assertEquals(2, $nodeBlocLinks[1]['order']);
        $I->assertEquals(18, $nodeBlocLinks[2]['blocId']);
        $I->assertEquals(3, $nodeBlocLinks[2]['order']);
    }


}
