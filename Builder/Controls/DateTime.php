<?php
namespace Goomento\PageBuilder\Builder\Controls;

class DateTime extends AbstractControlData
{
    const NAME = 'date_time';

    /**
     * Get date time control default settings.
     *
     * Retrieve the default settings of the date time control. Used to return the
     * default settings while initializing the date time control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'picker_options' => [],
        ];
    }

    /**
     * Render date time control output in the editor.
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
            <label for="<?= $controlUid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper">
                <input id="<?= $controlUid; ?>" placeholder="{{ data.placeholder }}" class="gmt-date-time-picker flatpickr" type="text" data-setting="{{ data.name }}">
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
