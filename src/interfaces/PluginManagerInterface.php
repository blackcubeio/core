<?php
/**
 * PluginManagerInterface.php
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
use yii\base\Action;

/**
 * Interface PluginManagerInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */
interface PluginManagerInterface {

    /**
     * PluginInterface constructor.
     * @param string $id
     */
    public function __construct(string $id);

    /**
     * @return string plugin id
     */
    public function getId();

    /**
     * @return string plugin name
     */
    public function getName() :string;

    /**
     * Define alias for plugin
     * @return void
     */
    public function setAlias() :void;

    /**
     * @return string Semver version
     */
    public function getVersion() :string;

    /**
     * @return bool check if plugin is compatible with current core version
     */
    public function getIsCompatible() :bool;

    /**
     * @return bool check if plugin is installed
     */
    public function getIsRegistered() :bool;

    /**
     * @return bool register and install plugin
     */
    public function register() :bool;

    /**
     * @return bool upgrade plugin to new version
     */
    public function upgrade() :bool;

    /**
     * @return bool unregister and uninstall plugin
     */
    public function unregister() :bool;

    /**
     * @return bool activate plugin
     */
    public function activate() :bool;

    /**
     * @return bool deactivate plugin
     */
    public function deactivate() :bool;

    /**
     * @return bool check if plugin is active
     */
    public function getIsActive() :bool;

    /**
     * @param string $hook
     * @param ElementInterface|null $element
     * @param array $additionalParameters
     * @return array widget configuration
     */
    public function hookWidget(string $hook, ?ElementInterface $element = null, array $additionalParameters = []);

    /**
     * @param string $hook hook name
     * @param ElementInterface $element
     * @param array $additionalParams additional parameters passed at runtime
     * @return mixed
     */
    public function hook(string $hook, ?ElementInterface $element = null, array $additionalParams = []);
}
