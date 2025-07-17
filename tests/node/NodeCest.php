<?php
/**
 * NodeTest.php
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
use tests\NodeTester;

/**
 * Test matrix basic functions
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class NodeCest extends NodeBase
{

    public function testChildren(NodeTester $I)
    {
        $node = Node::findOne(['id' => 1]);
        $children = $node->getChildren();
        $I->assertCount(12, $node->children);
        foreach ($children->each() as $i => $child) {
            $I->assertEquals(($i+2), $child->id);
        }
        $node = Node::findOne(['id' => 10]);
        $I->assertCount(2, $node->children);
        $I->assertEquals($this->nodeList[11]['nodePath'], $node->children[0]->path);
        $I->assertEquals($this->nodeList[12]['nodePath'], $node->children[1]->path);
    }

    public function testTree(NodeTester $I)
    {
        $node = Node::findOne(['id' => 1]);
        $tree = $node->getTree();
        $I->assertCount(13, $node->tree);
        foreach ($tree->each() as $i => $subNode) {
            $I->assertEquals(($i+1), $subNode->id);
        }
        $node = Node::findOne(['id' => 10]);
        $I->assertCount(2, $node->children);
        $I->assertEquals($this->nodeList[11]['nodePath'], $node->children[0]->path);
        $I->assertEquals($this->nodeList[12]['nodePath'], $node->children[1]->path);
    }

    public function testSiblings(NodeTester $I)
    {
        $node = Node::findOne(['id' => 12]);
        $I->assertEquals(1, $node->getSiblings()->count());
        $I->assertEquals(11, $node->siblings[0]->id);

        $node = Node::findOne(['id' => 6]);
        $I->assertEquals(3, $node->getSiblings()->count());
        $I->assertEquals(2, $node->siblings[0]->id);
        $I->assertEquals(3, $node->siblings[1]->id);
        $I->assertEquals(9, $node->siblings[2]->id);

        $node = Node::findOne(['id' => 1]);
        $I->assertEquals(0, $node->getSiblings()->count());
    }

    public function testSiblingsTrees(NodeTester $I)
    {
        $node = Node::findOne(['id' => 12]);
        $I->assertEquals(1, $node->getSiblingsTrees()->count());
        $I->assertEquals(11, $node->siblings[0]->id);

        $node = Node::findOne(['id' => 6]);
        $I->assertEquals(9, $node->getSiblingsTrees()->count());
        $I->assertEquals(2, $node->siblingsTrees[0]->id);
        $I->assertEquals(3, $node->siblingsTrees[1]->id);
        $I->assertEquals(4, $node->siblingsTrees[2]->id);
        $I->assertEquals(5, $node->siblingsTrees[3]->id);
        $I->assertEquals(9, $node->siblingsTrees[4]->id);
        $I->assertEquals(10, $node->siblingsTrees[5]->id);
        $I->assertEquals(11, $node->siblingsTrees[6]->id);
        $I->assertEquals(12, $node->siblingsTrees[7]->id);
        $I->assertEquals(13, $node->siblingsTrees[8]->id);

        $node = Node::findOne(['id' => 13]);
        $I->assertEquals(3, $node->getSiblingsTrees()->count());
        $I->assertEquals(10, $node->siblingsTrees[0]->id);
        $I->assertEquals(11, $node->siblingsTrees[1]->id);
        $I->assertEquals(12, $node->siblingsTrees[2]->id);

    }

    public function testPreviousSiblingsTrees(NodeTester $I)
    {
        $node = Node::findOne(['id' => 3]);
        $I->assertEquals(1, $node->getPreviousSiblingsTrees()->count());
        $I->assertEquals(2, $node->previousSiblingsTrees[0]->id);

        $node = Node::findOne(['id' => 6]);
        $I->assertEquals(4, $node->getPreviousSiblingsTrees()->count());
        $I->assertEquals(2, $node->previousSiblingsTrees[0]->id);
        $I->assertEquals(3, $node->previousSiblingsTrees[1]->id);
        $I->assertEquals(4, $node->previousSiblingsTrees[2]->id);
        $I->assertEquals(5, $node->previousSiblingsTrees[3]->id);

        $node = Node::findOne(['id' => 2]);
        $I->assertEquals(0, $node->getPreviousSiblingsTrees()->count());

        $node = Node::findOne(['id' => 1]);
        $I->assertEquals(0, $node->getPreviousSiblingsTrees()->count());
    }

    public function testPreviousSiblings(NodeTester $I)
    {
        $node = Node::findOne(['id' => 3]);
        $I->assertEquals(1, $node->getPreviousSiblings()->count());
        $I->assertEquals(2, $node->previousSiblings[0]->id);

        $node = Node::findOne(['id' => 6]);
        $I->assertEquals(2, $node->getPreviousSiblings()->count());
        $I->assertEquals(2, $node->previousSiblings[0]->id);
        $I->assertEquals(3, $node->previousSiblings[1]->id);

        $node = Node::findOne(['id' => 2]);
        $I->assertEquals(0, $node->getPreviousSiblings()->count());
    }

    public function testPreviousSibling(NodeTester $I)
    {
        $node = Node::findOne(['id' => 3]);
        $I->assertEquals(1, $node->getPreviousSibling()->count());
        $I->assertEquals(2, $node->previousSibling->id);

        $node = Node::findOne(['id' => 6]);
        // $I->assertEquals(1, $node->getPreviousSibling()->count());
        $I->assertEquals(3, $node->previousSibling->id);

        $node = Node::findOne(['id' => 2]);
        $I->assertEquals(0, $node->getPreviousSibling()->count());
    }

    public function testNextSiblingsTrees(NodeTester $I)
    {
        $node = Node::findOne(['id' => 3]);
        $I->assertEquals(8, $node->getNextSiblingsTrees()->count());
        $I->assertEquals(6, $node->nextSiblingsTrees[0]->id);
        $I->assertEquals(7, $node->nextSiblingsTrees[1]->id);
        $I->assertEquals(8, $node->nextSiblingsTrees[2]->id);
        $I->assertEquals(9, $node->nextSiblingsTrees[3]->id);
        $I->assertEquals(10, $node->nextSiblingsTrees[4]->id);
        $I->assertEquals(11, $node->nextSiblingsTrees[5]->id);
        $I->assertEquals(12, $node->nextSiblingsTrees[6]->id);
        $I->assertEquals(13, $node->nextSiblingsTrees[7]->id);

        $node = Node::findOne(['id' => 6]);
        $I->assertEquals(5, $node->getNextSiblingsTrees()->count());
        $I->assertEquals(9, $node->nextSiblingsTrees[0]->id);
        $I->assertEquals(10, $node->nextSiblingsTrees[1]->id);
        $I->assertEquals(11, $node->nextSiblingsTrees[2]->id);
        $I->assertEquals(12, $node->nextSiblingsTrees[3]->id);
        $I->assertEquals(13, $node->nextSiblingsTrees[4]->id);

        $node = Node::findOne(['id' => 13]);
        $I->assertEquals(0, $node->getNextSiblingsTrees()->count());

        $node = Node::findOne(['id' => 1]);
        $I->assertEquals(0, $node->getNextSiblingsTrees()->count());
    }

    public function testNextSiblings(NodeTester $I)
    {
        $node = Node::findOne(['id' => 3]);
        $I->assertEquals(2, $node->getNextSiblings()->count());
        $I->assertEquals(6, $node->nextSiblings[0]->id);
        $I->assertEquals(9, $node->nextSiblings[1]->id);

        $node = Node::findOne(['id' => 6]);
        $I->assertEquals(1, $node->getNextSiblings()->count());
        $I->assertEquals(9, $node->nextSiblings[0]->id);

        $node = Node::findOne(['id' => 9]);
        $I->assertEquals(0, $node->getNextSiblings()->count());
    }

    public function testNextSibling(NodeTester $I)
    {
        $node = Node::findOne(['id' => 3]);
        // $I->assertEquals(1, $node->getPreviousSibling()->count());
        $I->assertEquals(6, $node->nextSibling->id);

        $node = Node::findOne(['id' => 6]);
        // $I->assertEquals(1, $node->getPreviousSibling()->count());
        $I->assertEquals(9, $node->nextSibling->id);

        $node = Node::findOne(['id' => 12]);
        $I->assertEquals(0, $node->getNextSibling()->count());
    }

    public function testSaveIntoChildren(NodeTester $I) {
        $node = Node::findOne(['id' => 6]);
        $pivot = Node::findOne(['id' => 3]);
        $node->saveInto($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['id' => 6]);
        $I->assertEquals('1.2.3', $node->path);
    }

    public function testSaveInto(NodeTester $I) {
        $node = Node::findOne(['id' => 11]);
        $pivot = Node::findOne(['id' => 12]);
        $node->saveInto($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['id' => 11]);
        $I->assertEquals('1.4.1.1.1', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.1', $pivot->path);

    }

    public function testSaveAfter(NodeTester $I) {
        $node = Node::findOne(['id' => 11]);
        $pivot = Node::findOne(['id' => 12]);
        $node->saveAfter($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['id' => 11]);
        $I->assertEquals('1.4.1.2', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.1', $pivot->path);
    }

    public function testSaveAfterTree(NodeTester $I) {
        $node = Node::findOne(['id' => 9]);
        $pivot = Node::findOne(['id' => 4]);
        $node->saveAfter($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['id' => 9]);
        $I->assertEquals('1.2.2', $node->path);
        $pivot = Node::findOne(['id' => 4]);
        $I->assertEquals('1.2.1', $pivot->path);
        $node = Node::findOne(['id' => 9]);
        $I->assertEquals('1.2.2', $node->path);
        $node = Node::findOne(['id' => 10]);
        $I->assertEquals('1.2.2.1', $node->path);
        $node = Node::findOne(['id' => 11]);
        $I->assertEquals('1.2.2.1.1', $node->path);
        $node = Node::findOne(['id' => 12]);
        $I->assertEquals('1.2.2.1.2', $node->path);
        $node = Node::findOne(['id' => 13]);
        $I->assertEquals('1.2.2.2', $node->path);
    }

    public function testSaveBefore(NodeTester $I) {
        $node = Node::findOne(['id' => 12]);
        $pivot = Node::findOne(['id' => 11]);
        $node->saveBefore($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['id' => 11]);
        $I->assertEquals('1.4.1.2', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.1', $pivot->path);
    }

    public function testSaveBeforeTree(NodeTester $I) {
        $node = Node::findOne(['id' => 9]);
        $pivot = Node::findOne(['id' => 5]);
        $node->saveBefore($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['id' => 9]);
        $I->assertEquals('1.2.2', $node->path);
        $pivot = Node::findOne(['id' => 4]);
        $I->assertEquals('1.2.1', $pivot->path);
        $node = Node::findOne(['id' => 9]);
        $I->assertEquals('1.2.2', $node->path);
        $node = Node::findOne(['id' => 10]);
        $I->assertEquals('1.2.2.1', $node->path);
        $node = Node::findOne(['id' => 11]);
        $I->assertEquals('1.2.2.1.1', $node->path);
        $node = Node::findOne(['id' => 12]);
        $I->assertEquals('1.2.2.1.2', $node->path);
        $node = Node::findOne(['id' => 13]);
        $I->assertEquals('1.2.2.2', $node->path);
        $node = Node::findOne(['id' => 5]);
        $I->assertEquals('1.2.3', $node->path);
    }

    public function testSaveNewIntoChildren(NodeTester $I) {
        $node = new Node();
        $node->languageId = 'fr';
        $node->name = 'newnode';
        $pivot = Node::findOne(['id' => 3]);
        $node->saveInto($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['name' => 'newnode']);
        $I->assertEquals('1.2.3', $node->path);
    }

    public function testSaveNewInto(NodeTester $I) {
        $node = new Node();
        $node->languageId = 'fr';
        $node->name = 'newnode';
        $pivot = Node::findOne(['id' => 12]);
        $node->saveInto($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['name' => 'newnode']);
        $I->assertEquals('1.4.1.2.1', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.2', $pivot->path);

    }

    public function testSaveNewAfter(NodeTester $I) {
        $node = new Node();
        $node->languageId = 'fr';
        $node->name = 'newnode';
        $pivot = Node::findOne(['id' => 12]);
        $node->saveAfter($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['name' => 'newnode']);
        $I->assertEquals('1.4.1.3', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.2', $pivot->path);
    }

    public function testSaveNewAfterWithSibling(NodeTester $I) {
        $node = new Node();
        $node->languageId = 'fr';
        $node->name = 'newnode';
        $pivot = Node::findOne(['id' => 11]);
        $node->saveAfter($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['name' => 'newnode']);
        $I->assertEquals('1.4.1.2', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.3', $pivot->path);
    }

    public function testSaveNewBefore(NodeTester $I) {
        $node = new Node();
        $node->languageId = 'fr';
        $node->name = 'newnode';
        $pivot = Node::findOne(['id' => 11]);
        $node->saveBefore($pivot);
        $I->assertCount(0, $node->errors);
        $node = Node::findOne(['name' => 'newnode']);
        $I->assertEquals('1.4.1.1', $node->path);
        $pivot = Node::findOne(['id' => 11]);
        $I->assertEquals('1.4.1.2', $pivot->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.3', $pivot->path);
    }
}
