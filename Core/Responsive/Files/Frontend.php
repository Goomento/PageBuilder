<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Responsive\Files;

use Goomento\PageBuilder\Core\Files\Base;
use Goomento\PageBuilder\Core\Responsive\Responsive;
use Goomento\PageBuilder\Helper\StaticConfig;

/**
 * Class Frontend
 * @package Goomento\PageBuilder\Core\Responsive\Files
 */
class Frontend extends Base
{
    const META_KEY = 'goomento-custom-breakpoints-files';

    private $template_file;


    public function __construct($file_name, $template_file = null)
    {
        $this->template_file = $template_file;

        parent::__construct($file_name);
    }


    public function parseContent()
    {
        $breakpoints = Responsive::getBreakpoints();

        $breakpoints_keys = array_keys($breakpoints);

        $file_content = file_get_contents($this->template_file);

        return preg_replace_callback('/GOOMENTO_SCREEN_([A-Z]+)_([A-Z]+)/', function ($placeholder_data) use ($breakpoints_keys, $breakpoints) {
            $breakpoint_index = array_search(strtolower($placeholder_data[1]), $breakpoints_keys);

            $is_max_point = 'MAX' === $placeholder_data[2];

            if ($is_max_point) {
                $breakpoint_index++;
            }

            $value = $breakpoints[ $breakpoints_keys[ $breakpoint_index ] ];

            if ($is_max_point) {
                $value--;
            }

            return $value . 'px';
        }, $file_content);
    }

    /**
     * Load meta.
     *
     * Retrieve the file meta data.
     *
     */
    protected function loadMeta()
    {
        $option = $this->loadMetaOption();

        $file_meta_key = $this->getFileMetaKey();

        if (empty($option[ $file_meta_key ])) {
            return [];
        }

        return $option[ $file_meta_key ];
    }


    private function getFileMetaKey()
    {
        return pathinfo($this->getFileName(), PATHINFO_FILENAME);
    }


    private function loadMetaOption()
    {
        $option = StaticConfig::getOption(static::META_KEY);

        if (! $option) {
            $option = [];
        }

        return $option;
    }
}
