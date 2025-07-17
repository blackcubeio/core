<?php
/**
 * m000000_000011_menus.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\migrations;

use yii\db\Migration;

/**
 * Class m000000_000011_menus
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
class m000000_000011_menus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%menus}}', 'host', $this->string()->after('name'));
        $this->dropIndex('name', '{{%menus}}');
        $this->createIndex('menus__id_host_languageId', '{{%menus}}', ['id', 'host', 'languageId'], true);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('menus__id_host_languageId', '{{%menus}}');
        $this->createIndex('name', '{{%menus}}', 'name', true);
        $this->dropColumn('{{%menus}}', 'host');
        return true;
    }
}
