<?php
/**
 * ElasticInterface.php
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

/**
 * Elastic interface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 * @since XXX
 *
 * @property array $structure
 */

interface ElasticInterface
{
    /**
     * @return array
     * @since XXX
     */
    public function getStructure() :array;

}
