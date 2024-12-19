<?php
/**
 * TaggableInterface.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\interfaces;

use blackcube\core\models\Tag;

/**
 * Taggable interface
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 *
 */

interface TaggableInterface
{
    /**
     * Attach a tag to the element
     * @param Tag $tag
     * @return bool
     */
    public function attachTag(Tag $tag);

    /**
     * Detach the tag from the element but do not delete it
     * @param Tag $tag
     * @return bool
     */
    public function detachTag(Tag $tag);

}
