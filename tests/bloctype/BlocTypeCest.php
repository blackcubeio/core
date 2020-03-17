<?php namespace tests\bloctype;
use blackcube\core\models\BlocType;
use tests\BloctypeTester;

class BlocTypeCest extends BlocTypeBase
{
    public function testInsert(BloctypeTester $I)
    {
        $blocType = new BlocType();
        $blocType->template = $this->jsonSchemaBasic;
        $blocType->name = 'contact';
        $I->assertFalse($blocType->validate());
        $I->assertFalse($blocType->save());

        $blocType->name = 'contact2';

        $I->assertTrue($blocType->validate());
        $I->assertTrue($blocType->save());
    }

    public function testLoad(BloctypeTester $I)
    {
        $blocType = BlocType::find()->where(['name' => 'contact2'])->one();
        $I->assertNull($blocType);
        $blocType = BlocType::find()->where(['name' => 'contact'])->one();
        $I->assertInstanceOf(BlocType::class, $blocType);
        $I->assertEquals($this->jsonSchemaBasic, $blocType->template);
    }
}
