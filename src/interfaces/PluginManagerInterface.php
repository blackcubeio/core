<?php
/**
 * PluginManagerInterface.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
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
 * @copyright 2010-2020 Redcat
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
    public function __construct($id);

    /**
     * @return string plugin id
     */
    public function getId();

    /**
     * @return string plugin name
     */
    public function getName();

    /**
     * Define alias for plugin
     * @return void
     */
    public function setAlias();

    /**
     * @return string Semver version
     */
    public function getVersion();

    /**
     * @return bool check if plugin is compatible with current core version
     */
    public function getIsCompatible();

    /**
     * @return bool check if plugin is installed
     */
    public function getIsRegistered();

    /**
     * @return bool register and install plugin
     */
    public function register();

    /**
     * @return bool upgrade plugin to new version
     */
    public function upgrade();

    /**
     * @return bool unregister and uninstall plugin
     */
    public function unregister();

    /**
     * @return bool activate plugin
     */
    public function activate();

    /**
     * @return bool deactivate plugin
     */
    public function deactivate();

    /**
     * @return bool check if plugin is active
     */
    public function getIsActive();

    /**
     * @param string $hook
     * @param ElementInterface|null $element
     * @param array $additionalParameters
     * @return array widget configuration
     */
    public function hookWidget($hook, ElementInterface $element = null, $additionalParameters = []);

    /**
     * @param string $hook hook name
     * @param ElementInterface $element
     * @param array $additionalParams additional parameters passed at runtime
     * @return mixed
     */
    public function hook($hook, ElementInterface $element = null, $additionalParams = []);
}
