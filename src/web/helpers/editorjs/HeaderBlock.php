<?php
/**
 * HeaderBlock.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\web\helpers\editorjs;

use blackcube\core\web\helpers\Html;

/**
 * Class HeaderBlock
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class HeaderBlock extends EditorJsBlock
{
    /**
     * @var string
     */
    public $tag = 'h1';

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $baseTag = 'h';
    /**
     * @var integer
     */
    protected $level;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if (isset($this->data['text']) === true) {
            $this->text = $this->data['text'];
        }
        if (isset($this->data['level']) === true) {
            $this->tag = $this->baseTag.$this->data['level'];
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
