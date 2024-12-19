<?php
/**
 * ListBlock.php
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
 * Class ListBlock
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class ListBlock extends EditorJsBlock
{
    /**
     * @var string
     */
    public $tag = 'ul';

    /**
     * @var string
     */
    public $itemTag = 'li';

    /**
     * @var array
     */
    protected $items = [];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if (isset($this->data['items']) === true) {
            $this->items = $this->data['items'];
        }
        if (isset($this->data['style']) && $this->data['style'] !== 'unordered') {
            $this->tag = 'ol';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($options = [])
    {
        $html = '';
        if (count($this->items) > 0) {
            $listOptions = $options;
            unset($listOptions['itemOptions']);
            $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
            $html .= Html::beginTag($this->tag, $listOptions);
            foreach($this->items as $item) {
                $html .= Html::tag($this->itemTag, $item, $itemOptions);
            }
            $html .= Html::endTag($this->tag);
        }
        return $html;
    }

}
