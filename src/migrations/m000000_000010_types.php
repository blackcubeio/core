<?php
/**
 * m000000_000010_types.php
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
 * Class m000000_000010_types
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
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
