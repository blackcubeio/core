<?php
/**
 * ElementInterface.php
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
use yii\db\ActiveQuery;

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
 * @property \blackcube\core\models\Bloc[] $blocs
 * @property \blackcube\core\models\Slug $slug
 * @property \blackcube\core\models\Type $type
 * @property boolean $isActive
 */

interface ElementInterface
{
    /**
     * @return string
     * @since XXX
     */
    public static function getElementType() :string;

    /**
     * @return FilterActiveQuery
     * @since XXX
     */
    public function getBlocs() :ActiveQuery;

    /**
     * @return FilterActiveQuery
     * @since XXX
     */
    public function getSlug() :ActiveQuery;

    /**
     * @return FilterActiveQuery
     * @since XXX
     */
    public function getType() :ActiveQuery;

    /**
     * @return boolean
     * @since XXX
     */
    public function getIsActive() :bool;

}
