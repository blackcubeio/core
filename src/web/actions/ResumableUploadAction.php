<?php
/**
 * ResumableUploadAction.php
 *
 * PHP Version 8.2+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */ 

namespace blackcube\core\web\actions;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction;
use Yii;

/**
 * resumable action
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright 2010-2025 Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ResumableUploadAction extends ViewAction
{
    /**
     * @var string
     */
    public $uploadAlias = '@app/runtime/blackcube/uploads';

    /**
     * @var string
     */
    public $fileId = 'file';

    /**
     * @var string
     */
    protected $extension = null;

    /**
     * @var string
     */
    protected $originalFilename = null;

    /**
     * @var string
     */
    protected $finalPath = null;

    /**
     * @var bool
     */
    protected $uploadComplete = false;

    /**
     * @var string
     */
    protected $finalFilename = null;

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

    /**
     * @throws ServerErrorHttpException
     */
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

    /**
     * @param string $identifier
     * @param string $filename
     * @param string $chunkNumber
     * @return bool
     */
    protected function getIsChunkUploaded($identifier, $filename, $chunkNumber)
    {
        $filePath = $this->getTmpChunkFile($identifier, $filename, $chunkNumber);
        return file_exists($filePath);
    }

    /**
     * @param string $filename
     * @param string $identifier
     * @param integer $chunkSize
     * @param integer $totalSize
     * @return bool
     */
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

    /**
     * @param string $identifier
     * @param string $filename
     * @param integer $chunkNumber
     * @return string
     */
    protected function getTmpChunkFile($identifier, $filename, $chunkNumber)
    {
        return $this->getTmpChunkDir($identifier) . DIRECTORY_SEPARATOR . $this->getTmpChunkname($filename, $chunkNumber);
    }

    /**
     * @param string $identifier
     * @return bool|string
     */
    protected function getTmpChunkDir($identifier)
    {
        if ($identifier !== null) {
            $identifier = preg_replace('/[^a-z0-9_\-.]+/i', '_', $identifier);
        }

        $identifier = self::cleanUpFilename($identifier);

        $tmpChunkDir = Yii::getAlias($this->uploadAlias.'/'.$identifier);
        if (file_exists($tmpChunkDir) === false) {
            mkdir($tmpChunkDir, 0777, true);
        }
        return $tmpChunkDir;
    }

    /**
     * @param string $filename
     * @param integer $chunkNumber
     * @return string
     */
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

    /**
     * @param string $filename
     * @return string
     */
    public static function cleanUpFilename($filename)
    {
        if ($filename !== null) {
            return preg_replace('/[^a-z0-9_\-.]+/i', '_', $filename);
        } else {
            return $filename;
        }

    }

    /**
     * @param string $directory
     */
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

    /**
     * @param array $chunkFiles
     * @param string $destFile
     * @return bool
     */
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
