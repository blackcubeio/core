<?php
/**
 * SluggedInterface.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\interfaces;

use blackcube\core\models\FilterActiveQuery;

/**
 * Element interface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
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
