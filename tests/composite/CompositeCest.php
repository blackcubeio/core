<?php
/**
 * CompositeCest.php
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
use blackcube\core\models\Slug;
use blackcube\core\models\Type;
use tests\CompositeTester;
use yii\base\Model;
use yii\base\UnknownPropertyException;

/**
 * Test composite
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class CompositeCest extends CompositeBase
{

    public function testInsert(CompositeTester $I)
    {
        $composite = new Composite();
        $composite->name = 'test composite';
        $composite->languageId = 'fr';
        $composite->typeId = 6;
        $composite->slugId = 22;
        $composite->active = true;
        $I->assertTrue($composite->validate());
        $I->assertTrue($composite->save());

        $savedComposite = Composite::findOne(['id' => $composite->id]);
        $I->assertInstanceOf(Composite::class, $savedComposite);
        $I->assertEquals($composite->name, $savedComposite->name);
        $I->assertEquals($composite->languageId, $savedComposite->languageId);
        $I->assertEquals($composite->typeId, $savedComposite->typeId);
        $I->assertEquals($composite->active, $savedComposite->active);
        $I->assertEquals($composite->slugId, $savedComposite->slugId);

        $erroneousComposite = new Composite();
        $erroneousComposite->name = 'test composite';
        $erroneousComposite->languageId = 'fr';
        $erroneousComposite->typeId = 6;
        $erroneousComposite->slugId = 22;
        $erroneousComposite->active = true;
        $I->assertFalse($erroneousComposite->validate());
        $I->assertFalse($erroneousComposite->save());
    }

    public function testFind(CompositeTester $I)
    {
        $composite = new Composite();
        $composite->name = 'test composite';
        $composite->languageId = 'fr';
        $composite->typeId = 6;
        $composite->slugId = 22;
        $composite->active = true;
        $I->assertTrue($composite->save());

        $realComposite = Composite::findOne(['id' => $composite->id]);
        $I->assertInstanceOf(Composite::class, $realComposite);

        $activeComposite = Composite::find()->where(['id' => 1])->active()->one();
        $I->assertInstanceOf(Composite::class, $activeComposite);

        $voidComposite = Composite::findOne(['id' => 999]);
        $I->assertNull($voidComposite);

        $inactiveComposite = Composite::find()->where(['id' => 3])->active()->one();
        $I->assertNull($inactiveComposite);
    }

    public function testDelete(CompositeTester $I)
    {
        $composite = new Composite();
        $composite->name = 'test composite';
        $composite->languageId = 'fr';
        $composite->typeId = 6;
        $composite->slugId = 22;
        $composite->active = true;
        $I->assertTrue($composite->save());

        $insertedId = $composite->id;
        $dbComposite = Composite::findOne(['id' => $insertedId]);
        $I->assertInstanceOf(Composite::class, $dbComposite);
        $dbComposite->delete();

        $voidComposite = Composite::findOne(['id' => $insertedId]);
        $I->assertNull($voidComposite);
    }

    public function testSearch(CompositeTester $I)
    {
        $composites = Composite::find()->all();
        $I->assertCount(count($this->compositeList), $composites);

        foreach($this->compositeList as $id => $config) {
            $composite = Composite::find()->where(['id' => $id])->one();
            $I->assertInstanceOf(Composite::class, $composite);
            $I->assertEquals($config['name'], $composite->name);
            $I->assertEquals($config['active'], $composite->active);
            $I->assertEquals($config['typeId'], $composite->typeId);
            if (isset($config['slugId'])) {
                $I->assertEquals($config['slugId'], $composite->slugId);
            }
        }

        $composites = Composite::find()->where(['slugId' => 6])->all();
        $I->assertCount(1, $composites);
        $composites = Composite::find()->where(['slugId' => 999])->all();
        $I->assertCount(0, $composites);
    }

    public function testSlug(CompositeTester $I)
    {
        $slug = Slug::find()->where(['id' => 5])->active()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $composite = $slug->element;
        $I->assertInstanceOf(Composite::class, $composite);

        $slug = Slug::find()->where(['id' => 21])->active()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $composite = $slug->getElement()->active()->one();
        $I->assertNull($composite);
    }

    public function testControllerAction(CompositeTester $I)
    {
        $slug = Slug::find()->where(['id' => 5])->active()->one();
        $I->assertInstanceOf(Slug::class, $slug);
        $composite = $slug->element;
        $I->assertInstanceOf(Composite::class, $composite);

        $type = $composite->type;
        $I->assertInstanceOf(Type::class, $type);
        $I->assertEquals('composite-test', $type->name);
    }

}
