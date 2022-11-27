<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Helper\EscaperHelper;

class Dimensions extends AbstractBaseUnits
{
    const NAME = 'dimensions';

    /**
     * Get dimensions control default values.
     *
     * Retrieve the default value of the dimensions control. Used to return the
     * default values while initializing the dimensions control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return array_merge(
            AbstractBaseUnits::getDefaultValue(),
            [
                'top' => '',
                'right' => '',
                'bottom' => '',
                'left' => '',
                'isLinked' => true,
            ]
        );
    }

    /**
     * Get dimensions control default settings.
     *
     * Retrieve the default settings of the dimensions control. Used to return the
     * default settings while initializing the dimensions control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return array_merge(
            parent::getDefaultSettings(),
            [
                'label_block' => true,
                'allowed_dimensions' => 'all',
                'placeholder' => '',
            ]
        );
    }

    /**
     * Render dimensions control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $dimensions = [
            'top' => __('Top'),
            'right' => __('Right'),
            'bottom' => __('Bottom'),
            'left' => __('Left'),
        ]; ?>
        <div class="gmt-control-field">
            <label class="gmt-control-title">{{{ data.label }}}</label>
            <?php $this->printUnitsTemplate(); ?>
            <div class="gmt-control-input-wrapper">
                <ul class="gmt-control-dimensions">
                    <?php
                    foreach ($dimensions as $dimensionKey => $dimensionTitle):
                        $controlUid = $this->getControlUid($dimensionKey); ?>
                        <li class="gmt-control-dimension">
                            <input id="<?= $controlUid; ?>" type="number" data-setting="<?= EscaperHelper::escapeHtml($dimensionKey); ?>"
                                   placeholder="<#
                               if ( _.isObject( data.placeholder ) ) {
                                if ( ! _.isUndefined( data.placeholder.<?= $dimensionKey; ?> ) ) {
                                    print( data.placeholder.<?= $dimensionKey; ?> );
                                }
                               } else {
                                print( data.placeholder );
                               } #>"
                            <# if ( -1 === _.indexOf( allowed_dimensions, '<?= $dimensionKey; ?>' ) ) { #>
                                disabled
                                <# } #>
                                    />
                            <label for="<?= EscaperHelper::escapeHtml($controlUid); ?>" class="gmt-control-dimension-label"><?= $dimensionTitle; ?></label>
                        </li>
                    <?php endforeach; ?>
                    <li>
                        <button class="gmt-link-dimensions tooltip-target" data-tooltip="<?= __('Link values together'); ?>">
                            <span class="gmt-linked">
                                <i class="fas fa-link" aria-hidden="true"></i>
                                <span class="gmt-screen-only"><?= __('Link values together'); ?></span>
                            </span>
                            <span class="gmt-unlinked">
                                <i class="fas fa-unlink"></i>
                                <span class="gmt-screen-only"><?= __('Unlinked values'); ?></span>
                            </span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
