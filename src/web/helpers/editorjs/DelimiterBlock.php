<?php
/**
 * DelimiterBlock.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\web\helpers\editorjs;

use blackcube\core\web\helpers\Html;

/**
 * Class DelimiterBlock
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class DelimiterBlock extends EditorJsBlock
{
    /**
     * @var string
     */
    public $tag = 'hr';

    /**
     * {@inheritDoc}
     */
    public function render($options = [])
    {
        return Html::tag($this->tag, null, $options);
    }

}
