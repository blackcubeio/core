<?php
/**
 * m000000_000002_blocs.php
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
 * Class m000000_000002_blocs
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class m000000_000002_blocs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%blocs}}', [
            'id' => $this->bigPrimaryKey(),
            'blocTypeId' => $this->bigInteger()->notNull(),
            'active' => $this->boolean()->defaultValue(false),
            'data' => $this->binary(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime()
        ]);
        $this->createIndex('blocs__blocTypeId_idx', '{{%blocs}}', 'blocTypeId', false);
        $this->addForeignKey('blocs_blocTypeId__blocTypes_id_fk', '{{%blocs}}', 'blocTypeId', '{{%blocTypes}}', 'id', 'CASCADE', 'CASCADE');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('blocs_blocTypeId__blocTypes_id_fk', '{{%blocs}}');
        $this->dropIndex('blocs__blocTypeId_idx', '{{%blocs}}');
        $this->dropTable('{{%blocs}}');
        return true;
    }
}
