<?php
/**
 * EditorJsBlock.php
 *
 * PHP version 7.4+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers\editorjs
 */

namespace blackcube\core\web\helpers\editorjs;

use yii\base\BaseObject;

/**
 * Class EditorJsBlock
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers\editorjs
 * @since XXX
 */
abstract class EditorJsBlock extends BaseObject
{
    /**
     * @var array Block data
     */
    public $data;

    /**
     * @var integer current block index
     */
    public $index;

    /**
     * Render Specific block
     * @param array $options
     * @return string
     */
    abstract function render($options = []);

    /**
     * Remove all tags attributes
     * @param string $text
     * @return string
     */
    protected function filterRaw($text)
    {
        return preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si",'<$1$2>', $text);
    }
}
