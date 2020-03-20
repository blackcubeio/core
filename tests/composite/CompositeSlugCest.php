<?php
/**
 * CompositeSlugCest.php
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
namespace tests\composite;

use blackcube\core\models\Composite;
use blackcube\core\models\Tag;
use blackcube\core\models\Slug;
use tests\composite\CompositeBase;
use tests\CompositeTester;
use tests\TagTester;
use blackcube\core\models\Bloc;
use yii\base\Model;
use yii\base\UnknownPropertyException;
use yii\helpers\Json;

/**
 * Test composite
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class CompositeSlugCest extends CompositeBase
{

    public function testLoad(CompositeTester $I)
    {
        $composite = Composite::findOne(['id' => 1]);
        $I->assertInstanceOf(Composite::class, $composite);
        $slug = $composite->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slug);
    }

    public function testDetach(CompositeTester $I)
    {
        $composite = Composite::findOne(['id' => 1]);
        $I->assertInstanceOf(Composite::class, $composite);
        $slug = $composite->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $slugId = $slug->id;
        $status = $composite->detachSlug();
        $I->assertTrue($status);
        $removedSlug = Slug::findOne(['id' => $slugId]);
        $I->assertNull($removedSlug);

        $noSlugComposite = Composite::findOne(['id' => 4]);
        $I->assertInstanceOf(Composite::class, $noSlugComposite);
        $noSlug = $noSlugComposite->getSlug()->one();
        $I->assertNull($noSlug);
        $status = $noSlugComposite->detachSlug();
        $I->assertFalse($status);

    }

    public function testAttach(CompositeTester $I)
    {
        $noSlugComposite = Composite::findOne(['id' => 4]);
        $I->assertInstanceOf(Composite::class, $noSlugComposite);
        $noSlug = $noSlugComposite->getSlug()->one();
        $I->assertNull($noSlug);
        $slug = Slug::findOne(['id' => 20]);
        $I->assertInstanceOf(Composite::class, $noSlugComposite);
        $status = $noSlugComposite->attachSlug($slug);
        $I->assertTrue($status);
        $noSlugComposite->refresh();
        $slugAttached = $noSlugComposite->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $slugAttached);
        $I->assertEquals(20, $slugAttached->id);
    }

    public function testReAttach(CompositeTester $I)
    {
        $composite = Composite::findOne(['id' => 1]);
        $I->assertInstanceOf(Composite::class, $composite);
        $initialSlug = $composite->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $initialSlug);
        $initialSlugId = $initialSlug->id;
        $newSlug = Slug::findOne(['id' => 20]);
        $I->assertInstanceOf(Slug::class, $newSlug);
        $status = $composite->attachSlug($newSlug);
        $I->assertTrue($status);
        $attachedSlug = $composite->getSlug()->one();
        $I->assertInstanceOf(Slug::class, $attachedSlug);
        $I->assertEquals(20, $attachedSlug->id);
        $deletedSlug = Slug::findOne(['id' => $initialSlugId]);
        $I->assertNull($deletedSlug);

    }

}
