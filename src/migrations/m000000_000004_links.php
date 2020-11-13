<?php
/**
 * m000000_000004_links.php
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
 * Class m000000_000004_links
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000004_links extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%composites_tags}}', [
            'compositeId' => $this->bigInteger(),
            'tagId' => $this->bigInteger(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[compositeId]], [[tagId]])'
        ]);
        $this->createIndex('composites_tags__compositeId_idx', '{{%composites_tags}}', 'compositeId', false);
        $this->createIndex('composites_tags__tagId_idx', '{{%composites_tags}}', 'tagId', false);
        $this->addForeignKey('composites_tags_compositeId__composites_id_fk', '{{%composites_tags}}', 'compositeId', '{{%composites}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('composites_tags_tagId__tags_id_fk', '{{%composites_tags}}', 'tagId', '{{%tags}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%nodes_tags}}', [
            'nodeId' => $this->bigInteger(),
            'tagId' => $this->bigInteger(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[nodeId]], [[tagId]])'
        ]);
        $this->createIndex('nodes_tags__nodeId_idx', '{{%nodes_tags}}', 'nodeId', false);
        $this->createIndex('nodes_tags__tagId_idx', '{{%nodes_tags}}', 'tagId', false);
        $this->addForeignKey('nodes_tags_nodeId__nodes_id_fk', '{{%nodes_tags}}', 'nodeId', '{{%nodes}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('nodes_tags_tagId__tags_id_fk', '{{%nodes_tags}}', 'tagId', '{{%tags}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%nodes_composites}}', [
            'nodeId' => $this->bigInteger(),
            'compositeId' => $this->bigInteger(),
            'order' => $this->integer(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[nodeId]], [[compositeId]])'
        ]);
        $this->createIndex('nodes_composites__nodeId_idx', '{{%nodes_composites}}', 'nodeId', false);
        $this->createIndex('nodes_composites__compositeId_idx', '{{%nodes_composites}}', 'compositeId', false);
        $this->addForeignKey('nodes_composites_nodeId__nodes_id_fk', '{{%nodes_composites}}', 'nodeId', '{{%nodes}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('nodes_composites_compositeId__composites_id_fk', '{{%nodes_composites}}', 'compositeId', '{{%composites}}', 'id', 'CASCADE', 'CASCADE');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('nodes_composites_compositeId__composites_id_fk', '{{%nodes_composites}}');
        $this->dropForeignKey('nodes_composites_nodeId__nodes_id_fk', '{{%nodes_composites}}');
        $this->dropIndex('nodes_composites__compositeId_idx', '{{%nodes_composites}}');
        $this->dropIndex('nodes_composites__nodeId_idx', '{{%nodes_composites}}');
        $this->dropTable('{{%nodes_composites}}');


        $this->dropForeignKey('nodes_tags_tagId__tags_id_fk', '{{%nodes_tags}}');
        $this->dropForeignKey('nodes_tags_nodeId__nodes_id_fk', '{{%nodes_tags}}');
        $this->dropIndex('nodes_tags__tagId_idx', '{{%nodes_tags}}');
        $this->dropIndex('nodes_tags__nodeId_idx', '{{%nodes_tags}}');
        $this->dropTable('{{%nodes_tags}}');


        $this->dropForeignKey('composites_tags_tagId__tags_id_fk', '{{%composites_tags}}');
        $this->dropForeignKey('composites_tags_compositeId__composites_id_fk', '{{%composites_tags}}');
        $this->dropIndex('composites_tags__tagId_idx', '{{%composites_tags}}');
        $this->dropIndex('composites_tags__compositeId_idx', '{{%composites_tags}}');
        $this->dropTable('{{%composites_tags}}');
        return true;
    }
}
