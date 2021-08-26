<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Helper\Hooks;

/**
 * Class HoverAnimation
 * @package Goomento\PageBuilder\Builder\Controls
 */
class HoverAnimation extends BaseData
{

    /**
     * Animations.
     *
     * Holds all the available hover animation effects of the control.
     *
     *
     * @var array
     */
    private static $_animations;

    /**
     * Get hover animation control type.
     *
     * Retrieve the control type, in this case `hover_animation`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'hover_animation';
    }

    /**
     * Get animations.
     *
     * Retrieve the available hover animation effects.
     *
     *
     * @return array Available hover animation.
     */
    public static function getAnimations()
    {
        if (is_null(self::$_animations)) {
            self::$_animations = [
                'grow' => 'Grow',
                'shrink' => 'Shrink',
                'pulse' => 'Pulse',
                'pulse-grow' => 'Pulse Grow',
                'pulse-shrink' => 'Pulse Shrink',
                'push' => 'Push',
                'pop' => 'Pop',
                'bounce-in' => 'Bounce In',
                'bounce-out' => 'Bounce Out',
                'rotate' => 'Rotate',
                'grow-rotate' => 'Grow Rotate',
                'float' => 'Float',
                'sink' => 'Sink',
                'bob' => 'Bob',
                'hang' => 'Hang',
                'skew' => 'Skew',
                'skew-forward' => 'Skew Forward',
                'skew-backward' => 'Skew Backward',
                'wobble-vertical' => 'Wobble Vertical',
                'wobble-horizontal' => 'Wobble Horizontal',
                'wobble-to-bottom-right' => 'Wobble To Bottom Right',
                'wobble-to-top-right' => 'Wobble To Top Right',
                'wobble-top' => 'Wobble Top',
                'wobble-bottom' => 'Wobble Bottom',
                'wobble-skew' => 'Wobble Skew',
                'buzz' => 'Buzz',
                'buzz-out' => 'Buzz Out',
            ];

            $additional_animations = [];
            /**
             * Element hover animations list.
             *
             *
             * @param array $additional_animations Additional Animations array.
             */
            $additional_animations = Hooks::applyFilters('pagebuilder/controls/hover_animations/additional_animations', $additional_animations);
            self::$_animations = array_merge(self::$_animations, $additional_animations);
        }

        return self::$_animations;
    }

    /**
     * Render hover animation control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $control_uid = $this->getControlUid(); ?>
		<div class="gmt-control-field">
			<label for="<?= $control_uid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
			<div class="gmt-control-input-wrapper">
				<select id="<?= $control_uid; ?>" data-setting="{{ data.name }}">
					<option value=""><?= __('None'); ?></option>
					<?php foreach (self::getAnimations() as $animation_name => $animation_title) : ?>
						<option value="<?= $animation_name; ?>"><?= $animation_title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="gmt-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
    }

    /**
     * Get hover animation control default settings.
     *
     * Retrieve the default settings of the hover animation control. Used to return
     * the default settings while initializing the hover animation control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
        ];
    }
}
