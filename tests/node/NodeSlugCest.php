<?php
/**
 * NodeSlugCest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 */
namespace tests\node;

use blackcube\core\models\Node;
use blackcube\core\models\Slug;
use tests\NodeTester;

/**
 * Test node
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
 */
class NodeSlugCest extends NodeBase
{
    public function testSlug(NodeTester $I)
    {
        $node = Node::findOne(['id' => 2]);
        $I->assertInstanceOf(Node::class, $node);
        $I->assertEquals('node-1.1.html', $node->slug->path);
        $slug = Slug::findOne(['id' => 9]);
        $I->assertInstanceOf(Slug::class, $slug);
        $I->assertInstanceOf(Node::class, $slug->element);
        $I->assertEquals('1.1', $slug->element->path);
        $I->assertEquals('Node 1.1', $slug->element->name);
        $slug = Slug::findOne(['id' => 16]);
        $I->assertNull($slug->element);
    }

    public function testNoSlug(NodeTester $I)
    {
        $node = Node::findOne(['id' => 9]);
        $I->assertInstanceOf(Node::class, $node);
        $I->assertNull($node->slug);
    }

}
