<?php
/**
 * ResumableUploadAction.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2017 Ibitux
 * @license http://www.ibitux.com/license license
 * @version 1.3.1
 * @link http://www.ibitux.com
 * @package application\actions
 */

namespace blackcube\core\actions;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction;
use Yii;

/**
 * resumable action
 *
 * @author Philippe Gaultier <pgaultier@ibitux.com>
 * @copyright 2010-2017 Ibitux
 * @license http://www.ibitux.com/license license
 * @version 1.3.1
 * @link http://www.ibitux.com
 * @package application\actions
 * @since 1.3.0
 */
class ResumableUploadAction extends ViewAction
{
    public $uploadAlias = '@app/runtime/blackcube/uploads';

    public $fileId = 'file';

    protected $extension;
    protected $originalFilename;
    protected $finalPath;

    protected $uploadComplete = false;
    protected $finalFilename;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->getIsUpload() === true) {
            $this->originalFilename = $this->getResumableParam('filename');
            $this->finalFilename = $this->originalFilename;
            $this->extension = $this->extractExtension($this->originalFilename);
            $this->handleChunk();
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = ['finalFilename' => $this->finalFilename];
            return Yii::$app->response;
        } elseif(Yii::$app->request->isGet === true) {
            // perform resume test
            throw new NotFoundHttpException();
        }
    }

    protected function handleChunk()
    {
        $identifier = $this->getResumableParam('identifier');
        $chunkNumber = $this->getResumableParam('chunkNumber');
        $chunkSize = $this->getResumableParam('chunkSize');
        $totalSize = $this->getResumableParam('totalSize');
        if ($this->getIsChunkUploaded($identifier, $this->originalFilename, $chunkNumber) === false) {
            $chunkFile = $this->getTmpChunkFile($identifier, $this->originalFilename, $chunkNumber);
            if(move_uploaded_file($_FILES[$this->fileId]['tmp_name'], $chunkFile)) {

            } else {
                throw new ServerErrorHttpException();
            }
        }
        if ($this->getIsUploadComplete($this->originalFilename, $identifier, $chunkSize, $totalSize)) {
            $this->uploadComplete = true;
            $this->createFileAndDeleteTmp($identifier, $this->originalFilename);
        }
    }

    /**
     * @return boolean
     * @since XXX
     */
    protected function getIsUpload()
    {
        return (isset($_FILES) === true && empty($_FILES) === false);
    }

    protected function getIsChunkUploaded($identifier, $filename, $chunkNumber)
    {
        $filePath = $this->getTmpChunkFile($identifier, $filename, $chunkNumber);
        return file_exists($filePath);
    }

    protected function getIsUploadComplete($filename, $identifier, $chunkSize, $totalSize)
    {
        if ($chunkSize <= 0) {
            return false;
        }
        $numOfChunks = ((int)($totalSize / $chunkSize)) + ($totalSize % $chunkSize == 0 ? 0 : 1);
        for ($i = 1; $i < $numOfChunks; $i++) {
            if ($this->getIsChunkUploaded($identifier, $filename, $i) === false) {
                return false;
            }
        }
        return true;
    }

    protected function getTmpChunkFile($identifier, $filename, $chunkNumber)
    {
        return $this->getTmpChunkDir($identifier) . DIRECTORY_SEPARATOR . $this->getTmpChunkname($filename, $chunkNumber);
    }

    protected function getTmpChunkDir($identifier)
    {
        $identifier = preg_replace('/[^a-z0-9_\-.]+/i', '_', $identifier);
        $identifier = self::cleanUpFilename($identifier);

        $tmpChunkDir = Yii::getAlias($this->uploadAlias.'/'.$identifier);
        if (file_exists($tmpChunkDir) === false) {
            mkdir($tmpChunkDir, 0777, true);
        }
        return $tmpChunkDir;
    }

    protected function getTmpChunkname($filename, $chunkNumber)
    {
        return $filename . '.part' . $chunkNumber;
    }

    /**
     * Create the final file from chunks
     */
    protected function createFileAndDeleteTmp($identifier, $filename)
    {
        $tmpFolder = $this->getTmpChunkDir($identifier);
        $chunkFiles = scandir($tmpFolder);
        $chunkFiles = array_diff($chunkFiles, ['.', '..']);
        $chunkFiles = array_map(function($file) use($tmpFolder) {
            return $tmpFolder.DIRECTORY_SEPARATOR.$file;
        }, $chunkFiles);
        // if the user has set a custom filename
        $finalFilename = self::cleanUpFilename($filename);
        $this->finalFilename = $finalFilename;

        // replace filename reference by the final file
        $filepath = Yii::getAlias($this->uploadAlias.'/'.$finalFilename);
        $this->finalPath = $filepath;
        if($this->createFileFromChunks($chunkFiles, $filepath) === true) {
            // delete folder
            $this->deleteDirectory($tmpFolder);
        }
    }

    public static function cleanUpFilename($filename)
    {
        return preg_replace('/[^a-z0-9_\-.]+/i', '_', $filename);
    }

    protected function deleteDirectory($directory)
    {
        $dir = opendir($directory);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file !== '.' ) && ( $file !== '..' )) {
                $full = $directory . DIRECTORY_SEPARATOR . $file;
                if ( is_dir($full) ) {
                    $this->deleteDirectory($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($directory);
    }

    protected function createFileFromChunks($chunkFiles, $destFile)
    {
        natsort($chunkFiles);
        $destHandle = fopen($destFile, 'w');
        foreach($chunkFiles as $chunkFile) {
            $sourceHandle = fopen($chunkFile, 'r');
            stream_copy_to_stream($sourceHandle, $destHandle);
            fclose($sourceHandle);
        }
        fclose($destHandle);
        return file_exists($destFile);
    }

    /**
     * Get resumable parameter
     * @param string $name resumable short name
     * @return string|null
     * @since XXX
     */
    protected function getResumableParam($name)
    {
        $paramName = 'resumable' . ucfirst($name);
        return Yii::$app->request->getBodyParam($paramName, null);
    }

    /**
     * Extract extension from filename
     * @param string $filename
     * @return string
     * @since XXX
     */
    protected function extractExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }
}
