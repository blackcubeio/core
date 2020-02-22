<?php
/**
 * m000000_000000_administrators.php
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

use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m000000_000000_administrators
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000000_administrators extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%administrators}}', [
            'id' => $this->bigPrimaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'password' => $this->string(),
            'active' => $this->boolean()->defaultValue(false)->notNull(),
            'authKey' => $this->string(),
            'token' => $this->string(),
            'tokenType' => $this->string(),
            'dateCreate' => $this->dateTime()->notNull(),
            'dateUpdate' => $this->dateTime(),
        ]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%administrators}}');
        return true;
    }
}
