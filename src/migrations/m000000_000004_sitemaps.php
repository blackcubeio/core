<?php
/**
 * m000000_000004_sitemaps.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\migrations;

use yii\db\Migration;

/**
 * Class m000000_000004_sitemaps
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 
class m000000_000004_sitemaps extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sitemaps}}', [
            'id' => $this->bigPrimaryKey(),
            'slugId' => $this->bigInteger()->notNull(),
            'frequency' => $this->string(64)->defaultValue('daily'),
            'priority' => $this->float()->defaultValue(0.5),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
        ]);
        $this->createIndex('sitemaps__slugId_idx', '{{%sitemaps}}', 'slugId', true);
        $this->addForeignKey('sitemaps__slugId__slugs_id_fk', '{{%sitemaps}}', 'slugId', '{{%slugs}}', 'id', 'CASCADE', 'CASCADE');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sitemaps__slugId__slugs_id_fk', '{{%sitemaps}}');
        $this->dropIndex('sitemaps__slugId_idx', '{{%sitemaps}}');
        $this->dropTable('{{%sitemaps}}');

        return true;
    }
}
