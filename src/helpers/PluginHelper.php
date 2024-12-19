<?php
/**
 * PluginHelper.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\helpers;

use yii\base\InvalidArgumentException;

/**
 * Class PluginHelper
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /
class PluginHelper {

    /**
     * @param string|array $configuration
     * @return string
     */
    public static function generateId($configuration)
    {
        if (is_string($configuration) === true) {
            $pluginId = md5($configuration);
        } elseif (is_array($configuration) && isset($configuration['id'])) {
            return $configuration['id'];
        } elseif (is_array($configuration) && isset($configuration['class'])) {
            $pluginId = md5($configuration['class']);
        } elseif (is_array($configuration) && isset($configuration['__class'])) {
            $pluginId = md5($configuration['__class']);
        } else {
            throw new InvalidArgumentException();
        }
        return $pluginId;
    }
}
