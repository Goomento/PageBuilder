<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class PopoverToggle extends AbstractControlData
{

    const NAME = 'popover_toggle';


    /**
     * Get popover toggle control default settings.
     *
     * Retrieve the default settings of the popover toggle control. Used to
     * return the default settings while initializing the popover toggle
     * control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'return_value' => 'yes',
        ];
    }

    /**
     * Render popover toggle control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $controlUid = $this->getControlUid(); ?>
        <div class="gmt-control-field">
            <label class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper">
                <input id="<?= $controlUid; ?>-custom" class="gmt-control-popover-toggle-toggle" type="radio" name="gmt-choose-{{ data.name }}-{{ data._cid }}" value="{{ data.return_value }}">
                <label class="gmt-control-popover-toggle-toggle-label" for="<?= $controlUid; ?>-custom">
                    <i class="far fa-edit"></i>
                    <span class="gmt-screen-only"><?= __('Edit'); ?></span>
                </label>
                <input id="<?= $controlUid; ?>-default" type="radio" name="gmt-choose-{{ data.name }}-{{ data._cid }}" value="">
                <label class="gmt-control-popover-toggle-reset-label tooltip-target" for="<?= $controlUid; ?>-default" data-tooltip="<?= __('Back to default'); ?>" data-tooltip-pos="s">
                    <i class="fas fa-redo"></i>
                    <span class="gmt-screen-only"><?= __('Back to default'); ?></span>
                </label>
            </div>
        </div>
        <?php
    }
}
