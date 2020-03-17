<?php
/**
 * NodeElementCest.php
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
use blackcube\core\models\Tag;
use tests\NodeTester;

/**
 * Test node
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class NodeElementCest extends NodeBase
{

    public function testTags(NodeTester $I)
    {
        $node = Node::findOne(['id' => 1]);
        $countTags = array_reduce($this->nodeTagLinks, function($carry, $item) use ($node) {
            return ($item['nodeId'] === $node->id) ? $carry + 1 : $carry;
        }, 0);
        $tags = $node->tags;
        $I->assertCount($countTags, $tags);

        $activeTags = $node->getTags()->active()->all();
        $I->assertCount(3, $activeTags);

        $newTag = Tag::findOne(['id' => 8]);
        $attachStatus = $node->attachTag($newTag);
        $I->assertTrue($attachStatus);

        $activeTags = $node->getTags()->active()->all();
        $I->assertCount(4, $activeTags);

        $detachStatus = $node->detachTag($newTag);
        $I->assertTrue($detachStatus);

        $detachStatus = $node->detachTag($newTag);
        $I->assertFalse($detachStatus);

        $activeTags = $node->getTags()->active()->all();
        $I->assertCount(3, $activeTags);

        $existingTag = Tag::findOne(['id' => 2]);
        $attachStatus = $node->attachTag($existingTag);
        $I->assertFalse($attachStatus);

    }

}
