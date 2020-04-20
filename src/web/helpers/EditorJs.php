<?php
/**
 * EditorJsBlock.php
 *
 * PHP version 7.2+
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 */

namespace blackcube\core\web\helpers;

use blackcube\core\Module;
use blackcube\core\web\helpers\editorjs\DelimiterBlock;
use blackcube\core\web\helpers\editorjs\EmbedBlock;
use blackcube\core\web\helpers\editorjs\HeaderBlock;
use blackcube\core\web\helpers\editorjs\ListBlock;
use blackcube\core\web\helpers\editorjs\ParagraphBlock;
use blackcube\core\web\helpers\editorjs\QuoteBlock;
use blackcube\core\web\helpers\editorjs\RawBlock;
use phpDocumentor\Reflection\Types\Self_;
use yii\base\Component;
use yii\helpers\Json;
use Yii;

/**
 * Class EditorJs
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 * @since XXX
 */
class EditorJs extends Component
{
    public const BLOCK_PARAGRAPH = 'paragraph';
    public const BLOCK_LIST = 'list';
    public const BLOCK_HEADER = 'header';
    public const BLOCK_DELIMITER = 'delimiter';
    public const BLOCK_RAW = 'raw';
    public const BLOCK_QUOTE = 'quote';
    public const BLOCK_EMBED = 'embed';

    public $renderers = [
        self::BLOCK_PARAGRAPH => ParagraphBlock::class,
        self::BLOCK_LIST => ListBlock::class,
        self::BLOCK_HEADER => HeaderBlock::class,
        self::BLOCK_QUOTE => QuoteBlock::class,
        self::BLOCK_RAW => RawBlock::class,
        self::BLOCK_DELIMITER => DelimiterBlock::class,
        self::BLOCK_EMBED => EmbedBlock::class,
    ];

    /**
     * @var string|array
     */
    public $editorJsConfig; // ['time' => 111, 'blocks' => [], 'version' => 'xxx]

    public $options = [];

    protected $blocks = [];

    protected $editorTime = 0;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();
        if (is_string($this->editorJsConfig) === true) {
            $this->editorJsConfig = Json::decode($this->editorJsConfig);
        }
        if (isset($this->editorJsConfig['blocks']) === true && is_array($this->editorJsConfig['blocks'])) {
            $this->blocks = $this->editorJsConfig['blocks'];
        }
        if (isset($this->editorJsConfig['time']) === true) {
            $this->editorTime = $this->editorJsConfig['time'];
        }
    }

    /**
     * Render editor JS Content
     * @return string
     */
    public function render()
    {
        $output = '';
        foreach($this->blocks as $i => $block) {
            try {
                $blockType = $block['type'];
                if (isset($this->renderers[$blockType]) === true) {
                    $config = [
                        'class' => $this->renderers[$blockType],
                        'data' => isset($block['data']) ? $block['data'] : [],
                        'index' => $i,
                    ];
                    $renderer = Yii::createObject($config);
                    /* @var $renderer \blackcube\core\web\helpers\editorjs\EditorJsBlock */
                    if ($renderer !== null) {
                        $blockOptions = isset($this->options[$blockType]) ? $this->options[$blockType] : [];
                        $output .= $renderer->render($blockOptions);
                    }
                }
            } catch (\Exception $e) {
                Yii::warning(Module::t('helpers', 'Unable to render block'));
            }
        }
        return $output;
    }

}
