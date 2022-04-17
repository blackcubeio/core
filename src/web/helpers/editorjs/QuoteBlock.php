<?php
/**
 * QuoteBlock.php
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
 * Class QuoteBlock
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers\editorjs
 * @since XXX
 */
class QuoteBlock extends EditorJsBlock
{
    /**
     * @var string
     */
    public $tag = 'div';

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $caption;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if (isset($this->data['text']) === true) {
            $this->text = $this->data['text'];
        }
        if (isset($this->data['caption']) === true) {
            $this->caption = $this->data['caption'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($options = [])
    {
        $html = '';
        if ($this->text !== null) {
            $mainOptions = $options;
            unset($mainOptions['captionOptions']);
            $captionOptions = isset($options['captionOptions']) ? $options['captionOptions'] : [];
            $rawText = $this->filterRaw($this->text);
            $html .= Html::tag($this->tag, $rawText, $mainOptions);
            if ($this->caption !== null) {
                $rawText = $this->filterRaw($this->caption);
                $html .= Html::tag($this->tag, $rawText, $captionOptions);
            }
        }
        return $html;
    }

}
