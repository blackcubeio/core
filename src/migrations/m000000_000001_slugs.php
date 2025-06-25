<?php
/**
 * m000000_000001_slugs.php
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
 * Class m000000_000001_slugs
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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
            'host' => $this->string(190)->defaultValue(null),
            'path' => $this->string(190)->defaultValue(null),
            'targetUrl' => $this->string(190)->defaultValue(null),
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
