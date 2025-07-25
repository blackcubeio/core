<?php
/**
 * CompositeElementCest.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
namespace tests\composite;

use blackcube\core\models\Composite;
use tests\CompositeTester;
use blackcube\core\models\Tag;

/**
 * Test composite
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class CompositeElementCest extends CompositeBase
{

    public function testTags(CompositeTester $I)
    {
        $composite = Composite::findOne(['id' => 1]);
        $countTags = array_reduce($this->compositeTagLinks, function($carry, $item) use ($composite) {
            return ($item['compositeId'] === $composite->id) ? $carry + 1 : $carry;
        }, 0);
        $tags = $composite->tags;
        $I->assertCount($countTags, $tags);

        $activeTags = $composite->getTags()->active()->all();
        $I->assertCount(3, $activeTags);

        $newTag = Tag::findOne(['id' => 8]);
        $attachStatus = $composite->attachTag($newTag);
        $I->assertTrue($attachStatus);

        $activeTags = $composite->getTags()->active()->all();
        $I->assertCount(4, $activeTags);

        $detachStatus = $composite->detachTag($newTag);
        $I->assertTrue($detachStatus);

        $detachStatus = $composite->detachTag($newTag);
        $I->assertFalse($detachStatus);

        $activeTags = $composite->getTags()->active()->all();
        $I->assertCount(3, $activeTags);

        $existingTag = Tag::findOne(['id' => 2]);
        $attachStatus = $composite->attachTag($existingTag);
        $I->assertFalse($attachStatus);

    }

}
