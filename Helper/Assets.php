<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir;
use Magento\Store\Model\Store;

class Assets extends AbstractHelper
{
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * @var CatalogImageHelper
     */
    protected $catalogImageHelper;
    /**
     * @var Dir
     */
    protected $dir;
    /**
     * @var File
     */
    protected $filesystemIo;

    /**
     * Assets constructor.
     * @param Context $context
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param CatalogImageHelper $catalogImageHelper
     * @param File $filesystemIo
     * @param Dir $dir
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        DirectoryList $directoryList,
        CatalogImageHelper $catalogImageHelper,
        File $filesystemIo,
        Dir $dir
    ) {
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->catalogImageHelper = $catalogImageHelper;
        $this->filesystemIo = $filesystemIo;
        $this->dir = $dir;
        parent::__construct($context);
    }

    /**
     * @param $path
     * @return string
     * @throws FileSystemException
     */
    protected function getAbsolutePath($path)
    {
        $path = (string) $path;
        if (!self::isAbsolutePath($path)) {
            $path = explode('/', $path);
            $directoryCode = $path[0];
            if (self::isAvailableDirectoryCode($directoryCode)) {
                $path[0] = $this->directoryList->getPath($directoryCode);
            }
            $path = implode('/', $path);
        }

        return $path;
    }

    /**
     * @param $path
     * @return bool
     */
    protected function isAbsolutePath($path)
    {
        return substr($path, 0, 1) === '/';
    }

    /**
     * @param $origin
     * @param $destination
     * @return bool
     * @throws FileSystemException
     */
    public function copy($origin, $destination)
    {
        $origin = $this->getAbsolutePath($origin);
        $paths = $this->parsePath($destination);
        if (substr($destination, -1, 1) === '/') {
            $this->mkDirIfNotExisted($paths['absolute_path']);
        } else {
            $this->mkDirIfNotExisted(dirname($paths['absolute_path']));
        }
        if (is_dir($paths['absolute_path'])) {
            $destination = rtrim($destination, '\\/') . '/' . basename($origin);
        }
        return self::xcopy($origin, $this->getAbsolutePath($destination));
    }

    /**
     * @param $source
     * @param $dest
     * @param int $permissions
     * @return bool
     */
    public static function xcopy($source, $dest, $permissions = 0755)
    {
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        if (is_file($source)) {
            return copy($source, $dest);
        }

        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            self::xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        $dir->close();
        return true;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getDefaultPlaceholderUrl($type = 'image')
    {
        return $this->catalogImageHelper->getDefaultPlaceholderUrl($type);
    }

    /**
     * @param $module
     * @param string $type
     * @return string
     */
    public function getModulePath($module, $type = '')
    {
        return $this->dir->getDir($module, $type);
    }

    /**
     * The path must indicate the file in `pub` directory
     * @param $path
     * @return string|null
     * @throws FileSystemException
     */
    public function pathToUrl($path)
    {
        $paths = $this->parsePath($path);
        if ($paths['directory_code']) {
            $uriPart = $paths['related_path'];
            if ($paths['directory_code'] === 'media') {
                return $this->_urlBuilder->getUrl($uriPart, ['_type' => 'media']);
            } else {
                return $this->_urlBuilder->getDirectUrl($uriPart, ['_type' => 'link']);
            }
        }
        return null;
    }

    /**
     * @param $folder
     * @return string
     * @throws FileSystemException
     */
    public function mkDirIfNotExisted($folder)
    {
        $folder = $this->getAbsolutePath($folder);
        if (!file_exists($folder)) {
            $this->filesystemIo->mkdir($folder);
        }
        return $folder;
    }

    /**
     * @param $fileName
     * @param $content
     * @return int
     * @throws FileSystemException
     */
    public function writeToFile($fileName, $content)
    {
        $fileName = $this->parsePath($fileName);
        if ($fileName['directory_code']) {
            $folder = $this->filesystem->getDirectoryWrite($fileName['directory_code']);
            return $folder->writeFile($fileName['related_path'], $content);
        }
        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    public function isInternalPath($path)
    {
        $absolutePath = $this->getAbsolutePath($path);
        return strpos($absolutePath, BP) !== false;
    }

    /**
     * @param $path
     * @return string[]
     * @throws FileSystemException
     */
    public function parsePath($path)
    {
        $path = (string) $path;
        $result = [
            'directory_code' => '',
            'related_path' => '',
            'absolute_path' => '',
        ];
        $absolutePath = $this->getAbsolutePath($path);
        if (self::isAbsolutePath($path) && $this->isInternalPath($path)) {
            $path = substr($absolutePath, strlen(BP . '/'));
        }
        if ($path !== $absolutePath) {
            $path = explode('/', $path);
            $directoryCode = $path[0];
            if ($directoryCode && self::isAvailableDirectoryCode($directoryCode)) {
                array_shift($path);
                $result['directory_code'] = $directoryCode;
            }
            $result['related_path'] = implode('/', $path);
        }
        $result['absolute_path'] = $absolutePath;
        return $result;
    }

    /**
     * @param $directoryCode
     * @return bool
     */
    protected static function isAvailableDirectoryCode($directoryCode)
    {
        return isset(DirectoryList::getDefaultConfig()[$directoryCode]);
    }

    /**
     * @param $fileName
     * @return bool
     * @throws FileSystemException
     */
    public function delete($fileName)
    {
        $fileName = $this->parsePath($fileName);
        if ($fileName['directory_code']) {
            $folder = $this->filesystem->getDirectoryWrite($fileName['directory_code']);
            return $folder->delete($fileName['related_path']);
        } elseif ($fileName['absolute_path'] && file_exists($fileName['absolute_path'])) {
            // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
            @unlink($fileName['absolute_path']);
            return true;
        }

        return false;
    }

    /**
     * @param $fileName
     * @param string $content
     * @return int
     * @throws FileSystemException
     */
    public function save($fileName, $content = '')
    {
        $paths = $this->parsePath($fileName);
        if ($paths['directory_code']) {
            $this->mkDirIfNotExisted(dirname($fileName));
            return $this->writeToFile($fileName, $content);
        }
        return false;
    }
}
