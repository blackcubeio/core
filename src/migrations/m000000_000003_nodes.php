<?php
/**
 * m000000_000003_nodes.php
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
 * Class m000000_000003_nodes
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000003_nodes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%nodes}}', [
            'id' => $this->bigPrimaryKey(),
            'path' => $this->string(190)->unique()->notNull(),
            'left' => $this->decimal(25, 22)->notNull(),
            'right' => $this->decimal(25, 22)->notNull(),
            'level' => $this->integer()->notNull(),
            'name' => $this->string(190),
            'slugId' => $this->bigInteger(),
            'languageId' => $this->string(6)->notNull(),
            'typeId' => $this->bigInteger(),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateStart' => $this->dateTime(),
            'dateEnd' => $this->dateTime(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime()
        ]);
        $this->createIndex('nodes__left_idx', '{{%nodes}}', 'left', false);
        $this->createIndex('nodes__right_idx', '{{%nodes}}', 'right', false);
        $this->createIndex('nodes__level_idx', '{{%nodes}}', 'level', false);
        $this->createIndex('nodes__languageId_idx', '{{%nodes}}', 'languageId', false);
        $this->createIndex('nodes__typeId_idx', '{{%nodes}}', 'typeId', false);
        $this->createIndex('nodes__slugId_idx', '{{%nodes}}', 'slugId', true);
        $this->addForeignKey('nodes_languageId__languages_id_fk', '{{%nodes}}', 'languageId', '{{%languages}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('nodes_typeId__types_id_fk', '{{%nodes}}', 'typeId', '{{%types}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('nodes_slugId__slugs_id_fk', '{{%nodes}}', 'slugId', '{{%slugs}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%nodes_blocs}}', [
            'nodeId' => $this->bigInteger(),
            'blocId' => $this->bigInteger(),
            'order' => $this->integer(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[nodeId]], [[blocId]])'
        ]);
        $this->createIndex('nodes_blocs__nodeId_idx', '{{%nodes_blocs}}', 'nodeId', false);
        $this->createIndex('nodes_blocs__blocId_idx', '{{%nodes_blocs}}', 'blocId', false);
        $this->addForeignKey('nodes_blocs_nodeId__nodes_id_fk', '{{%nodes_blocs}}', 'nodeId', '{{%nodes}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('nodes_blocs_blocId__blocs_id_fk', '{{%nodes_blocs}}', 'blocId', '{{%blocs}}', 'id', 'CASCADE', 'CASCADE');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('nodes_blocs_blocId__blocs_id_fk', '{{%nodes_blocs}}');
        $this->dropForeignKey('nodes_blocs_nodeId__nodes_id_fk', '{{%nodes_blocs}}');
        $this->dropIndex('nodes_blocs__blocId_idx', '{{%nodes_blocs}}');
        $this->dropIndex('nodes_blocs__nodeId_idx', '{{%nodes_blocs}}');
        $this->dropTable('{{%nodes_blocs}}');

        $this->dropForeignKey('nodes_slugId__slugs_id_fk', '{{%nodes}}');
        $this->dropForeignKey('nodes_typeId__types_id_fk', '{{%nodes}}');
        $this->dropForeignKey('nodes_languageId__languages_id_fk', '{{%nodes}}');
        $this->dropIndex('nodes__slugId_idx', '{{%nodes}}');
        $this->dropIndex('nodes__typeId_idx', '{{%nodes}}');
        $this->dropIndex('nodes__languageId_idx', '{{%nodes}}');
        $this->dropIndex('nodes__right_idx', '{{%nodes}}');
        $this->dropIndex('nodes__left_idx', '{{%nodes}}');
        $this->dropIndex('nodes__level_idx', '{{%nodes}}');
        $this->dropTable('{{%nodes}}');
        return true;
    }
}
