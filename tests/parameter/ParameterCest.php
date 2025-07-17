<?php
/**
 * ParameterCest.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\parameter
 */
namespace tests\parameter;

use blackcube\core\models\Parameter;
use tests\ParameterTester;
use yii\base\Model;
use yii\base\UnknownPropertyException;

/**
 * Test parameter
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\parameter
 */
class ParameterCest extends ParameterBase
{

    public function testInsert(ParameterTester $I)
    {
        $parameter = new Parameter();
        $parameter->domain = 'TEST';
        $parameter->name = 'NAME';
        $parameter->value = 'parameter name';
        $I->assertTrue($parameter->validate());
        $I->assertTrue($parameter->save());
        $savedParameter = Parameter::findOne(['domain' => 'TEST', 'name' => 'NAME']);
        $I->assertInstanceOf(Parameter::class, $savedParameter);
        $I->assertEquals($parameter->value, $savedParameter->value);

        $erroneousParameter = new Parameter();
        $erroneousParameter->domain = 'TEST';
        $erroneousParameter->name = 'NAME';
        $erroneousParameter->value = 'new value';
        $I->assertFalse($erroneousParameter->validate());
        $I->assertFalse($erroneousParameter->save());
        $savedParameter = Parameter::findOne(['domain' => 'TEST', 'name' => 'NAME']);
        $I->assertInstanceOf(Parameter::class, $savedParameter);
        $I->assertEquals($parameter->domain, $erroneousParameter->domain);
        $I->assertEquals($parameter->name, $erroneousParameter->name);
        $I->assertNotEquals($parameter->value, $erroneousParameter->value);
    }

    public function testFind(ParameterTester $I)
    {
        $parameter = new Parameter();
        $parameter->domain = 'TEST';
        $parameter->name = 'NAME';
        $parameter->value = 'parameter name';
        $I->assertTrue($parameter->save());

        $realParameter = Parameter::findOne(['domain' => 'TEST', 'name' => 'NAME']);
        $I->assertInstanceOf(Parameter::class, $realParameter);

        $voidParameter = Parameter::findOne(['domain' => 'UNK', 'name' => 'UNK']);
        $I->assertNull($voidParameter);
    }

    public function testDelete(ParameterTester $I)
    {
        $parameter = new Parameter();
        $parameter->domain = 'TEST';
        $parameter->name = 'NAME';
        $parameter->value = 'parameter name';
        $I->assertTrue($parameter->save());

        $dbParameter = Parameter::findOne(['domain' => 'TEST', 'name' => 'NAME']);
        $I->assertInstanceOf(Parameter::class, $dbParameter);
        $dbParameter->delete();

        $voidParameter = Parameter::findOne(['domain' => 'TEST', 'name' => 'NAME']);
        $I->assertNull($voidParameter);
    }

    public function testSearch(ParameterTester $I)
    {
        $parameters = Parameter::find()->all();
        $I->assertCount(count($this->parameterList), $parameters);

        foreach($this->parameterList as $config) {
            $parameter = Parameter::find()->where(['domain' => $config['domain'], 'name' => $config['name']])->one();
            $I->assertInstanceOf(Parameter::class, $parameter);
            $I->assertEquals($config['value'], $parameter->value);
        }
    }

}
