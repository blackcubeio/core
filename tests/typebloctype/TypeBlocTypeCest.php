<?php
namespace tests\typebloctype;
use blackcube\core\models\TypeBlocType;
use tests\TypebloctypeTester;

class TypeBlocTypeCest extends TypeBlocTypeBase
{
    // tests
    public function testInsert(TypebloctypeTester $I)
    {
        $typeBlocType = new TypeBlocType();
        $typeBlocType->typeId = 5;
        $typeBlocType->blocTypeId = 1;
        $typeBlocType->allowed = false;
        $I->assertTrue($typeBlocType->validate());
        $I->assertTrue($typeBlocType->save());

        $typeBlocType = new TypeBlocType();
        $typeBlocType->typeId = 5;
        $typeBlocType->blocTypeId = 1;
        $typeBlocType->allowed = true;
        $I->assertFalse($typeBlocType->validate());
        $I->assertFalse($typeBlocType->save());
    }

    public function testLoad(TypebloctypeTester $I)
    {
        $typeBlocType = TypeBlocType::find()->where(['typeId' => 1, 'blocTypeId' => 3])->one();
        $I->assertNull($typeBlocType);
        $typeBlocType = TypeBlocType::find()->where(['typeId' => 1, 'blocTypeId' => 1])->one();
        $I->assertInstanceOf(TypeBlocType::class, $typeBlocType);
    }

    public function testDelete(TypebloctypeTester $I)
    {
        $typeBlocType = new TypeBlocType();
        $typeBlocType->typeId = 5;
        $typeBlocType->blocTypeId = 1;
        $typeBlocType->allowed = false;
        $I->assertTrue($typeBlocType->save());

        $dbTypeBlocType = TypeBlocType::findOne(['typeId' => 5, 'blocTypeId' => 1]);
        $I->assertInstanceOf(TypeBlocType::class, $dbTypeBlocType);
        $dbTypeBlocType->delete();

        $voidTypeBlocType = TypeBlocType::findOne(['typeId' => 5, 'blocTypeId' => 1]);
        $I->assertNull($voidTypeBlocType);

    }

    public function testSearch(TypebloctypeTester $I)
    {
        $typeBlocTypes = TypeBlocType::find()->all();
        $I->assertCount(count($this->typeBlocTypeList), $typeBlocTypes);
        foreach($this->typeBlocTypeList as $config) {
            $typeBlocType = TypeBlocType::find()->where(['typeId' => $config['typeId'], 'blocTypeId' => $config['blocTypeId']])->one();
            $I->assertInstanceOf(TypeBlocType::class, $typeBlocType);
            $I->assertEquals($config['allowed'], $typeBlocType->allowed);
        }
        $typeBlocTypes = TypeBlocType::find()->where(['blocTypeId' => 1])->all();
        $I->assertCount(4, $typeBlocTypes);
        $typeBlocTypes = TypeBlocType::find()->where(['typeId' => 1])->all();
        $I->assertCount(2, $typeBlocTypes);
        $typeBlocTypes = TypeBlocType::find()->where(['blocTypeId' => 999])->all();
        $I->assertCount(0, $typeBlocTypes);
        $typeBlocTypes = TypeBlocType::find()->where(['typeId' => 999])->all();
        $I->assertCount(0, $typeBlocTypes);

    }
}
