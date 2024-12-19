<?php
/**
 * TypeTrait.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\traits;

/**
 * Type trait
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
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
