<?php
/**
 * Html.php
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
use creocoder\flysystem\Filesystem;
use Imagine\Image\ManipulatorInterface;
use yii\base\Model;
use yii\helpers\Html as YiiHtml;
use yii\helpers\Json;
use yii\imagine\Image;
use DateTime;
use Yii;

/**
 * Html helpers to handle Blackcube CMS fields
 *
 * @author Philippe Gaultier <pgaultier@redcat.io>
 * @copyright 2010-2020 Redcat
 * @license https://www.redcat.io/license license
 * @version XXX
 * @link https://www.redcat.io
 * @package blackcube\core\web\helpers
 * @since XXX
 */
class Html extends YiiHtml
{
    public const EDITORJS_BLOCK_PARAGRAPH = 'paragraph';
    public const EDITORJS_BLOCK_LIST = 'list';
    public const EDITORJS_BLOCK_HEADER = 'header';
    public const EDITORJS_BLOCK_DELIMITER = 'delimiter';
    public const EDITORJS_BLOCK_RAW = 'raw';
    public const EDITORJS_BLOCK_QUOTE = 'quote';
    public const EDITORJS_BLOCK_EMBED = 'embed';

    public static $editorJsRenderers = [
        self::EDITORJS_BLOCK_PARAGRAPH => ParagraphBlock::class,
        self::EDITORJS_BLOCK_LIST => ListBlock::class,
        self::EDITORJS_BLOCK_HEADER => HeaderBlock::class,
        self::EDITORJS_BLOCK_QUOTE => QuoteBlock::class,
        self::EDITORJS_BLOCK_RAW => RawBlock::class,
        self::EDITORJS_BLOCK_DELIMITER => DelimiterBlock::class,
        self::EDITORJS_BLOCK_EMBED => EmbedBlock::class,
    ];

    /**
     * Render editor JS Content
     * @param string|array $editorJsData
     * @param array $options
     * @return string
     */
    public static function editorJs($editorJsData, $options = [])
    {
        if (is_string($editorJsData) === true) {
            $editorJsData = Json::decode($editorJsData);
        }
        $blocks = [];
        if (isset($editorJsData['blocks']) === true && is_array($editorJsData['blocks'])) {
            $blocks = $editorJsData['blocks'];
        }
        $editorTime = 0;
        if (isset($editorJsData['time']) === true) {
            $editorTime = $editorJsData['time'];
        }
        $output = '';
        foreach($blocks as $i => $block) {
            try {
                $blockType = $block['type'];
                if (isset(static::$editorJsRenderers[$blockType]) === true) {
                    $config = [
                        'class' => static::$editorJsRenderers[$blockType],
                        'data' => isset($block['data']) ? $block['data'] : [],
                        'index' => $i,
                    ];
                    $renderer = Yii::createObject($config);
                    /* @var $renderer \blackcube\core\web\helpers\editorjs\EditorJsBlock */
                    if ($renderer !== null) {
                        $blockOptions = isset($options[$blockType]) ? $options[$blockType] : [];
                        $output .= $renderer->render($blockOptions);
                    }
                }
            } catch (\Exception $e) {
                Yii::warning(Module::t('helpers', 'Unable to render block'));
            }
        }
        return $output;
    }

    /**
     * Extend img tag to handle fs saved files
     * @param array|string $src
     * @param array $options
     * @return string
     */
    public static function img($src, $options = [])
    {
        if (is_string($src) === true) {
            if (isset($options['width'], $options['height']) === true) {
                $src = static::cacheImage($src, $options['width'], $options['height']);
            } else {
                $src = static::cacheImage($src);
            }
        }
        return parent::img($src, $options);
    }

    /**
     * Generate an image in cache
     * @param string $imageLink
     * @param integer|null $width
     * @param integer|null $height
     * @return string
     */
    public static function cacheImage($imageLink, $width = null, $height = null)
    {
        $prefix = trim(Module::getInstance()->uploadFsPrefix, '/') . '/';
        $fileCachePathAlias = trim(Module::getInstance()->fileCachePathAlias, '/') . '/';
        $fileCacheUrlAlias = trim(Module::getInstance()->fileCacheUrlAlias, '/') . '/';
        $resultFileUrl = $imageLink;
        if (strncmp($prefix, $imageLink, strlen($prefix)) === 0) {
            $fs = Module::getInstance()->fs;
            /* @var $fs Filesystem */
            $originalFilename = str_replace($prefix, '', $imageLink);
            if ($fs->has($originalFilename) === true) {
                $fileData = pathinfo($originalFilename);
                $targetFilename = $fileData['dirname'].'/'.$fileData['filename'].'.'.$fileData['extension'];
                if ($width !== null && $height !== null) {
                    $targetFilename = $fileData['dirname'].'/'.$fileData['filename'].'-'.$width.'-'.$height.'.'.$fileData['extension'];
                }
                $originalFileTimestamp = $fs->getTimestamp($originalFilename);
                $cachedFilePath = Yii::getAlias($fileCachePathAlias.$targetFilename);
                $cachedFileUrl = Yii::getAlias($fileCacheUrlAlias.$targetFilename);
                if (file_exists($cachedFilePath) === false || filemtime($cachedFilePath) < $originalFileTimestamp) {
                    $targetCachePath = pathinfo($cachedFilePath, PATHINFO_DIRNAME);
                    if (is_dir($targetCachePath) === false) {
                        mkdir($targetCachePath, 0777, true);
                    }
                    if ($width !== null && $height !== null) {
                        $sourceStream = $fs->readStream($originalFilename);
                        $image = Image::thumbnail($sourceStream, $width, $height, ManipulatorInterface::THUMBNAIL_OUTBOUND);
                        $image->save($cachedFilePath);
                        fclose($sourceStream);
                    } else {
                        $sourceStream = $fs->readStream($originalFilename);
                        $targetStream = fopen($cachedFilePath, 'w');
                        stream_copy_to_stream($sourceStream, $targetStream);
                        fclose($sourceStream);
                        fclose($targetStream);
                    }
                }
                $resultFileUrl = $cachedFileUrl;
            }

        }
        return $resultFileUrl;
    }

    /**
     * Generate cached file
     * @param string $fileLink
     * @return string
     */
    public static function cacheFile($fileLink)
    {
        $prefix = trim(Module::getInstance()->uploadFsPrefix, '/') . '/';
        $fileCachePathAlias = trim(Module::getInstance()->fileCachePathAlias, '/') . '/';
        $fileCacheUrlAlias = trim(Module::getInstance()->fileCacheUrlAlias, '/') . '/';
        $resultFileUrl = $fileLink;
        if (strncmp($prefix, $fileLink, strlen($prefix)) === 0) {
            $fs = Module::getInstance()->fs;
            /* @var $fs Filesystem */
            $originalFilename = str_replace($prefix, '', $fileLink);
            if ($fs->has($originalFilename) === true) {
                $fileData = pathinfo($originalFilename);
                $targetFilename = $fileData['dirname'].'/'.$fileData['filename'].'.'.$fileData['extension'];
                $originalFileTimestamp = $fs->getTimestamp($originalFilename);
                $cachedFilePath = Yii::getAlias($fileCachePathAlias.$targetFilename);
                $cachedFileUrl = Yii::getAlias($fileCacheUrlAlias.$targetFilename);
                if (file_exists($cachedFilePath) === false || filemtime($cachedFilePath) < $originalFileTimestamp) {
                    $targetCachePath = pathinfo($cachedFilePath, PATHINFO_DIRNAME);
                    if (is_dir($targetCachePath) === false) {
                        mkdir($targetCachePath, 0777, true);
                    }
                    $sourceStream = $fs->readStream($originalFilename);
                    $targetStream = fopen($cachedFilePath, 'w');
                    stream_copy_to_stream($sourceStream, $targetStream);
                    fclose($sourceStream);
                    fclose($targetStream);
                }
                $resultFileUrl = $cachedFileUrl;
            }

        }
        return $resultFileUrl;

    }
    /**
     * Generate input[type=datetime-local] field
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public static function activeDateTimeInput($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        if (isset($options['value'])) {
            $value = $options['value'];
        } else {
            $currentValue = static::getAttributeValue($model, $attribute);
            if ($currentValue instanceof DateTime) {
                $value = $currentValue->format('Y-m-d\TH:i:s');
            } elseif($currentValue !==null) {
                $currentDate = new DateTime($currentValue);
                $value = $currentDate->format('Y-m-d\TH:i:s');;
            } else {
                $value;
            }
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        static::setActivePlaceholder($model, $attribute, $options);

        return static::input('datetime-local', $name, $value, $options);
    }

    /**
     * Generate input[type=date] field
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public static function activeDateInput($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        if (isset($options['value'])) {
            $value = $options['value'];
        } else {
            $currentValue = static::getAttributeValue($model, $attribute);
            if ($currentValue instanceof DateTime) {
                $value = $currentValue->format('Y-m-d');
            } elseif ($currentValue !== null) {
                $currentDate = new DateTime($currentValue);
                $value = $currentDate->format('Y-m-d');
            } else {
                $value = null;
            }
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        static::setActivePlaceholder($model, $attribute, $options);
        // self::normalizeMaxLength($model, $attribute, $options);

        return static::input('date', $name, $value, $options);
    }

}
