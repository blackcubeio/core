<?php
/**
 * BlocCest.php
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
namespace tests\bloc;

use blackcube\core\models\Bloc;
use tests\BlocTester;
use yii\base\Model;
use yii\base\UnknownPropertyException;

/**
 * Test bloc
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class BlocCest extends BlocBase
{

    public function testInsert(BlocTester $I)
    {
        $bloc = new Bloc();
        $bloc->blocTypeId = 2;
        $bloc->text = 'sample text';
        $bloc->save();
        $I->assertTrue($bloc->validate());
        $I->assertTrue($bloc->save());

        $savedBloc = Bloc::findOne(['id' => $bloc->id]);
        $I->assertInstanceOf(Bloc::class, $savedBloc);
        $I->assertEquals($bloc->blocTypeId, $savedBloc->blocTypeId);
        $I->assertEquals($bloc->text, $savedBloc->text);
        $I->assertEquals($bloc->data, $savedBloc->data);
    }

    public function testDelete(BlocTester $I)
    {
        $bloc = new Bloc();
        $bloc->blocTypeId = 2;
        $bloc->text = 'sample text';
        $bloc->save();
        $I->assertTrue($bloc->save());

        $insertedId = $bloc->id;
        $dbBloc = Bloc::findOne(['id' => $insertedId]);
        $I->assertInstanceOf(Bloc::class, $dbBloc);
        $dbBloc->delete();

        $voidBloc = Bloc::findOne(['id' => $insertedId]);
        $I->assertNull($voidBloc);
    }

    public function testElastic(BlocTester $I)
    {
        $bloc = Bloc::findOne(['id' => 1]);
        $I->assertInstanceOf(Bloc::class, $bloc);
        $I->assertContains('text', $bloc->attributes());
        $I->assertTrue($bloc->hasAttribute('text'));
        $I->assertEquals('bloc 1', $bloc->text);

        $bloc = new Bloc();
        $bloc->blocTypeId = 2;
        $I->assertTrue($bloc->hasAttribute('text'));
        $bloc->text = 'b';
        $I->assertFalse($bloc->validate());
        $I->assertArrayHasKey('text', $bloc->errors);
        $bloc->text = 'sample bloc';
        $I->assertTrue($bloc->validate());
        $I->assertArrayNotHasKey('text', $bloc->errors);
        $I->assertTrue($bloc->save());

        $insertedId = $bloc->id;
        $dbBloc = Bloc::findOne(['id' => $bloc->id]);
        $I->assertInstanceOf(Bloc::class, $dbBloc);
        $I->assertEquals($bloc->text, $dbBloc->text);
        $I->assertEquals($bloc->title, $dbBloc->title);

        $bloc->title = 'new title';
        $I->assertTrue($bloc->save());

        $dbBloc->refresh();
        $I->assertEquals($bloc->text, $dbBloc->text);
        $I->assertEquals($bloc->title, $dbBloc->title);


    }

}
