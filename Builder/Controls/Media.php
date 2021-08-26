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
use Goomento\PageBuilder\Helper\Theme;

/**
 * Class Media
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Media extends BaseMultiple
{

    /**
     * Get media control type.
     *
     * Retrieve the control type, in this case `media`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'media';
    }

    /**
     * Get media control default values.
     *
     * Retrieve the default value of the media control. Used to return the default
     * values while initializing the media control.
     *
     *
     * @return array Control default value.
     */
    public function getDefaultValue()
    {
        return [
            'url' => '',
        ];
    }

    /**
     * Import media images.
     *
     * Used to import media control files from external sites while importing
     * SagoTheme template JSON file, and replacing the old data.
     *
     *
     * @param array $settings Control settings
     *
     * @return array Control settings.
     */
    public function onImport($settings)
    {
        if (empty($settings['url'])) {
            return $settings;
        }

        /** @var Manager $templateLibraryManager */
        $templateLibraryManager = StaticObjectManager::get(Manager::class);

        $newUrl = $templateLibraryManager->getImportImagesInstance()->import($settings['url']);

        if ($newUrl) {
            $settings['url'] = $newUrl;
        }

        return $settings;
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
        Theme::enqueueScript('goomento-media');
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
                'categories' => [ TagsModule::IMAGE_CATEGORY ],
                'returnType' => 'object',
            ],
        ];
    }
}
