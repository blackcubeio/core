<?php
/**
 * NodeLanguageCest.php
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
use blackcube\core\models\Language;
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
