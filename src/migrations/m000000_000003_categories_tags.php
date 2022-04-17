<?php
/**
 * m000000_000003_categories_tags.php
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
 * Class m000000_000003_categories_tags
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000003_categories_tags extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%categories}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(190)->unique()->notNull(),
            'slugId' => $this->bigInteger(),
            'languageId' => $this->string(6)->notNull(),
            'typeId' => $this->bigInteger(),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime()
        ]);
        $this->createIndex('categories__languageId_idx', '{{%categories}}', 'languageId', false);
        $this->createIndex('categories__typeId_idx', '{{%categories}}', 'typeId', false);
        $this->addForeignKey('categories_languageId__languages_id_fk', '{{%categories}}', 'languageId', '{{%languages}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('categories_typeId__languages_id_fk', '{{%categories}}', 'typeId', '{{%types}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('categories__slugId_idx', '{{%categories}}', 'slugId', true);
        $this->addForeignKey('categories_slugId__slugs_id_fk', '{{%categories}}', 'slugId', '{{%slugs}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%categories_blocs}}', [
            'categoryId' => $this->bigInteger(),
            'blocId' => $this->bigInteger(),
            'order' => $this->integer(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[categoryId]], [[blocId]])'
        ]);
        $this->createIndex('categories_blocs__categoryId_idx', '{{%categories_blocs}}', 'categoryId', false);
        $this->createIndex('categories_blocs__blocId_idx', '{{%categories_blocs}}', 'blocId', false);
        $this->addForeignKey('categories_blocs_categoryId__categories_id_fk', '{{%categories_blocs}}', 'categoryId', '{{%categories}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('categories_blocs_blocId__blocs_id_fk', '{{%categories_blocs}}', 'blocId', '{{%blocs}}', 'id', 'CASCADE', 'CASCADE');


        $this->createTable('{{%tags}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(190)->notNull(),
            'slugId' => $this->bigInteger(),
            'categoryId' => $this->bigInteger()->notNull(),
            'typeId' => $this->bigInteger(),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'CONSTRAINT [[tags__name_categoryId_key]] UNIQUE ([[name]], [[categoryId]])'
        ]);

        $this->createIndex('tags__categoryId_idx', '{{%tags}}', 'categoryId', false);
        $this->createIndex('tags__typeId_idx', '{{%tags}}', 'typeId', false);
        $this->addForeignKey('tags_categoryId__categories_id_fk', '{{%tags}}', 'categoryId', '{{%categories}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('tags_typeId__types_id_fk', '{{%tags}}', 'typeId', '{{%types}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('tags__slugId_idx', '{{%tags}}', 'slugId', true);
        $this->addForeignKey('tags_slugId__slugs_id_fk', '{{%tags}}', 'slugId', '{{%slugs}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%tags_blocs}}', [
            'tagId' => $this->bigInteger(),
            'blocId' => $this->bigInteger(),
            'order' => $this->integer(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[tagId]], [[blocId]])'
        ]);
        $this->createIndex('tags_blocs__tagId_idx', '{{%tags_blocs}}', 'tagId', false);
        $this->createIndex('tags_blocs__blocId_idx', '{{%tags_blocs}}', 'blocId', false);
        $this->addForeignKey('tags_blocs_tagId__tags_id_fk', '{{%tags_blocs}}', 'tagId', '{{%tags}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('tags_blocs_blocId__blocs_id_fk', '{{%tags_blocs}}', 'blocId', '{{%blocs}}', 'id', 'CASCADE', 'CASCADE');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('tags_blocs_blocId__blocs_id_fk', '{{%tags_blocs}}');
        $this->dropForeignKey('tags_blocs_tagId__tags_id_fk', '{{%tags_blocs}}');
        $this->dropIndex('tags_blocs__blocId_idx', '{{%tags_blocs}}');
        $this->dropIndex('tags_blocs__tagId_idx', '{{%tags_blocs}}');
        $this->dropTable('{{%tags_blocs}}');

        $this->dropForeignKey('tags_typeId__types_id_fk', '{{%tags}}');
        $this->dropForeignKey('tags_categoryId__categories_id_fk', '{{%tags}}');
        $this->dropForeignKey('tags_slugId__slugs_id_fk', '{{%tags}}');
        $this->dropIndex('tags__typeId_idx', '{{%tags}}');
        $this->dropIndex('tags__categoryId_idx', '{{%tags}}');
        $this->dropIndex('tags__slugId_idx', '{{%tags}}');
        $this->dropTable('{{%tags}}');

        $this->dropForeignKey('categories_blocs_blocId__blocs_id_fk', '{{%categories_blocs}}');
        $this->dropForeignKey('categories_blocs_categoryId__categories_id_fk', '{{%categories_blocs}}');
        $this->dropIndex('categories_blocs__blocId_idx', '{{%categories_blocs}}');
        $this->dropIndex('categories_blocs__categoryId_idx', '{{%categories_blocs}}');
        $this->dropTable('{{%categories_blocs}}');

        $this->dropForeignKey('categories_typeId__languages_id_fk', '{{%categories}}');
        $this->dropForeignKey('categories_languageId__languages_id_fk', '{{%categories}}');
        $this->dropForeignKey('categories_slugId__slugs_id_fk', '{{%categories}}');
        $this->dropIndex('categories__typeId_idx', '{{%categories}}');
        $this->dropIndex('categories__languageId_idx', '{{%categories}}');
        $this->dropIndex('categories__slugId_idx', '{{%categories}}');
        $this->dropTable('{{%categories}}');
        return true;
    }
}
