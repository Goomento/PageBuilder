<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Base\ImportInterface;
use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;
use Goomento\PageBuilder\Helper\MediaHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class Media extends AbstractBaseMultiple implements ImportInterface
{
    const NAME = 'media';

    /**
     * Get media control default values.
     *
     * Retrieve the default value of the media control. Used to return the default
     * values while initializing the media control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return [
            'url' => '',
        ];
    }

    /**
     * Import media images.
     *
     * Used to import media control files from external sites while importing
     * Goomento template JSON file, and replacing the old data.
     *
     *
     * @param array|null $data Control settings
     * @param array $extraData
     * @return array Control settings.
     */
    public function onImport($data, $extraData = [])
    {
        if (empty($data['url'])) {
            return $data;
        }

        $newUrl = MediaHelper::downloadImage($data['url']);

        if ($newUrl) {
            $data['url'] = $newUrl;
        }

        return $data;
    }

    /**
     * Enqueue media control scripts and styles.
     *
     * Used to register and enqueue custom scripts and styles used by the media
     * control.
     *
     */
    public function enqueue()
    {
        ThemeHelper::enqueueScript('goomento-media');
    }

    /**
     * Render media control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <div class="gmt-control-field gmt-control-media">
            <label class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper gmt-aspect-ratio-219">
                <div class="gmt-control-media__content gmt-control-tag-area gmt-control-preview-area gmt-fit-aspect-ratio">
                    <div class="gmt-control-media-upload-button gmt-fit-aspect-ratio">
                        <i class="fas fa-plus-circle" aria-hidden="true"></i>
                    </div>
                    <div class="gmt-control-media-area gmt-fit-aspect-ratio">
                        <div class="gmt-control-media__remove" title="<?= __('Remove'); ?>">
                            <i class="fas fa-trash"></i>
                        </div>
                        <# if( 'image' === data.media_type ) { #>
                        <div class="gmt-control-media__preview gmt-fit-aspect-ratio"></div>
                        <# } else if( 'video' === data.media_type ) { #>
                        <video class="gmt-control-media-video" preload="metadata"></video>
                        <i class="fas fa-video"></i>
                        <# } #>
                    </div>
                    <div class="gmt-control-media__tools">
                        <# if( 'image' === data.media_type ) { #>
                        <div class="gmt-control-media__tool gmt-control-media__replace"><?= __('Choose Image'); ?></div>
                        <# } else if( 'video' === data.media_type ) { #>
                        <div class="gmt-control-media__tool gmt-control-media__replace"><?= __('Choose Video'); ?></div>
                        <# } #>
                    </div>
                </div>
            </div>
            <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
            <# } #>
            <input type="hidden" data-setting="{{ data.name }}"/>
        </div>
        <?php
    }

    /**
     * Get media control default settings.
     *
     * Retrieve the default settings of the media control. Used to return the default
     * settings while initializing the media control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'media_type' => 'image',
            'dynamic' => [
                'categories' => [TagsModule::IMAGE_CATEGORY],
                'returnType' => 'object',
                'active' => true
            ],
        ];
    }
}
