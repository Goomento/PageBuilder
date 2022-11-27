<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Helper\HooksHelper;

class Animation extends AbstractControlData
{
    const NAME = 'animation';

    /**
     * Retrieve default control settings.
     *
     * Get the default settings of the control. Used to return the default
     * settings while initializing the control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        $defaultSettings = parent::getDefaultSettings();

        $defaultSettings['label_block'] = true;
        $defaultSettings['render_type'] = 'none';

        return $defaultSettings;
    }

    /**
     * Get animations list.
     *
     * Retrieve the list of all the available animations.
     *
     *
     * @return array Control type.
     */
    public static function getAnimations()
    {
        $animations = [
            'Fading' => [
                'fadeIn' => 'Fade In',
                'fadeInDown' => 'Fade In Down',
                'fadeInLeft' => 'Fade In Left',
                'fadeInRight' => 'Fade In Right',
                'fadeInUp' => 'Fade In Up',
            ],
            'Zooming' => [
                'zoomIn' => 'Zoom In',
                'zoomInDown' => 'Zoom In Down',
                'zoomInLeft' => 'Zoom In Left',
                'zoomInRight' => 'Zoom In Right',
                'zoomInUp' => 'Zoom In Up',
            ],
            'Bouncing' => [
                'bounceIn' => 'Bounce In',
                'bounceInDown' => 'Bounce In Down',
                'bounceInLeft' => 'Bounce In Left',
                'bounceInRight' => 'Bounce In Right',
                'bounceInUp' => 'Bounce In Up',
            ],
            'Sliding' => [
                'slideInDown' => 'Slide In Down',
                'slideInLeft' => 'Slide In Left',
                'slideInRight' => 'Slide In Right',
                'slideInUp' => 'Slide In Up',
            ],
            'Rotating' => [
                'rotateIn' => 'Rotate In',
                'rotateInDownLeft' => 'Rotate In Down Left',
                'rotateInDownRight' => 'Rotate In Down Right',
                'rotateInUpLeft' => 'Rotate In Up Left',
                'rotateInUpRight' => 'Rotate In Up Right',
            ],
            'Attention Seekers' => [
                'bounce' => 'Bounce',
                'flash' => 'Flash',
                'pulse' => 'Pulse',
                'rubberBand' => 'Rubber Band',
                'shake' => 'Shake',
                'headShake' => 'Head Shake',
                'swing' => 'Swing',
                'tada' => 'Tada',
                'wobble' => 'Wobble',
                'jello' => 'Jello',
            ],
            'Light Speed' => [
                'lightSpeedIn' => 'Light Speed In',
            ],
            'Specials' => [
                'rollIn' => 'Roll In',
            ],
        ];

        /**
         * Element appearance animations list.
         *
         *
         * @param array $additionalAnimations Additional Animations array.
         */
        $additionalAnimations = HooksHelper::applyFilters('pagebuilder/controls/animations/additional_animations', [])->getResult();

        return array_merge($animations, $additionalAnimations);
    }

    /**
     * Render animations control template.
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
                <select id="<?= $controlUid; ?>" data-setting="{{ data.name }}">
                    <option value=""><?= __('Default'); ?></option>
                    <option value="none"><?= __('None'); ?></option>
                    <?php foreach (static::getAnimations() as $animationsGroupName => $animationsGroup): ?>
                        <optgroup label="<?= $animationsGroupName; ?>">
                            <?php foreach ($animationsGroup as $animationName => $animationTitle): ?>
                                <option value="<?= $animationName; ?>"><?= $animationTitle; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
