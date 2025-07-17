<?php
/**
 * PluginHookInterface.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\interfaces;

use Yii;
use yii\base\Model;

/**
 * Interface PluginHookInterface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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
