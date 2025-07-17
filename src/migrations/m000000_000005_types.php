<?php
/**
 * m000000_000005_types.php
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
 * Class m000000_000005_types
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 
class m000000_000005_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%types}}', 'route', $this->string(190)->defaultValue(null));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%types}}', 'route', $this->string(190)->notNull());
        return true;
    }
}
