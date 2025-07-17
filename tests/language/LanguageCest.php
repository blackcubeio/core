<?php
/**
 * LanguageCest.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
namespace tests\language;
use blackcube\core\models\Language;
use tests\LanguageTester;

/**
 * Test matrix basic functions
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 * @package tests\unit
 */
class LanguageCest extends LanguageBase
{

    public function testInsert(LanguageTester $I)
    {
        $language = new Language();
        $language->id = 'fr2';
        $status = $language->save();
        $I->assertFalse($status);

        $language->name = 'France Test2';
        $status = $language->save();
        $I->assertFalse($status);

        $language->main = true;
        $status = $language->save();
        $I->assertTrue($status);
    }

    public function testMainLanguage(LanguageTester $I)
    {
        $language = Language::findOne(['id' => 'fr']);
        $I->assertInstanceOf(Language::class, $language);
        //PATCH Mysql / PostgreSQL
        $isMain = $language->main === 1 || $language->main === true;
        $I->assertTrue($isMain);
        $mainLanguage = $language->mainLanguage;
        $I->assertInstanceOf(Language::class, $mainLanguage);
        $I->assertEquals($language->id, $mainLanguage->id);

        $language = Language::findOne(['id' => 'fr-FR']);
        $I->assertInstanceOf(Language::class, $language);
        //PATCH Mysql / PostgreSQL
        $isMain = $language->main === 1 || $language->main === true;
        $I->assertFalse($isMain);
        $mainLanguage = $language->mainLanguage;
        $I->assertInstanceOf(Language::class, $mainLanguage);
        $I->assertEquals('fr', $mainLanguage->id);
        //PATCH Mysql / PostgreSQL
        $isMain = $mainLanguage->main === 1 || $mainLanguage->main === true;
        $I->assertTrue($isMain);
    }

}
