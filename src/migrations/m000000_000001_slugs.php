<?php
/**
 * m000000_000001_slugs.php
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
 * Class m000000_000001_slugs
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000001_slugs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%slugs}}', [
            'id' => $this->bigPrimaryKey(),
            'host' => $this->string(255)->defaultValue(null),
            'path' => $this->string()->defaultValue(null),
            'targetUrl' => $this->string()->defaultValue(null),
            'httpCode' => $this->integer()->defaultValue(null),
            'active' => $this->boolean()->defaultValue(true)->notNull(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime()
        ]);
        $this->createIndex('slugs__host_path_idx', '{{%slugs}}', ['host', 'path'], true);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('slugs__host_path_idx', '{{%slugs}}');
        $this->dropTable('{{%slugs}}');
        return true;
    }
}
