<?php
/**
 * PluginHookInterface.php
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

/**
 * Interface PluginHookInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */
interface PluginHookInterface {
    /**
     * hook should return a boolean
     */
    public const PLUGIN_HOOK_LOAD = 'pluginHookLoad';

    /**
     * hook should return a boolean
     */
    public const PLUGIN_HOOK_VALIDATE = 'pluginHookValidate';

    /**
     * hook should return a boolean
     */
    public const PLUGIN_HOOK_SAVE = 'pluginHookSave';

    /**
     * hook should return a boolean
     */
    public const PLUGIN_HOOK_DELETE = 'pluginHookDelete';

}
