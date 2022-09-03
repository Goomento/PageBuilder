<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class ExitAnimation extends Animation
{
    const NAME = 'exit_animation';

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
         * @param array $additionalAnimations Additional Animations array.
         */
        $additionalAnimations = \Goomento\PageBuilder\Helper\HooksHelper::applyFilters('pagebuilder/controls/exit-animations/additional_animations', [])->getResult();

        return array_merge($animations, $additionalAnimations);
    }
}
