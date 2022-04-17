<?php
/**
 * RawBlock.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers\editorjs
 */

namespace blackcube\core\web\helpers\editorjs;

use blackcube\core\web\helpers\Html;

/**
 * Class RawBlock
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers\editorjs
 * @since XXX
 */
class RawBlock extends EditorJsBlock
{

    /**
     * @var string
     */
    protected $raw;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if (isset($this->data['html']) === true) {
            $this->raw = $this->data['html'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($options = [])
    {
        $html = '';
        if ($this->raw !== null) {
            $html .= $this->raw;
        }
        return $html;
    }

}
