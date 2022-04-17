<?php
/**
 * m000000_000001_types_blocTypes.php
 *
 * PHP version 7.2+
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
 * Class m000000_000001_types_blocTypes
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000001_types_blocTypes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%types}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(190)->notNull(),
            'route' => $this->string(190)->notNull(),
            'minBlocs' => $this->integer()->defaultValue(null),
            'maxBlocs' => $this->integer()->defaultValue(null),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime()
        ]);
        $this->createIndex('types__name_idx', '{{%types}}', 'name', true);

        $this->createTable('{{%blocTypes}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(190)->notNull(),
            'template' => $this->binary(),
            'view' => $this->string(190),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime()
        ]);
        $this->createIndex('blocTypes__name_idx', '{{%blocTypes}}', 'name', true);

        $this->createTable('{{%types_blocTypes}}', [
            'typeId' => $this->bigInteger()->notNull(),
            'blocTypeId' => $this->bigInteger()->notNull(),
            'allowed' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[typeId]], [[blocTypeId]])'
        ]);
        $this->createIndex('types_blocTypes__typeId_idx', '{{%types_blocTypes}}', 'typeId', false);
        $this->createIndex('types_blocTypes__blocTypeId_idx', '{{%types_blocTypes}}', 'blocTypeId', false);
        $this->addForeignKey('types_blocTypes_typeId__types_id_fk', '{{%types_blocTypes}}', 'typeId', '{{%types}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('types_blocTypes_blocTypeId__blocTypes_id_fk', '{{%types_blocTypes}}', 'blocTypeId', '{{%blocTypes}}', 'id', 'CASCADE', 'CASCADE');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('types_blocTypes_blocTypeId__blocTypes_id_fk', '{{%types_blocTypes}}');
        $this->dropForeignKey('types_blocTypes_typeId__types_id_fk', '{{%types_blocTypes}}');
        $this->dropIndex('types_blocTypes__blocTypeId_idx', '{{%types_blocTypes}}');
        $this->dropIndex('types_blocTypes__typeId_idx', '{{%types_blocTypes}}');
        $this->dropTable('{{%types_blocTypes}}');

        $this->dropIndex('blocTypes__name_idx', '{{%blocTypes}}');
        $this->dropTable('{{%blocTypes}}');

        $this->dropIndex('types__name_idx', '{{%types}}');
        $this->dropTable('{{%types}}');
        return true;
    }
}
