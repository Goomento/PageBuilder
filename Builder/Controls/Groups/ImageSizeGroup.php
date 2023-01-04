<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;
use Goomento\PageBuilder\Helper\HooksHelper;

class ImageSizeGroup extends AbstractControlGroup
{

    const NAME = 'image-size';

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
     * @param array $settings Control settings.
     * @param string $imageKey Optional. Settings key for image. Default
     *                               is null. If not defined uses image size key
     *                               as the image key.
     *
     * @return string Image HTML.
     */
    public static function getAttachmentImageHtml(
        array $settings,
        string $imageKey = 'image',
        string $sizeKey = null
    ) {
        if (null === $sizeKey) {
            $sizeKey = $imageKey;
        }

        $image = $settings[ $imageKey ];

        $imageClass = !empty($settings['hover_animation'])
            ? 'gmt-animation-' . $settings['hover_animation']
            : '';

        $html = '';

        $attrs = [];

        if (!empty($settings[$imageKey . '_classes'])) {
            $imageClass .= ' ' . $settings[$imageKey . '_classes'];
        }

        if (!empty($settings[$imageKey . '_hover_animation'])) {
            $imageClass .= ' gmt-animation-' . $settings[$imageKey . '_hover_animation'];
        }

        $attrs['class'] = $imageClass;
        $attrs['src'] = $image['url'];
        $attrs['title'] = $image['title'] ?? basename($attrs['src']);
        $attrs['alt'] = $image['alt'] ?? $attrs['title'];

        if (isset($settings[$sizeKey . '_size']) && $settings[$sizeKey . '_size'] === 'custom') {
            $height = $settings[$sizeKey . '_custom_dimension']['height'] ?: false;
            $width = $settings[$sizeKey . '_custom_dimension']['width'] ?: false;
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
         * @param string $imageKey Optional. Settings key for image size.
         *                               Default is `image`.
         * @param string $imageKey      Optional. Settings key for image. Default
         *                               is null. If not defined uses image size key
         *                               as the image key.
         */
        return HooksHelper::applyFilters('pagebuilder/image_size/get_attachment_image_html', $html, $settings, $imageKey, $imageKey)->getResult();
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
        $imageSizes = $this->getImageSizes();

        $args = $this->getArgs();

        if (!empty($args['default']) && isset($imageSizes[ $args['default'] ])) {
            $defaultValue = $args['default'];
        } else {
            // Get the first item for default value.
            $defaultValue = array_keys($imageSizes);
            $defaultValue = array_shift($defaultValue);
        }

        $fields['size']['options'] = $imageSizes;

        $fields['size']['default'] = $defaultValue;

        if (!isset($imageSizes['custom'])) {
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
        $imageSizes['full'] = __('Full');
        $imageSizes['custom'] = __('Custom');

        return $imageSizes;
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
