<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);
namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class ExitAnimation
 * @package Goomento\PageBuilder\Builder\Controls
 */
class ExitAnimation extends Animation
{

    /**
     * Get control type.
     *
     * Retrieve the animation control type.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'exit_animation';
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
                'fadeIn' => 'Fade Out',
                'fadeInDown' => 'Fade Out Up',
                'fadeInLeft' => 'Fade Out Left',
                'fadeInRight' => 'Fade Out Right',
                'fadeInUp' => 'Fade Out Down',
            ],
            'Zooming' => [
                'zoomIn' => 'Zoom Out',
                'zoomInDown' => 'Zoom Out Up',
                'zoomInLeft' => 'Zoom Out Left',
                'zoomInRight' => 'Zoom Out Right',
                'zoomInUp' => 'Zoom Out Down',
            ],
            'Sliding' => [
                'slideInDown' => 'Slide Out Up',
                'slideInLeft' => 'Slide Out Left',
                'slideInRight' => 'Slide Out Right',
                'slideInUp' => 'Slide Out Down',
            ],
            'Rotating' => [
                'rotateIn' => 'Rotate Out',
                'rotateInDownLeft' => 'Rotate Out Up Left',
                'rotateInDownRight' => 'Rotate Out Up Right',
                'rotateInUpRight' => 'Rotate Out Down Left',
                'rotateInUpLeft' => 'Rotate Out Down Right',
            ],
            'Light Speed' => [
                'lightSpeedIn' => 'Light Speed Out',
            ],
            'Specials' => [
                'rollIn' => 'Roll Out',
            ],
        ];

        /**
         * Element appearance animations list.
         *
         *
         * @param array $additional_animations Additional Animations array.
         */
        $additional_animations = \Goomento\PageBuilder\Helper\Hooks::applyFilters('pagebuilder/controls/exit-animations/additional_animations', []);

        return array_merge($animations, $additional_animations);
    }
}
