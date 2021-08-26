<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\TemplateLibrary\Manager;
use Goomento\PageBuilder\Core\DynamicTags\Module as TagsModule;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Gallery
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Gallery extends BaseData
{

    /**
     * Get gallery control type.
     *
     * Retrieve the control type, in this case `gallery`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'gallery';
    }

    /**
     * Import gallery images.
     *
     * Used to import gallery control files from external sites while importing
     * SagoTheme template JSON file, and replacing the old data.
     *
     *
     * @param array $settings Control settings
     *
     * @return array Control settings.
     */
    public function onImport($settings)
    {
        foreach ($settings as &$attachment) {
            if (empty($attachment['url'])) {
                continue;
            }

            $attachment = StaticObjectManager::get(Manager::class)->getImportImagesInstance()->import($attachment);
        }

        // Filter out attachments that don't exist
        return array_filter($settings);
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
                'categories' => [ TagsModule::GALLERY_CATEGORY ],
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
    public function getDefaultValue()
    {
        return [];
    }
}
