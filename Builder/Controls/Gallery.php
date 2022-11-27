<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Base\ImportInterface;
use Goomento\PageBuilder\Helper\MediaHelper;

class Gallery extends AbstractControlData implements ImportInterface
{
    const NAME = 'gallery';

    /**
     * Import gallery images.
     *
     * Used to import gallery control files from external sites while importing
     * Goomento template JSON file, and replacing the old data.
     *
     *
     * @param array|null $data Control settings
     * @param array $extraData
     * @return array Control settings.
     */
    public function onImport($data, $extraData = [])
    {
        foreach ($data as &$attachment) {
            if (empty($attachment['url'])) {
                continue;
            }

            $newUrl = MediaHelper::downloadImage($attachment['url']);
            if ($newUrl) {
                $attachment['url'] = $newUrl;
            }
        }

        // Filter out attachments that don't exist
        return array_filter($data);
    }

    /**
     * Render gallery control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <div class="gmt-control-field">
            <div class="gmt-control-title">{{{ data.label }}}</div>
            <div class="gmt-control-input-wrapper">
                <# if ( data.description ) { #>
                <div class="gmt-control-field-description">{{{ data.description }}}</div>
                <# } #>
                <div class="gmt-control-media__content gmt-control-tag-area">
                    <div class="gmt-control-gallery-status">
                        <span class="gmt-control-gallery-status-title"></span>
                        <span class="gmt-control-gallery-clear"><i class="fas fa-trash" aria-hidden="true"></i></span>
                    </div>
                    <div class="gmt-control-gallery-content">
                        <div class="gmt-control-gallery-thumbnails"></div>
                        <div class="gmt-control-gallery-edit"><span><i class="fas fa-pencil-alt"></i></span></div>
                        <button class="gmt-button gmt-control-gallery-add" aria-label="<?= __('Add Images'); ?>">
                            <i class="fas fa-plus-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get gallery control default settings.
     *
     * Retrieve the default settings of the gallery control. Used to return the
     * default settings while initializing the gallery control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'separator' => 'none',
            'dynamic' => [
                'returnType' => 'object',
            ],
        ];
    }

    /**
     * Get gallery control default values.
     *
     * Retrieve the default value of the gallery control. Used to return the default
     * values while initializing the gallery control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return [];
    }
}
