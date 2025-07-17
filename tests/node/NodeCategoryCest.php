<?php
/**
 * NodeCategoryCest.php
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
use blackcube\core\models\Category;
use blackcube\core\models\Node;
use tests\NodeTester;

/**
 * Test node
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class NodeCategoryCest extends NodeBase
{
    public function testCategory(NodeTester $I)
    {
        $node = Node::find()->where(['id' => 5])->active()->one();
        $I->assertInstanceOf(Node::class, $node);
        $tags = $node->getTags()->all();
        $tags2 = $node->getTags()->active()->all();

        $node = Node::find()->where(['id' => 1])->active()->one();
        $I->assertInstanceOf(Node::class, $node);
        $tags = $node->getTags()->all();
        $tags2 = $node->getTags()->active()->all();
        $categories = $node->categories;
        $I->assertCount(3, $categories);
        $I->assertEquals(2, $node->getCategories()->active()->count());
    }

    public function testNoCategory(NodeTester $I)
    {
        $node = Node::findOne(['id' => 6]);
        $I->assertInstanceOf(Node::class, $node);
        $categories = $node->getCategories()->active()->all();
        $I->assertCount(0, $categories);
        $I->assertEquals(0, $node->getCategories()->active()->count());

        //TODO: find a simple way to retrieve only active categories
        /*/
        $node = Node::findOne(['id' => 5]);
        $I->assertInstanceOf(Node::class, $node);
        $categories = $node->getCategories()->active()->all();
        $I->assertCount(0, $categories);
        /**/


    }

}
