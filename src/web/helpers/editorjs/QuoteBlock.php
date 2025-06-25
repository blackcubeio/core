<?php
/**
 * QuoteBlock.php
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
 * Class QuoteBlock
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
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
