<?php
/**
 * NodeErrorCest.php
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
use blackcube\core\exceptions\InvalidNodeConfigurationException;
use blackcube\core\models\Node;
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
class NodeErrorCest extends NodeBase
{
    public function testExceptionSaveInto(NodeTester $I)
    {
        $pivot = Node::findOne(['id' => 1]);
        $I->assertInstanceOf(Node::class, $pivot);
        $insertNode = new Node();
        $insertNode->setNodePath('1.3');
        $insertNode->languageId = 'fr';
        $insertNode->name = 'Error node';

        $I->expectThrowable(InvalidNodeConfigurationException::class, function () use ($insertNode, $pivot) {
            $insertNode->saveInto($pivot);
        });
    }

    public function testErrorSaveInto(NodeTester $I)
    {
        $pivot = Node::findOne(['id' => 1]);
        $I->assertInstanceOf(Node::class, $pivot);
        $insertNode = new Node();
        $insertNode->name = 'Error node';

        $status = $insertNode->saveInto($pivot);
        $I->assertFalse($status);
        $I->assertNotEmpty($insertNode->errors);

        $node = Node::findOne(['id' => 6]);
        $pivot = Node::findOne(['id' => 3]);
        $node->languageId = null;
        $status = $node->saveInto($pivot);
        $I->assertCount(1, $node->errors);
        $I->assertFalse($status);
        $node = Node::findOne(['id' => 6]);
        $I->assertEquals('1.3', $node->path);

    }

    public function testExceptionSaveBefore(NodeTester $I)
    {
        $pivot = Node::findOne(['id' => 12]);
        $I->assertInstanceOf(Node::class, $pivot);
        $insertNode = new Node();
        $insertNode->setNodePath('1.3');
        $insertNode->languageId = 'fr';
        $insertNode->name = 'Error node';

        $I->expectThrowable(InvalidNodeConfigurationException::class, function () use ($insertNode, $pivot) {
            $insertNode->saveBefore($pivot);
        });
    }

    public function testErrorSaveBefore(NodeTester $I)
    {
        $pivot = Node::findOne(['id' => 12]);
        $I->assertInstanceOf(Node::class, $pivot);
        $insertNode = new Node();
        $insertNode->name = 'Error node';

        $status = $insertNode->saveBefore($pivot);
        $I->assertFalse($status);
        $I->assertNotEmpty($insertNode->errors);


        $node = Node::findOne(['id' => 12]);
        $pivot = Node::findOne(['id' => 11]);
        $node->languageId = null;
        $status = $node->saveBefore($pivot);
        $I->assertCount(1, $node->errors);
        $I->assertFalse($status);
        $node = Node::findOne(['id' => 11]);
        $I->assertEquals('1.4.1.1', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.2', $pivot->path);
    }

    public function testExceptionSaveAfter(NodeTester $I)
    {
        $pivot = Node::findOne(['id' => 12]);
        $I->assertInstanceOf(Node::class, $pivot);
        $insertNode = new Node();
        $insertNode->setNodePath('1.3');
        $insertNode->languageId = 'fr';
        $insertNode->name = 'Error node';

        $I->expectThrowable(InvalidNodeConfigurationException::class, function () use ($insertNode, $pivot) {
            $insertNode->saveAfter($pivot);
        });
    }

    public function testErrorSaveAfter(NodeTester $I)
    {
        $pivot = Node::findOne(['id' => 12]);
        $I->assertInstanceOf(Node::class, $pivot);
        $insertNode = new Node();
        $insertNode->name = 'Error node';

        $status = $insertNode->saveAfter($pivot);
        $I->assertFalse($status);
        $I->assertNotEmpty($insertNode->errors);

        $node = Node::findOne(['id' => 11]);
        $pivot = Node::findOne(['id' => 12]);
        $node->languageId = null;
        $status = $node->saveAfter($pivot);
        $I->assertFalse($status);
        $I->assertCount(1, $node->errors);
        $node = Node::findOne(['id' => 11]);
        $I->assertEquals('1.4.1.1', $node->path);
        $pivot = Node::findOne(['id' => 12]);
        $I->assertEquals('1.4.1.2', $pivot->path);

    }


}
