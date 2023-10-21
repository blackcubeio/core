<?php
/**
 * m000000_000010_types.php
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
 * Class m000000_000010_types
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\migrations
 */
class m000000_000010_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%types}}', 'nodeAllowed', $this->boolean()->defaultValue(true)->notNull()->after('route'));
        $this->addColumn('{{%types}}', 'compositeAllowed', $this->boolean()->defaultValue(true)->notNull()->after('route'));
        $this->addColumn('{{%types}}', 'categoryAllowed', $this->boolean()->defaultValue(true)->notNull()->after('route'));
        $this->addColumn('{{%types}}', 'tagAllowed', $this->boolean()->defaultValue(true)->notNull()->after('route'));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%types}}', 'tagAllowed');
        $this->dropColumn('{{%types}}', 'categoryAllowed');
        $this->dropColumn('{{%types}}', 'compositeAllowed');
        $this->dropColumn('{{%types}}', 'nodeAllowed');
        return true;
    }
}
