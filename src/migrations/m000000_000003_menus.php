<?php
/**
 * m000000_000003_menus.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */

namespace blackcube\core\migrations;

use yii\db\Migration;

/**
 * Class m000000_000003_menus
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000003_menus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%menus}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(255)->unique()->notNull(),
            'languageId' => $this->string(6)->notNull(),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime()
        ]);
        $this->createIndex('menus__languageId_idx', '{{%menus}}', 'languageId', false);
        $this->addForeignKey('menus_languageId__languages_id_fk', '{{%menus}}', 'languageId', '{{%languages}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%menus_items}}', [
            'id' => $this->bigPrimaryKey(),
            'menuId' => $this->bigInteger(),
            'parentId' => $this->bigInteger()->defaultValue(null),
            'name' => $this->string(255)->unique()->notNull(),
            'route' => $this->string(255)->notNull(),
            'queryString' => $this->string(255)->defaultValue(null),
            'order' => $this->integer(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
        ]);
        $this->createIndex('items__menuId_idx', '{{%menus_items}}', 'menuId', false);
        $this->createIndex('items__parentId_idx', '{{%menus_items}}', 'parentId', false);
        $this->addForeignKey('items__menuId__menus_id_fk', '{{%menus_items}}', 'menuId', '{{%menus}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('items__parentId__items_id_fk', '{{%menus_items}}', 'parentId', '{{%menus_items}}', 'id', 'CASCADE', 'CASCADE');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('items__parentId__items_id_fk', '{{%menus_items}}');
        $this->dropForeignKey('items__menuId__menus_id_fk', '{{%menus_items}}');
        $this->dropIndex('items__parentId_idx', '{{%menus_items}}');
        $this->dropIndex('items__menuId_idx', '{{%menus_items}}');

        $this->dropTable('{{%menus_items}}');

        $this->dropForeignKey('menus_languageId__languages_id_fk', '{{%menus}}');
        $this->dropIndex('menus__languageId_idx', '{{%menus}}');

        $this->dropTable('{{%menus}}');

        return true;
    }
}
