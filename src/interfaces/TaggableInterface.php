<?php
/**
 * TaggableInterface.php
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

use blackcube\core\models\Tag;

/**
 * Taggable interface
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\interfaces
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
