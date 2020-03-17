<?php
/**
 * NodeCompositeCest.php
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
namespace tests\node;

use blackcube\core\models\Node;
use blackcube\core\models\NodeBloc;
use blackcube\core\models\NodeComposite;
use blackcube\core\models\Slug;
use blackcube\core\models\Composite;
use tests\NodeTester;
use blackcube\core\models\Bloc;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\helpers\Json;

/**
 * Test node composite
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class NodeCompositeCest extends NodeBase
{

    public function testLoad(NodeTester $I)
    {
        $node = Node::findOne(['id' => 1]);
        $composites = $node->composites;
        $countComposites = array_reduce($this->nodeCompositeLinks, function($carry, $item) use ($node) {
            return ($item['nodeId'] === $node->id) ? $carry + 1 : $carry;
        }, 0);

        $I->assertCount($countComposites, $composites);
        foreach($this->nodeCompositeLinks as $nodeComposite) {
            if ($nodeComposite['nodeId'] === $node->id) {
                $compositeIndex = $nodeComposite['order'] - 1;
                $I->assertEquals($nodeComposite['compositeId'], $composites[$compositeIndex]->id);
            }
        }
    }

    public function testMove(NodeTester $I)
    {
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(1, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['compositeId']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['order']);
        $node = Node::find()->where(['id' => 1])->one();
        $composites = $node->composites;
        $moveComposite1 = $composites[0];
        $status = $node->moveComposite($moveComposite1, -1);
        $I->assertTrue($status);
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(2, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
        $I->assertEquals(1, $nodeCompositeLinks[1]['compositeId']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['order']);
    }

    public function testDetach(NodeTester $I)
    {
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(1, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['compositeId']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['order']);
        $node = Node::find()->where(['id' => 1])->one();
        $composites = $node->composites;
        $compositeDetach1 = $composites[0];
        $status = $node->detachComposite($compositeDetach1);
        $I->assertTrue($status);
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(2, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
    }


    public function testAttach(NodeTester $I)
    {
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(1, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['compositeId']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['order']);

        $compositeToAttach = Composite::findOne(['id' => 5]);
        $I->assertInstanceOf(Composite::class, $compositeToAttach);
        $node = Node::find()->where(['id' => 1])->one();
        $status = $node->attachComposite($compositeToAttach);
        $I->assertTrue($status);
        $composites = $node->composites;
        $I->assertCount(3, $composites);
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(5, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
        $I->assertEquals(1, $nodeCompositeLinks[1]['compositeId']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['order']);
        $I->assertEquals(2, $nodeCompositeLinks[2]['compositeId']);
        $I->assertEquals(3, $nodeCompositeLinks[2]['order']);
    }

    public function testAttachEnd(NodeTester $I)
    {
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(1, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['compositeId']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['order']);

        $compositeToAttach = Composite::findOne(['id' => 5]);
        $I->assertInstanceOf(Composite::class, $compositeToAttach);
        $node = Node::find()->where(['id' => 1])->one();
        $status = $node->attachComposite($compositeToAttach, -1);
        $I->assertTrue($status);
        $composites = $node->composites;
        $I->assertCount(3, $composites);
        $nodeCompositeLinks = NodeComposite::find()->where(['nodeId' => 1])->orderBy(['order' => SORT_ASC])->asArray()->all();
        $I->assertEquals(1, $nodeCompositeLinks[0]['compositeId']);
        $I->assertEquals(1, $nodeCompositeLinks[0]['order']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['compositeId']);
        $I->assertEquals(2, $nodeCompositeLinks[1]['order']);
        $I->assertEquals(5, $nodeCompositeLinks[2]['compositeId']);
        $I->assertEquals(3, $nodeCompositeLinks[2]['order']);
    }

}
