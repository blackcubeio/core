<?php namespace tests\type;
use blackcube\core\models\Type;
use tests\TypeTester;

class TypeCest extends TypeBase
{
    public function testInsert(TypeTester $I)
    {
        $type = new Type();
        $type->name = 'hometest';
        $type->route = 'home';
        $I->assertTrue($type->validate());
        $I->assertTrue($type->save());

        $dbType = Type::find()->where(['name' => 'hometest'])->one();
        $I->assertInstanceOf(Type::class, $dbType);

        $I->assertEquals('hometest', $type->name);
        $I->assertEquals('home', $type->route);
        $nullType = Type::find()->where(['name' => 'plop'])->one();
        $I->assertNull($nullType);
    }

    public function testLoad(TypeTester $I)
    {
        $typeHome = Type::findOne(['name' => 'home']);
        $I->assertInstanceOf(Type::class, $typeHome);
        $config = $this->typeList[$typeHome->id];
        $I->assertIsArray($config);
        foreach($config as $key => $value) {
            $I->assertEquals($value, $typeHome->{$key});
        }
        $countTypes = Type::find()->count();
        $I->assertEquals(count($this->typeList), $countTypes);
    }

}
