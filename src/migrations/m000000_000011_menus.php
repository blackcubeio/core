<?php
/**
 * m000000_000011_menus.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */

namespace blackcube\core\migrations;

use yii\db\Migration;

/**
 * Class m000000_000011_menus
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
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
