<?php
/**
 * LanguageCest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 */
namespace tests\language;
use blackcube\core\models\Language;
use tests\LanguageTester;

/**
 * Test matrix basic functions
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2016 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
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
