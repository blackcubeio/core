<?php
/**
 * ListBlock.php
 *
 * PHP version 8.0+
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
 * Class ListBlock
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2022 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers\editorjs
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
