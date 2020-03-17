<?php
/**
 * ElasticCest.php
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
namespace tests\elastic;

use blackcube\core\models\Elastic;
use tests\ElasticTester;
use yii\base\Model;
use yii\base\UnknownPropertyException;

/**
 * Test elastic
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Philippe Gaultier
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package tests\unit
 * @since XXX
 */
class ElasticCest extends ElasticBase
{

    public function testImport(ElasticTester $I)
    {
        $elastic = new Elastic(['schema' => $this->jsonSchemaBasic]);
        $I->assertNull($elastic->email);

        $I->assertFalse(isset($elastic->test));
        $I->expectThrowable(UnknownPropertyException::class, function() use ($elastic) {
            $elastic->test = 'test';
        });
        $status = $elastic->validate();
        $I->assertFalse($status);
        $elastic->email = 'test@test';
        $status = $elastic->validate();
        $I->assertFalse($status);
        unset($elastic->email);
        $I->assertNull($elastic->email);
        $elastic->email = 'test@test.com';
        $status = $elastic->validate();
        $I->assertTrue($status);

        $elastic->telephone = '012345678';
        $status = $elastic->validate();
        $I->assertFalse($status);
        $elastic->telephone = '0123456789';
        $status = $elastic->validate();
        $I->assertTrue($status);

        $data = $elastic->toArray();
        $I->assertArrayHasKey('telephone', $data);
        $I->assertArrayHasKey('email', $data);
        $I->assertEquals('0123456789', $data['telephone']);
        $I->assertEquals('test@test.com', $data['email']);
    }

    public function testLoad(ElasticTester $I)
    {
        $elastic = new Elastic(['schema' => $this->jsonSchemaBasic]);
        $I->assertNull($elastic->email);
        $I->assertNull($elastic->telephone);
        $I->assertEquals(Model::SCENARIO_DEFAULT, $elastic->scenario);

        $elastic->load(['telephone' => '0123456789', 'email' => 'test@test.com'], '');
        $I->assertEquals('0123456789', $elastic->telephone);
        $I->assertEquals('test@test.com', $elastic->email);

    }
}
