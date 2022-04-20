<?php
/**
 * PluginBootstrapInterface.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */

namespace blackcube\core\interfaces;

use Yii;
use yii\base\Model;
use yii\base\Application;

/**
 * Interface PluginBootstrapInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */
interface PluginBootstrapInterface {

    /**
     * @param string $moduleUid Core module uniqueId
     * @param Application $app
     */
    public function bootstrapCore(string $moduleUid, Application $app);

}
