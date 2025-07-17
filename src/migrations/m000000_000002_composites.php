<?php
/**
 * m000000_000002_composites.php
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
 * Class m000000_000002_composites
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
class m000000_000002_composites extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%composites}}', [
            'id' => $this->bigPrimaryKey(),
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
        $this->createIndex('composites__languageId_idx', '{{%composites}}', 'languageId', false);
        $this->addForeignKey('composites_languageId__languages_id_fk', '{{%composites}}', 'languageId', '{{%languages}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('composites__typeId_idx', '{{%composites}}', 'typeId', false);
        $this->addForeignKey('composites_typeId__types_id_fk', '{{%composites}}', 'typeId', '{{%types}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('composites__slugId_idx', '{{%composites}}', 'slugId', true);
        $this->addForeignKey('composites_slugId__slugs_id_fk', '{{%composites}}', 'slugId', '{{%slugs}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%composites_blocs}}', [
            'compositeId' => $this->bigInteger(),
            'blocId' => $this->bigInteger(),
            'order' => $this->integer(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
            'PRIMARY KEY([[compositeId]], [[blocId]])'
        ]);
        $this->createIndex('composites_blocs__compositeId_idx', '{{%composites_blocs}}', 'compositeId', false);
        $this->createIndex('composites_blocs__blocId_idx', '{{%composites_blocs}}', 'blocId', false);
        $this->addForeignKey('composites_blocs_compositeId__composites_id_fk', '{{%composites_blocs}}', 'compositeId', '{{%composites}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('composites_blocs_blocId__blocs_id_fk', '{{%composites_blocs}}', 'blocId', '{{%blocs}}', 'id', 'CASCADE', 'CASCADE');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('composites_blocs_blocId__blocs_id_fk', '{{%composites_blocs}}');
        $this->dropForeignKey('composites_blocs_compositeId__composites_id_fk', '{{%composites_blocs}}');
        $this->dropIndex('composites_blocs__blocId_idx', '{{%composites_blocs}}');
        $this->dropIndex('composites_blocs__compositeId_idx', '{{%composites_blocs}}');
        $this->dropTable('{{%composites_blocs}}');

        $this->dropForeignKey('composites_slugId__slugs_id_fk', '{{%composites}}');
        $this->dropIndex('composites__slugId_idx', '{{%composites}}');
        $this->dropForeignKey('composites_typeId__types_id_fk', '{{%composites}}');
        $this->dropIndex('composites__typeId_idx', '{{%composites}}');
        $this->dropForeignKey('composites_languageId__languages_id_fk', '{{%composites}}');
        $this->dropIndex('composites__languageId_idx', '{{%composites}}');
        $this->dropTable('{{%composites}}');
        return true;
    }
}
