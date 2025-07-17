<?php
/**
 * NodeTagCest.php
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
use blackcube\core\models\Tag;
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
class NodeTagCest extends NodeBase
{
    public function testTag(NodeTester $I)
    {
        $node = Node::findOne(['id' => 1]);
        $I->assertInstanceOf(Node::class, $node);
        $tags = $node->getTags()->active()->all();
        $I->assertCount(3, $tags);
        $I->assertEquals(3, $node->getTags()->active()->count());
        $tagList = array_filter($this->nodeTagLinks, function($item) use ($node) {
            return $item['nodeId'] == $node->id;
        });
        $tagList = array_map(function($item) {
            return $item['tagId'];
        }, $tagList);
        foreach($node->getTags()->active()->each() as $i => $tag) {
            $I->assertInstanceOf(Tag::class, $tag);
            $I->assertContains($tag->id, $tagList);
        }
    }

    public function testNoTag(NodeTester $I)
    {
        $node = Node::findOne(['id' => 6]);
        $I->assertInstanceOf(Node::class, $node);
        $tags = $node->getTags()->active()->all();
        $I->assertCount(0, $tags);
        $I->assertEquals(0, $node->getTags()->active()->count());


        $node = Node::findOne(['id' => 5]);
        $I->assertInstanceOf(Node::class, $node);
        $tags = $node->getTags()->active()->all();
        $I->assertCount(0, $tags);
        $I->assertEquals(0, $node->getTags()->active()->count());
        //TODO: fix tag research

    }

}
