<?php
/**
 * TypeTrait.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 */

namespace blackcube\core\traits;

/**
 * Type trait
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\traits
 * @since XXX
 */
trait TypeTrait
{
    /**
     * @return string controller
     */
    public function getRoute()
    {
        return (($this->type === null) || empty($this->type->route) === true) ? null : $this->type->route;
    }
    
    /**
     * @return integer|null
     */
    public function getMinBlocs()
    {
        return (($this->type === null) || empty($this->type->minBlocs) === true) ? null : $this->type->minBlocs;
    }

    /**
     * @return integer|null
     */
    public function getMaxBlocs()
    {
        return (($this->type === null) || empty($this->type->maxBlocs) === true) ? null : $this->type->maxBlocs;
    }

}
