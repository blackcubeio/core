<?php
/**
 * m000000_000004_seos.php
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
 * Class m000000_000004_sitemaps
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000004_seos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%seos}}', [
            'id' => $this->bigPrimaryKey(),
            'slugId' => $this->bigInteger()->notNull(),
            'canonicalSlugId' => $this->bigInteger()->defaultValue(null),
            'title' => $this->string(255)->defaultValue(null),
            'image' => $this->string(255)->defaultValue(null),
            'description' => $this->text()->defaultValue(null),
            'noindex' => $this->boolean()->defaultValue(false),
            'nofollow' => $this->boolean()->defaultValue(false),
            'og' => $this->boolean()->defaultValue(false),
            'ogType' => $this->string(255),
            'twitter' => $this->boolean()->defaultValue(false),
            'twitterCard' => $this->string(255),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
        ]);
        $this->createIndex('seos__slugId_idx', '{{%seos}}', 'slugId', true);
        $this->addForeignKey('seos__slugId__slugs_id_fk', '{{%seos}}', 'slugId', '{{%slugs}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('seos__canonicalSlugId_idx', '{{%seos}}', 'canonicalSlugId', false);
        $this->addForeignKey('seos__canonicalSlugId__slugs_id_fk', '{{%seos}}', 'canonicalSlugId', '{{%slugs}}', 'id', 'CASCADE', 'CASCADE');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('seos__canonicalSlugId__slugs_id_fk', '{{%seos}}');
        $this->dropIndex('seos__canonicalSlugId_idx', '{{%seos}}');
        $this->dropForeignKey('seos__slugId__slugs_id_fk', '{{%seos}}');
        $this->dropIndex('seos__slugId_idx', '{{%seos}}', 'slugId');
        $this->dropTable('{{%seos}}');
        return true;
    }
}
