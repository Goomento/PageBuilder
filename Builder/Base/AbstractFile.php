<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\AssetsHelper;
use Goomento\PageBuilder\Helper\ConfigHelper;

abstract class AbstractFile extends AbstractEntity
{
    const UPLOADS_DIR = 'media/goomento/';

    const DEFAULT_FILES_DIR = 'css/';

    const META_KEY = '';

    private $files_dir;

    private $file_name;

    const NAME = 'file';

    const TYPE = 'file';

    /**
     * File path.
     *
     * Holds the file path.
     *
     *
     * @var string
     */
    private $path;

    /**
     * Content.
     *
     * Holds the file content.
     *
     *
     * @var string
     */
    private $content;

    /**
     * @return string
     */
    public static function getBaseUploadsDir()
    {
        return self::UPLOADS_DIR;
    }

    /**
     * @return mixed
     */
    public static function getBaseUploadsUrl()
    {
        return AssetsHelper::pathToUrl(self::UPLOADS_DIR);
    }


    public function __construct($fileName)
    {
        /**
         * SagoTheme File Name
         *
         * Filters the File name
         *
         *
         * @param string   $fileName
         * @param object $this The file instance, which inherits Goomento\PageBuilder\Core\Files
         */
        $fileName = HooksHelper::applyFilters('pagebuilder/files/file_name', $fileName, $this);

        $this->setFileName($fileName);

        $this->setFilesDir(static::DEFAULT_FILES_DIR);

        $this->setPath();
    }

    /**
     * @param $files_dir
     * @return void
     */
    public function setFilesDir($files_dir)
    {
        $this->files_dir = $files_dir;
    }

    /**
     * @param $file_name
     * @return void
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return self::getBaseUploadsUrl() . $this->files_dir . $this->file_name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if ($this->content === null) {
            $this->content = $this->parseContent();
        }

        return $this->content;
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->updateFile();

        $meta = $this->getMeta();

        $meta['css_updated_time'] = time();

        $this->updateMeta($meta);
    }

    /**
     * @return void
     */
    public function updateFile()
    {
        $this->content = $this->parseContent();

        if ($this->content) {
            $this->write();
        } else {
            $this->delete();
        }
    }

    /**
     * Write resource
     */
    public function write()
    {
        AssetsHelper::save($this->path . ltrim($this->file_name), $this->content);
    }


    /**
     * Delete resource
     */
    public function delete()
    {
        AssetsHelper::delete($this->path . ltrim($this->file_name, '\\/'));

        $this->deleteMeta();
    }

    /**
     * Get meta data.
     *
     * Retrieve the CSS file meta data. Returns an array of all the data, or if
     * custom property is given it will return the property value, or `null` if
     * the property does not exist.
     *
     *
     * @param string $property Optional. Custom meta data property. Default is
     *                         null.
     *
     * @return array|null An array of all the data, or if custom property is
     *                    given it will return the property value, or `null` if
     *                    the property does not exist.
     */
    public function getMeta($property = null)
    {
        $default_meta = $this->getDefaultMeta();
        $meta = array_merge($default_meta, (array) $this->loadMeta());

        if ($property) {
            return $meta[$property] ?? null;
        }

        return $meta;
    }

    /**
     * @abstract
     */
    abstract protected function parseContent();

    /**
     * Load meta.
     *
     * Retrieve the file meta data.
     *
     */
    protected function loadMeta()
    {
        return ConfigHelper::getValue(static::META_KEY);
    }

    /**
     * Update meta.
     *
     * Update the file meta data.
     *
     *
     * @param array $meta New meta data.
     */
    protected function updateMeta($meta)
    {
        ConfigHelper::setValue(static::META_KEY, $meta);
    }

    /**
     * Delete meta.
     *
     * Delete the file meta data.
     *
     */
    protected function deleteMeta()
    {
        ConfigHelper::deleteValue(static::META_KEY);
    }

    /**
     * @return array
     */
    protected function getDefaultMeta()
    {
        return [
            'css_updated_time' => 0,
            'status'=> 'inline',
            'css'=> ''
        ];
    }


    private function setPath()
    {
        $dir_path = self::getBaseUploadsDir() . $this->files_dir;

        $this->path = $dir_path;
    }
}
