<?php
/**
 * SluggedInterface.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 */

namespace blackcube\core\interfaces;

use blackcube\core\models\FilterActiveQuery;

/**
 * Element interface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 * @since XXX
 *
 * @property \blackcube\core\models\Slug $slug
 */

interface SluggedInterface
{

    /**
     * @return FilterActiveQuery
     * @since XXX
     */
    public function getSlug();

}
