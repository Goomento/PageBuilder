<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Files;

use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticAssets;
use Goomento\PageBuilder\Helper\StaticConfig;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;

/**
 * Class Base
 * @package Goomento\PageBuilder\Core\Files
 */
abstract class Base
{
    const UPLOADS_DIR = 'media/goomento/';

    const DEFAULT_FILES_DIR = 'css/';

    const META_KEY = '';

    private $files_dir;

    private $file_name;

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


    public static function getBaseUploadsDir()
    {
        return self::UPLOADS_DIR;
    }


    public static function getBaseUploadsUrl()
    {
        return StaticUrlBuilder::getUrl(self::UPLOADS_DIR);
    }


    public function __construct($file_name)
    {
        /**
         * SagoTheme File Name
         *
         * Filters the File name
         *
         *
         * @param string   $file_name
         * @param object $this The file instance, which inherits Goomento\PageBuilder\Core\Files
         */
        $file_name = Hooks::applyFilters('pagebuilder/files/file_name', $file_name, $this);

        $this->setFileName($file_name);

        $this->setFilesDir(static::DEFAULT_FILES_DIR);

        $this->setPath();
    }


    public function setFilesDir($files_dir)
    {
        $this->files_dir = $files_dir;
    }


    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }


    public function getFileName()
    {
        return $this->file_name;
    }


    public function getUrl()
    {
        return self::getBaseUploadsUrl() . $this->files_dir . $this->file_name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        if (! $this->content) {
            $this->content = $this->parseContent();
        }

        return $this->content;
    }


    public function update()
    {
        $this->updateFile();

        $meta = $this->getMeta();

        $meta['css_updated_time'] = time();

        $this->updateMeta($meta);
    }


    public function updateFile()
    {
        $this->content = $this->parseContent();

        if ($this->content) {
            $this->write();
        } else {
            $this->delete();
        }
    }


    public function write()
    {
        StaticAssets::save($this->path . ltrim($this->file_name), $this->content);
    }


    public function delete()
    {
        StaticAssets::delete($this->path . ltrim($this->file_name, '\\/'));

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
        return StaticConfig::getOption(static::META_KEY);
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
        StaticConfig::setOption(static::META_KEY, $meta);
    }

    /**
     * Delete meta.
     *
     * Delete the file meta data.
     *
     */
    protected function deleteMeta()
    {
        StaticConfig::delOption(static::META_KEY);
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
