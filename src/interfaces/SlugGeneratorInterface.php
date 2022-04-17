<?php
/**
 * SlugGeneratorInterface.php
 *
 * PHP version 7.2+
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

/**
 * Interface SlugGeneratorInterface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */
interface SlugGeneratorInterface {
    /**
     * @param string $elementName
     * @param string|null $parentElementType
     * @param integer|null $parentElementId
     * @return string
     */
    public function getElementSlug($elementName, $parentElementType = null, $parentElementId = null);
}
