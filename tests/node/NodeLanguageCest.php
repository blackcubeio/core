<?php
/**
 * NodeLanguageCest.php
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
use blackcube\core\models\Language;
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
class NodeLanguageCest extends NodeBase
{
    public function testLanguage(NodeTester $I)
    {
        $node = Node::findOne(['id' => 1]);
        $I->assertInstanceOf(Node::class, $node);
        $language = $node->language;
        $I->assertInstanceOf(Language::class, $language);
        $I->assertEquals('French', $language->name);

        $node = Node::findOne(['id' => 5]);
        $I->assertInstanceOf(Node::class, $node);
        $language = $node->language;
        $I->assertInstanceOf(Language::class, $language);
        $I->assertEquals('French (France)', $language->name);
    }

    public function testMainLanguage(NodeTester $I)
    {
        $node = Node::findOne(['id' => 5]);
        $I->assertInstanceOf(Node::class, $node);
        $language = $node->mainLanguage;
        $I->assertInstanceOf(Language::class, $language);
        $I->assertEquals('French', $language->name);
    }

}
