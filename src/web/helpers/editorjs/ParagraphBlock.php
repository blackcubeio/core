<?php
/**
 * ParagraphBlock.php
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

use blackcube\core\web\helpers\Html;

/**
 * Class ParagraphBlock
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers\editorjs
 * @since XXX
 */
class ParagraphBlock extends EditorJsBlock
{
    /**
     * @var string
     */
    public $tag = 'p';

    /**
     * @var string
     */
    protected $text;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if (isset($this->data['text']) === true) {
            $this->text = $this->data['text'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($options = [])
    {
        $html = '';
        if ($this->text !== null) {
            $rawText = $this->filterRaw($this->text);
            $html .= Html::tag($this->tag, $rawText, $options);
        }
        return $html;
    }

}
