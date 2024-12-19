<?php
/**
 * m000000_000000_languages.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\migrations;

use yii\db\Expression;
use yii\db\Migration;
use Yii;

/**
 * Class m000000_000000_languages
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /
class m000000_000000_languages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%languages}}', [
            'id' => $this->string(6),
            'name' => $this->string(128)->notNull(),
            'main' => $this->boolean()->notNull(),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[id]])'
        ]);
        $this->batchInsert('{{%languages}}', [
            'id',
            'main',
            'name',
            'dateCreate',
            'dateUpdate'
        ], [
            ['af', true,'Afrikaans', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['af-ZA', false,'Afrikaans (South Africa)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar', true,'Arabic', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-AE', false,'Arabic (U.A.E.)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-BH', false,'Arabic (Bahrain)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-DZ', false,'Arabic (Algeria)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-EG', false,'Arabic (Egypt)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-IQ', false,'Arabic (Iraq)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-JO', false,'Arabic (Jordan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-KW', false,'Arabic (Kuwait)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-LB', false,'Arabic (Lebanon)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-LY', false,'Arabic (Libya)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-MA', false,'Arabic (Morocco)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-OM', false,'Arabic (Oman)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-QA', false,'Arabic (Qatar)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-SA', false,'Arabic (Saudi Arabia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-SY', false,'Arabic (Syria)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-TN', false,'Arabic (Tunisia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ar-YE', false,'Arabic (Yemen)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['az', true,'Azeri', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['az-AZ', false,'Azeri (Azerbaijan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['be', true,'Belarusian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['be-BY', false,'Belarusian (Belarus)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['bg', true,'Bulgarian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['bg-BG', false,'Bulgarian (Bulgaria)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['bs-BA', false,'Bosnian (Bosnia and Herzegovina)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ca', true,'Catalan', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ca-ES', false,'Catalan (Spain)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['cs', true,'Czech', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['cs-CZ', false,'Czech (Czech Republic)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['cy', true,'Welsh', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['cy-GB', false,'Welsh (United Kingdom)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['da', true,'Danish', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['da-DK', false,'Danish (Denmark)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['de', true,'German', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['de-AT', false,'German (Austria)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['de-CH', false,'German (Switzerland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['de-DE', false,'German (Germany)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['de-LI', false,'German (Liechtenstein)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['de-LU', false,'German (Luxembourg)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['dv', true,'Divehi', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['dv-MV', false,'Divehi (Maldives)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['el', true,'Greek', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['el-GR', false,'Greek (Greece)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en', true,'English', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-AU', false,'English (Australia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-BZ', false,'English (Belize)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-CA', false,'English (Canada)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-CB', false,'English (Caribbean)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-GB', false,'English (United Kingdom)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-IE', false,'English (Ireland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-JM', false,'English (Jamaica)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-NZ', false,'English (New Zealand)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-PH', false,'English (Republic of the Philippines)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-TT', false,'English (Trinidad and Tobago)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-US', false,'English (United States)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-ZA', false,'English (South Africa)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['en-ZW', false,'English (Zimbabwe)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['eo', true,'Esperanto', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es', true,'Spanish', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-AR', false,'Spanish (Argentina)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-BO', false,'Spanish (Bolivia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-CL', false,'Spanish (Chile)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-CO', false,'Spanish (Colombia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-CR', false,'Spanish (Costa Rica)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-DO', false,'Spanish (Dominican Republic)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-EC', false,'Spanish (Ecuador)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-ES', false,'Spanish (Spain)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-GT', false,'Spanish (Guatemala)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-HN', false,'Spanish (Honduras)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-MX', false,'Spanish (Mexico)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-NI', false,'Spanish (Nicaragua)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-PA', false,'Spanish (Panama)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-PE', false,'Spanish (Peru)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-PR', false,'Spanish (Puerto Rico)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-PY', false,'Spanish (Paraguay)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-SV', false,'Spanish (El Salvador)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-UY', false,'Spanish (Uruguay)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['es-VE', false,'Spanish (Venezuela)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['et', true,'Estonian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['et-EE', false,'Estonian (Estonia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['eu', true,'Basque', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['eu-ES', false,'Basque (Spain)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fa', true,'Farsi', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fa-IR', false,'Farsi (Iran)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fi', true,'Finnish', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fi-FI', false,'Finnish (Finland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fo', true,'Faroese', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fo-FO', false,'Faroese (Faroe Islands)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fr', true,'French', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fr-BE', false,'French (Belgium)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fr-CA', false,'French (Canada)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fr-CH', false,'French (Switzerland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fr-FR', false,'French (France)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fr-LU', false,'French (Luxembourg)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['fr-MC', false,'French (Principality of Monaco)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['gl', true,'Galician', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['gl-ES', false,'Galician (Spain)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['gu', true,'Gujarati', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['gu-IN', false,'Gujarati (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['he', true,'Hebrew', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['he-IL', false,'Hebrew (Israel)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hi', true,'Hindi', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hi-IN', false,'Hindi (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hr', true,'Croatian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hr-BA', false,'Croatian (Bosnia and Herzegovina)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hr-HR', false,'Croatian (Croatia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hu', true,'Hungarian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hu-HU', false,'Hungarian (Hungary)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hy', true,'Armenian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['hy-AM', false,'Armenian (Armenia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['id', true,'Indonesian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['id-ID', false,'Indonesian (Indonesia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['is', true,'Icelandic', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['is-IS', false,'Icelandic (Iceland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['it', true,'Italian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['it-CH', false,'Italian (Switzerland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['it-IT', false,'Italian (Italy)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ja', true,'Japanese', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ja-JP', false,'Japanese (Japan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ka', true,'Georgian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ka-GE', false,'Georgian (Georgia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['kk', true,'Kazakh', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['kk-KZ', false,'Kazakh (Kazakhstan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['kn', true,'Kannada', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['kn-IN', false,'Kannada (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ko', true,'Korean', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ko-KR', false,'Korean (Korea)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['kok', true,'Konkani', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['kok-IN', false,'Konkani (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ky', true,'Kyrgyz', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ky-KG', false,'Kyrgyz (Kyrgyzstan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['lt', true,'Lithuanian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['lt-LT', false,'Lithuanian (Lithuania)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['lv', true,'Latvian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['lv-LV', false,'Latvian (Latvia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mi', true,'Maori', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mi-NZ', false,'Maori (New Zealand)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mk', true,'FYRO Macedonian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mk-MK', false,'FYRO Macedonian (Former Yugoslav Republic of Macedonia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mn', true,'Mongolian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mn-MN', false,'Mongolian (Mongolia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mr', true,'Marathi', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mr-IN', false,'Marathi (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ms', true,'Malay', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ms-BN', false,'Malay (Brunei Darussalam)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ms-MY', false,'Malay (Malaysia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mt', true,'Maltese', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['mt-MT', false,'Maltese (Malta)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['nb', true,'Norwegian (Bokm?l)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['nb-NO', false,'Norwegian (Bokm?l) (Norway)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['nl', true,'Dutch', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['nl-BE', false,'Dutch (Belgium)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['nl-NL', false,'Dutch (Netherlands)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['nn-NO', false,'Norwegian (Nynorsk) (Norway)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ns', true,'Northern Sotho', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ns-ZA', false,'Northern Sotho (South Africa)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['pa', true,'Punjabi', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['pa-IN', false,'Punjabi (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['pl', true,'Polish', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['pl-PL', false,'Polish (Poland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ps', true,'Pashto', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ps-AR', false,'Pashto (Afghanistan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['pt', true,'Portuguese', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['pt-BR', false,'Portuguese (Brazil)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['pt-PT', false,'Portuguese (Portugal)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['qu', true,'Quechua', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['qu-BO', false,'Quechua (Bolivia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['qu-EC', false,'Quechua (Ecuador)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['qu-PE', false,'Quechua (Peru)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ro', true,'Romanian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ro-RO', false,'Romanian (Romania)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ru', true,'Russian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ru-RU', false,'Russian (Russia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sa', true,'Sanskrit', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sa-IN', false,'Sanskrit (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['se', true,'Sami (Northern)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['se-FI', false,'Sami (Finland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['se-NO', false,'Sami (Norway)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['se-SE', false,'Sami (Sweden)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sk', true,'Slovak', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sk-SK', false,'Slovak (Slovakia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sl', true,'Slovenian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sl-SI', false,'Slovenian (Slovenia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sq', true,'Albanian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sq-AL', false,'Albanian (Albania)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sr-BA', false,'Serbian (Bosnia and Herzegovina)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sr-SP', false,'Serbian (Serbia and Montenegro)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sv', true,'Swedish', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sv-FI', false,'Swedish (Finland)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sv-SE', false,'Swedish (Sweden)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sw', true,'Swahili', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['sw-KE', false,'Swahili (Kenya)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['syr', true,'Syriac', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['syr-SY', false,'Syriac (Syria)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ta', true,'Tamil', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ta-IN', false,'Tamil (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['te', true,'Telugu', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['te-IN', false,'Telugu (India)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['th', true,'Thai', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['th-TH', false,'Thai (Thailand)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tl', true,'Tagalog', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tl-PH', false,'Tagalog (Philippines)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tn', true,'Tswana', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tn-ZA', false,'Tswana (South Africa)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tr', true,'Turkish', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tr-TR', false,'Turkish (Turkey)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tt', true,'Tatar', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['tt-RU', false,'Tatar (Russia)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ts', true,'Tsonga', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['uk', true,'Ukrainian', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['uk-UA', false,'Ukrainian (Ukraine)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ur', true,'Urdu', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['ur-PK', false,'Urdu (Islamic Republic of Pakistan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['uz', true,'Uzbek', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['uz-UZ', false,'Uzbek (Uzbekistan)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['vi', true,'Vietnamese', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['vi-VN', false,'Vietnamese (Viet Nam)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['xh', true,'Xhosa', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['xh-ZA', false,'Xhosa (South Africa)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zh', true,'Chinese', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zh-CN', false,'Chinese (S)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zh-HK', false,'Chinese (Hong Kong)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zh-MO', false,'Chinese (Macau)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zh-SG', false,'Chinese (Singapore)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zh-TW', false,'Chinese (T)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zu', true,'Zulu', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
            ['zu-ZA', false,'Zulu (South Africa)', Yii::createObject(Expression::class, ['NOW()']), Yii::createObject(Expression::class, ['NOW()'])],
        ]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%languages}}');
        return true;
    }
}
