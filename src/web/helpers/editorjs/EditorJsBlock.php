<?php
/**
 * EditorJsBlock.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * /

namespace blackcube\core\web\helpers\editorjs;

use yii\base\BaseObject;

/**
 * Class EditorJsBlock
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
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
        if($text !== null) {
            return preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si",'<$1$2>', $text);
        } else {
            return $text;
        }

    }
}
