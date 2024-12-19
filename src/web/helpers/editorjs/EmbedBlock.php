<?php
/**
 * EmbedBlock.php
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
 * Class EmbedBlock
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Blackcube
 * @license https://www.blackcube.io/license license
 * @version XXX
 * @link https://www.blackcube.io
 * @since XXX
 */
class EmbedBlock extends EditorJsBlock
{
    /**
     * @var string
     */
    public $tag = 'iframe';

    /**
     * @var string
     */
    public $captionTag = 'div';

    /**
     * @var string
     */
    protected $caption;

    /**
     * @var integer
     */
    protected $width;

    /**
     * @var integer
     */
    protected $height;

    /**
     * @var string
     */
    protected $src;

    /**
     * @var string
     */
    protected $allow = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';



    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if (isset($this->data['caption']) === true) {
            $this->caption = $this->data['caption'];
        }
        if (isset($this->data['embed']) === true) {
            $this->src = $this->data['embed'];
        }
        if (isset($this->data['width']) === true) {
            $this->width = $this->data['width'];
        }
        if (isset($this->data['height']) === true) {
            $this->height = $this->data['height'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($options = [])
    {
        $html = '';
        if ($this->src !== null) {
            $mainOptions = $options;
            unset($mainOptions['captionOptions']);
            $mainOptions['width'] = $this->width;
            $mainOptions['height'] = $this->height;
            $mainOptions['frameborder'] = 0;
            $mainOptions['src'] = $this->src;
            $mainOptions['allow'] = $this->allow;
            $mainOptions['allowfullscreen'] = '';

            $html .= Html::tag($this->tag, '', $mainOptions);
            if ($this->caption !== null) {
                $captionOptions = isset($options['captionOptions']) ? $options['captionOptions'] : [];
                $rawText = $this->filterRaw($this->caption);
                $html .= Html::tag($this->captionTag, $rawText, $captionOptions);
            }
        }
        return $html;
    }

}
