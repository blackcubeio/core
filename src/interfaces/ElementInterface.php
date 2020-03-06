<?php
/**
 * ElementInterface.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2019 Redcat
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
 * @copyright 2010-2019 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
 *
 * @property \blackcube\core\models\Bloc[] $blocs
 * @property \blackcube\core\models\Slug $slug
 * @property \blackcube\core\models\Type $type
 */

interface ElementInterface
{
    /**
     * @return FilterActiveQuery
     */
    public function getBlocs();

    /**
     * @return FilterActiveQuery
     */
    public function getSlug();

    /**
     * @return FilterActiveQuery
     */
    public function getType();

}
