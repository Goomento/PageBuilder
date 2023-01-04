<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;

class BackgroundGroup extends AbstractControlGroup
{

    const NAME = 'background';

    /**
     * Fields.
     *
     * Holds all the background control fields.
     *
     * @var array Background control fields.
     */
    protected static $fields;

    /**
     * Background Types.
     *
     * Holds all the available background types.
     *
     * @var array
     */
    private static $backgroundTypes;

    /**
     * Get background control types.
     *
     * Retrieve available background types.
     *
     *
     * @return array Available background types.
     */
    public static function getBackgroundTypes()
    {
        if (null === self::$backgroundTypes) {
            self::$backgroundTypes = self::getDefaultBackgroundTypes();
        }

        return self::$backgroundTypes;
    }

    /**
     * Get Default background types.
     *
     * Retrieve background control initial types.
     *
     * @return array Default background types.
     */
    private static function getDefaultBackgroundTypes()
    {
        return [
            'classic' => [
                'title' => __('Classic'),
                'icon' => 'fas fa-paint-brush',
            ],
            'gradient' => [
                'title' => __('Gradient'),
                'icon' => 'fas fa-barcode',
            ],
            'video' => [
                'title' => __('Video'),
                'icon' => 'fas fa-video',
            ],
            'slideshow' => [
                'title' => __('Slideshow'),
                'icon' => 'fab fa-slideshare',
            ],
        ];
    }

    /**
     * Init fields.
     *
     * Initialize background control fields.
     *
     *
     * @return array Control fields.
     */
    public function initFields()
    {
        $fields = [];

        $fields['background'] = [
            'label' => __('Background Type'),
            'type' => Controls::CHOOSE,
            'label_block' => false,
            'render_type' => 'ui',
        ];

        $fields['color'] = [
            'label' => __('Color'),
            'type' => Controls::COLOR,
            'default' => '',
            'title' => __('Background Color'),
            'selectors' => [
                '{{SELECTOR}}' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'background' => [ 'classic', 'gradient' ],
            ],
        ];

        $fields['color_stop'] = [
            'label' => __('Location'),
            'type' => Controls::SLIDER,
            'size_units' => [ '%' ],
            'default' => [
                'unit' => '%',
                'size' => 0,
            ],
            'render_type' => 'ui',
            'condition' => [
                'background' => [ 'gradient' ],
            ],
            'of_type' => 'gradient',
        ];

        $fields['color_b'] = [
            'label' => __('Second Color'),
            'type' => Controls::COLOR,
            'default' => '#f2295b',
            'render_type' => 'ui',
            'condition' => [
                'background' => [ 'gradient' ],
            ],
            'of_type' => 'gradient',
        ];

        $fields['color_b_stop'] = [
            'label' => __('Location'),
            'type' => Controls::SLIDER,
            'size_units' => [ '%' ],
            'default' => [
                'unit' => '%',
                'size' => 100,
            ],
            'render_type' => 'ui',
            'condition' => [
                'background' => [ 'gradient' ],
            ],
            'of_type' => 'gradient',
        ];

        $fields['gradient_type'] = [
            'label' => __('Type'),
            'type' => Controls::SELECT,
            'options' => [
                'linear' => __('Linear'),
                'radial' => __('Radial'),
            ],
            'default' => 'linear',
            'render_type' => 'ui',
            'condition' => [
                'background' => [ 'gradient' ],
            ],
            'of_type' => 'gradient',
        ];

        $fields['gradient_angle'] = [
            'label' => __('Angle'),
            'type' => Controls::SLIDER,
            'size_units' => [ 'deg' ],
            'default' => [
                'unit' => 'deg',
                'size' => 180,
            ],
            'range' => [
                'deg' => [
                    'step' => 10,
                ],
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
            ],
            'condition' => [
                'background' => [ 'gradient' ],
                'gradient_type' => 'linear',
            ],
            'of_type' => 'gradient',
        ];

        $fields['gradient_position'] = [
            'label' => __('Position'),
            'type' => Controls::SELECT,
            'options' => [
                'center center' => __('Center Center'),
                'center left' => __('Center Left'),
                'center right' => __('Center Right'),
                'top center' => __('Top Center'),
                'top left' => __('Top Left'),
                'top right' => __('Top Right'),
                'bottom center' => __('Bottom Center'),
                'bottom left' => __('Bottom Left'),
                'bottom right' => __('Bottom Right'),
            ],
            'default' => 'center center',
            'selectors' => [
                '{{SELECTOR}}' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
            ],
            'condition' => [
                'background' => [ 'gradient' ],
                'gradient_type' => 'radial',
            ],
            'of_type' => 'gradient',
        ];

        $fields['image'] = [
            'label' => __('Image'),
            'type' => Controls::MEDIA,
            'dynamic' => [
                'active' => true,
            ],
            'responsive' => true,
            'title' => __('Background Image'),
            'selectors' => [
                '{{SELECTOR}}' => 'background-image: url("{{URL}}");',
            ],
            'render_type' => 'template',
            'condition' => [
                'background' => [ 'classic' ],
            ],
        ];

        $fields['position'] = [
            'label' => __('Position'),
            'type' => Controls::SELECT,
            'default' => '',
            'responsive' => true,
            'options' => [
                '' => __('Default'),
                'top left' => __('Top Left'),
                'top center' => __('Top Center'),
                'top right' => __('Top Right'),
                'center left' => __('Center Left'),
                'center center' => __('Center Center'),
                'center right' => __('Center Right'),
                'bottom left' => __('Bottom Left'),
                'bottom center' => __('Bottom Center'),
                'bottom right' => __('Bottom Right'),
                'initial' => __('Custom'),
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'background-position: {{VALUE}};',
            ],
            'condition' => [
                'background' => [ 'classic' ],
                'image.url!' => '',
            ],
        ];

        $fields['xpos'] = [
            'label' => __('X Position'),
            'type' => Controls::SLIDER,
            'responsive' => true,
            'size_units' => [ 'px', 'em', '%', 'vw' ],
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'tablet_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'mobile_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => -800,
                    'max' => 800,
                ],
                'em' => [
                    'min' => -100,
                    'max' => 100,
                ],
                '%' => [
                    'min' => -100,
                    'max' => 100,
                ],
                'vw' => [
                    'min' => -100,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'background-position: {{SIZE}}{{UNIT}} {{ypos.SIZE}}{{ypos.UNIT}}',
            ],
            'condition' => [
                'background' => [ 'classic' ],
                'position' => [ 'initial' ],
                'image.url!' => '',
            ],
            'required' => true,
            'device_args' => [
                ControlsStack::RESPONSIVE_TABLET => [
                    'selectors' => [
                        '{{SELECTOR}}' => 'background-position: {{SIZE}}{{UNIT}} {{ypos_tablet.SIZE}}{{ypos_tablet.UNIT}}',
                    ],
                    'condition' => [
                        'background' => [ 'classic' ],
                        'position_tablet' => [ 'initial' ],
                    ],
                ],
                ControlsStack::RESPONSIVE_MOBILE => [
                    'selectors' => [
                        '{{SELECTOR}}' => 'background-position: {{SIZE}}{{UNIT}} {{ypos_mobile.SIZE}}{{ypos_mobile.UNIT}}',
                    ],
                    'condition' => [
                        'background' => [ 'classic' ],
                        'position_mobile' => [ 'initial' ],
                    ],
                ],
            ],
        ];

        $fields['ypos'] = [
            'label' => __('Y Position'),
            'type' => Controls::SLIDER,
            'responsive' => true,
            'size_units' => [ 'px', 'em', '%', 'vh' ],
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'tablet_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'mobile_default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => -800,
                    'max' => 800,
                ],
                'em' => [
                    'min' => -100,
                    'max' => 100,
                ],
                '%' => [
                    'min' => -100,
                    'max' => 100,
                ],
                'vh' => [
                    'min' => -100,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'background-position: {{xpos.SIZE}}{{xpos.UNIT}} {{SIZE}}{{UNIT}}',
            ],
            'condition' => [
                'background' => [ 'classic' ],
                'position' => [ 'initial' ],
                'image.url!' => '',
            ],
            'required' => true,
            'device_args' => [
                ControlsStack::RESPONSIVE_TABLET => [
                    'selectors' => [
                        '{{SELECTOR}}' => 'background-position: {{xpos_tablet.SIZE}}{{xpos_tablet.UNIT}} {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        'background' => [ 'classic' ],
                        'position_tablet' => [ 'initial' ],
                    ],
                ],
                ControlsStack::RESPONSIVE_MOBILE => [
                    'selectors' => [
                        '{{SELECTOR}}' => 'background-position: {{xpos_mobile.SIZE}}{{xpos_mobile.UNIT}} {{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        'background' => [ 'classic' ],
                        'position_mobile' => [ 'initial' ],
                    ],
                ],
            ],
        ];

        $fields['attachment'] = [
            'label' => __('Attachment'),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('Default'),
                'scroll' => __('Scroll'),
                'fixed' => __('Fixed'),
            ],
            'selectors' => [
                '(desktop+){{SELECTOR}}' => 'background-attachment: {{VALUE}};',
            ],
            'condition' => [
                'background' => [ 'classic' ],
                'image.url!' => '',
            ],
        ];

        $fields['attachment_alert'] = [
            'type' => Controls::RAW_HTML,
            'content_classes' => 'gmt-control-field-description',
            'raw' => __('Note: Attachment Fixed works only on desktop.'),
            'separator' => 'none',
            'condition' => [
                'background' => [ 'classic' ],
                'image.url!' => '',
                'attachment' => 'fixed',
            ],
        ];

        $fields['repeat'] = [
            'label' => __('Repeat'),
            'type' => Controls::SELECT,
            'default' => '',
            'responsive' => true,
            'options' => [
                '' => __('Default'),
                'no-repeat' => __('No-repeat', 'Background AbstractControl'),
                'repeat' => __('Repeat'),
                'repeat-x' => __('Repeat-x', 'Background AbstractControl'),
                'repeat-y' => __('Repeat-y', 'Background AbstractControl'),
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'background-repeat: {{VALUE}};',
            ],
            'condition' => [
                'background' => [ 'classic' ],
                'image.url!' => '',
            ],
        ];

        $fields['size'] = [
            'label' => __('Size'),
            'type' => Controls::SELECT,
            'responsive' => true,
            'default' => '',
            'options' => [
                '' => __('Default'),
                'auto' => __('Auto'),
                'cover' => __('Cover'),
                'contain' => __('Contain'),
                'initial' => __('Custom'),
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'background-size: {{VALUE}};',
            ],
            'condition' => [
                'background' => [ 'classic' ],
                'image.url!' => '',
            ],
        ];

        $fields['bg_width'] = [
            'label' => __('Width'),
            'type' => Controls::SLIDER,
            'responsive' => true,
            'size_units' => [ 'px', 'em', '%', 'vw' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
                'vw' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'default' => [
                'size' => 100,
                'unit' => '%',
            ],
            'required' => true,
            'selectors' => [
                '{{SELECTOR}}' => 'background-size: {{SIZE}}{{UNIT}} auto',

            ],
            'condition' => [
                'background' => [ 'classic' ],
                'size' => [ 'initial' ],
                'image.url!' => '',
            ],
            'device_args' => [
                ControlsStack::RESPONSIVE_TABLET => [
                    'selectors' => [
                        '{{SELECTOR}}' => 'background-size: {{SIZE}}{{UNIT}} auto',
                    ],
                    'condition' => [
                        'background' => [ 'classic' ],
                        'size_tablet' => [ 'initial' ],
                    ],
                ],
                ControlsStack::RESPONSIVE_MOBILE => [
                    'selectors' => [
                        '{{SELECTOR}}' => 'background-size: {{SIZE}}{{UNIT}} auto',
                    ],
                    'condition' => [
                        'background' => [ 'classic' ],
                        'size_mobile' => [ 'initial' ],
                    ],
                ],
            ],
        ];

        $fields['video_link'] = [
            'label' => __('Video Link'),
            'type' => Controls::TEXT,
            'placeholder' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
            'description' => __('YouTube/Vimeo link, or link to video file (mp4 is recommended).'),
            'label_block' => true,
            'default' => '',
            'condition' => [
                'background' => [ 'video' ],
            ],
            'of_type' => 'video',
            'frontend_available' => true,
        ];

        $fields['video_start'] = [
            'label' => __('Start Time'),
            'type' => Controls::NUMBER,
            'description' => __('Specify a start time (in seconds)'),
            'placeholder' => 10,
            'condition' => [
                'background' => [ 'video' ],
            ],
            'of_type' => 'video',
            'frontend_available' => true,
        ];

        $fields['video_end'] = [
            'label' => __('End Time'),
            'type' => Controls::NUMBER,
            'description' => __('Specify an end time (in seconds)'),
            'placeholder' => 70,
            'condition' => [
                'background' => [ 'video' ],
            ],
            'of_type' => 'video',
            'frontend_available' => true,
        ];

        $fields['play_once'] = [
            'label' => __('Play Once'),
            'type' => Controls::SWITCHER,
            'condition' => [
                'background' => [ 'video' ],
            ],
            'of_type' => 'video',
            'frontend_available' => true,
        ];

        $fields['play_on_mobile'] = [
            'label' => __('Play On Mobile'),
            'type' => Controls::SWITCHER,
            'condition' => [
                'background' => [ 'video' ],
            ],
            'of_type' => 'video',
            'frontend_available' => true,
        ];

        $fields['video_fallback'] = [
            'label' => __('Background Fallback'),
            'description' => __('This cover image will replace the background video in case that the video could not be loaded.'),
            'type' => Controls::MEDIA,
            'label_block' => true,
            'condition' => [
                'background' => [ 'video' ],
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'background: url("{{URL}}") 50% 50%; background-size: cover;',
            ],
            'of_type' => 'video',
        ];

        $fields['slideshow_gallery'] = [
            'label' => __('Images'),
            'type' => Controls::GALLERY,
            'condition' => [
                'background' => [ 'slideshow' ],
            ],
            'show_label' => false,
            'of_type' => 'slideshow',
            'frontend_available' => true,
        ];

        $fields['slideshow_loop'] = [
            'label' => __('Infinite Loop'),
            'type' => Controls::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'background' => [ 'slideshow' ],
            ],
            'of_type' => 'slideshow',
            'frontend_available' => true,
        ];

        $fields['slideshow_slide_duration'] = [
            'label' => __('Duration') . ' (ms)',
            'type' => Controls::NUMBER,
            'default' => 5000,
            'condition' => [
                'background' => [ 'slideshow' ],
            ],
            'frontend_available' => true,
        ];

        $fields['slideshow_slide_transition'] = [
            'label' => __('Transition'),
            'type' => Controls::SELECT,
            'default' => 'fade',
            'options' => [
                'fade' => 'Fade',
                'slide_right' => 'Slide Right',
                'slide_left' => 'Slide Left',
                'slide_up' => 'Slide Up',
                'slide_down' => 'Slide Down',
            ],
            'condition' => [
                'background' => [ 'slideshow' ],
            ],
            'of_type' => 'slideshow',
            'frontend_available' => true,
        ];

        $fields['slideshow_transition_duration'] = [
            'label' => __('Transition Duration') . ' (ms)',
            'type' => Controls::NUMBER,
            'default' => 500,
            'condition' => [
                'background' => [ 'slideshow' ],
            ],
            'frontend_available' => true,
        ];

        $fields['slideshow_ken_burns'] = [
            'label' => __('Ken Burns Effect'),
            'type' => Controls::SWITCHER,
            'separator' => 'before',
            'condition' => [
                'background' => [ 'slideshow' ],
            ],
            'of_type' => 'slideshow',
            'frontend_available' => true,
        ];

        $fields['slideshow_ken_burns_zoom_direction'] = [
            'label' => __('Direction'),
            'type' => Controls::SELECT,
            'default' => 'in',
            'options' => [
                'in' => __('In'),
                'out' => __('Out'),
            ],
            'condition' => [
                'background' => [ 'slideshow' ],
                'slideshow_ken_burns!' => '',
            ],
            'of_type' => 'slideshow',
            'frontend_available' => true,
        ];

        return $fields;
    }

    /**
     * Get child default args.
     *
     * Retrieve the default arguments for all the child controls for a specific group
     * control.
     *
     *
     * @return array Default arguments for all the child controls.
     */
    protected function getChildDefaultArgs()
    {
        return [
            'types' => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}}:not(.gmt-motion-effects-element-type-background), {{WRAPPER}} > .gmt-motion-effects-container > .gmt-motion-effects-layer',
        ];
    }

    /**
     * Filter fields.
     *
     * Filter which controls to display, using `include`, `exclude`, `condition`
     * and `of_type` arguments.
     *
     *
     * @return array Control fields.
     */
    protected function filterFields()
    {
        $fields = parent::filterFields();

        $args = $this->getArgs();

        foreach ($fields as &$field) {
            if (isset($field['of_type']) && ! in_array($field['of_type'], $args['types'])) {
                unset($field);
            }
        }

        return $fields;
    }

    /**
     * Prepare fields.
     *
     * Process background control fields before adding them to `add_control()`.
     *
     *
     * @param array $fields Background control fields.
     *
     * @return array Processed fields.
     */
    protected function prepareFields($fields)
    {
        $args = $this->getArgs();

        $backgroundTypes = self::getBackgroundTypes();

        $chooseTypes = [];

        foreach ($args['types'] as $type) {
            if (isset($backgroundTypes[ $type ])) {
                $chooseTypes[ $type ] = $backgroundTypes[ $type ];
            }
        }

        $fields['background']['options'] = $chooseTypes;

        return parent::prepareFields($fields);
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the background control. Used to return the
     * default options while initializing the background control.
     *
     *
     * @return array Default background control options.
     */
    protected function getDefaultOptions()
    {
        return [
            'popover' => false,
        ];
    }
}
