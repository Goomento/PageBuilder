<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticConfig;

/**
 * Class ImageSize
 * @package Goomento\PageBuilder\Builder\Controls\Groups
 */
class ImageSize extends Base
{

    /**
     * Fields.
     *
     * Holds all the image size control fields.
     *
     *
     * @var array Image size control fields.
     */
    protected static $fields;

    /**
     * Get image size control type.
     *
     * Retrieve the control type, in this case `image-size`.
     *
     *
     * @return string Control type.
     */
    public static function getType()
    {
        return 'image-size';
    }

    /**
     * Get attachment image HTML.
     *
     * Retrieve the attachment image HTML code.
     *
     * Note that some widgets use the same key for the media control that allows
     * the image selection and for the image size control that allows the user
     * to select the image size, in this case the third parameter should be null
     * or the same as the second parameter. But when the widget uses different
     * keys for the media control and the image size control, when calling this
     * method you should pass the keys.
     *
     *
     * @param array  $settings       Control settings.
     * @param string $imageSizeKey Optional. Settings key for image size.
     *                               Default is `image`.
     * @param string $imageKey      Optional. Settings key for image. Default
     *                               is null. If not defined uses image size key
     *                               as the image key.
     *
     * @return string Image HTML.
     * TODO check this
     */
    public static function getAttachmentImageHtml($settings, $imageSizeKey = 'image', $imageKey = null)
    {
        if (! $imageKey) {
            $imageKey = $imageSizeKey;
        }

        $image = $settings[ $imageKey ];

        $imageClass = ! isset($settings['hover_animation']) && empty($settings['hover_animation'])
            ? 'gmt-animation-' . $settings['hover_animation']
            : '';

        $html = '';

        $attrs = [];

        $attrs['class'] = $imageClass;
        $attrs['src'] = $image['url'];
        $attrs['title'] = '';
        $attrs['alt'] = '';

        if (isset($settings[$imageSizeKey . '_size']) && $settings[$imageSizeKey . '_size'] === 'custom') {
            $height = $settings[$imageSizeKey . '_custom_dimension']['height'] ?: false;
            $width = $settings[$imageSizeKey . '_custom_dimension']['width'] ?: false;
            if ($height) {
                $attrs['height'] = $height;
            }
            if ($width) {
                $attrs['width'] = $width;
            }
        }

        if ($attrs['src']) {
            $html .= '<img ';
            foreach ($attrs as $key => $value) {
                $html .= " {$key}=\"{$value}\"";
            }
            $html .= ' />';
        }

        /**
         * Get Attachment Image HTML
         *
         * Filters the Attachment Image HTML
         *
         * @param string $html the attachment image HTML string
         * @param array  $settings       Control settings.
         * @param string $imageSizeKey Optional. Settings key for image size.
         *                               Default is `image`.
         * @param string $imageKey      Optional. Settings key for image. Default
         *                               is null. If not defined uses image size key
         *                               as the image key.
         */
        return Hooks::applyFilters('pagebuilder/image_size/get_attachment_image_html', $html, $settings, $imageSizeKey, $imageKey);
    }

    /**
     * Get all image sizes.
     *
     * Retrieve available image sizes with data like `width`, `height` and `crop`.
     *
     *
     * @return array An array of available image sizes.
     */
    public static function getAllImageSizes()
    {
        $default_image_sizes = [ 'thumbnail', 'medium', 'medium_large', 'large' ];

        $image_sizes = [];

        foreach ($default_image_sizes as $size) {
            $image_sizes[ $size ] = [
                'width' => (int)StaticConfig::getThemeOption($size . '_size_w'),
                'height' => (int)StaticConfig::getThemeOption($size . '_size_h'),
                'crop' => (bool)StaticConfig::getThemeOption($size . '_crop'),
            ];
        }

        return Hooks::applyFilters('image_size_names_choose', $image_sizes);
    }

    /**
     * Get attachment image src.
     *
     * Retrieve the attachment image source URL.
     *
     *
     * @param string $attachment_id  The attachment ID.
     * @param string $image_size_key Settings key for image size.
     * @param array  $settings       Control settings.
     *
     * @return string Attachment image source URL.
     * @deprecated
     */
    public static function getAttachmentImageSrc($attachment_id, $image_size_key, array $settings)
    {
        if (empty($attachment_id)) {
            return false;
        }

        $size = $settings[ $image_size_key . '_size' ];

        if ('custom' !== $size) {
            $attachment_size = $size;
        }

        return ! empty($image_src[0]) ? $image_src[0] : '';
    }

    /**
     * Get child default arguments.
     *
     * Retrieve the default arguments for all the child controls for a specific group
     * control.
     *
     *
     * @return array Default arguments for all the child controls.
     */
    protected function getChildDefaultArgs()
    {
        return [
            'include' => [],
            'exclude' => [],
        ];
    }

    /**
     * Init fields.
     *
     * Initialize image size control fields.
     *
     *
     * @return array Control fields.
     */
    protected function initFields()
    {
        $fields = [];

        $fields['size'] = [
            'label' => __('Image Size'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
            'label_block' => false,
        ];

        $fields['custom_dimension'] = [
            'label' => __('Image Dimension'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::IMAGE_DIMENSIONS,
            'description' => __('You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.'),
            'condition' => [
                'size' => 'custom',
            ],
            'separator' => 'none',
        ];

        return $fields;
    }

    /**
     * Prepare fields.
     *
     * Process image size control fields before adding them to `add_control()`.
     *
     *
     * @param array $fields Image size control fields.
     *
     * @return array Processed fields.
     */
    protected function prepareFields($fields)
    {
        $image_sizes = $this->getImageSizes();

        $args = $this->getArgs();

        if (! empty($args['default']) && isset($image_sizes[ $args['default'] ])) {
            $default_value = $args['default'];
        } else {
            // Get the first item for default value.
            $default_value = array_keys($image_sizes);
            $default_value = array_shift($default_value);
        }

        $fields['size']['options'] = $image_sizes;

        $fields['size']['default'] = $default_value;

        if (! isset($image_sizes['custom'])) {
            unset($fields['custom_dimension']);
        }

        return parent::prepareFields($fields);
    }

    /**
     * Get image sizes.
     *
     * Retrieve available image sizes after filtering `include` and `exclude` arguments.
     *
     *
     * @return array Filtered image sizes.
     */
    private function getImageSizes()
    {
        $image_sizes['full'] = __('Full');
        $image_sizes['custom'] = __('Custom');

        return $image_sizes;
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the image size control. Used to return the
     * default options while initializing the image size control.
     *
     *
     * @return array Default image size control options.
     */
    protected function getDefaultOptions()
    {
        return [
            'popover' => false,
        ];
    }
}
