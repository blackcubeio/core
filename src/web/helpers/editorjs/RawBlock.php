<?php
/**
 * RawBlock.php
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

use blackcube\core\web\helpers\Html;

/**
 * Class RawBlock
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
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
