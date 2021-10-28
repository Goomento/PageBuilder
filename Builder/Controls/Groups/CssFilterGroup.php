<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;

class CssFilterGroup extends AbstractControlGroup
{

    const NAME = 'css-filter';

    /**
     * Prepare fields.
     *
     * Process css_filter control fields before adding them to `add_control()`.
     *
     *
     * @param array $fields CSS filter control fields.
     *
     * @return array Processed fields.
     */
    protected static $fields;


    /**
     * Init fields.
     *
     * Initialize CSS filter control fields.
     *
     *
     * @return array Control fields.
     */
    protected function initFields()
    {
        $controls = [];

        $controls['blur'] = [
            'label' => __('Blur'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
            'required' => 'true',
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.1,
                ],
            ],
            'default' => [
                'size' => 0,
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px ) hue-rotate( {{hue.SIZE}}deg )',
            ],
        ];

        $controls['brightness'] = [
            'label' => __('Brightness'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
            'render_type' => 'ui',
            'required' => 'true',
            'default' => [
                'size' => 100,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'separator' => 'none',
        ];

        $controls['contrast'] = [
            'label' => __('Contrast'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
            'render_type' => 'ui',
            'required' => 'true',
            'default' => [
                'size' => 100,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'separator' => 'none',
        ];

        $controls['saturate'] = [
            'label' => __('Saturation'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
            'render_type' => 'ui',
            'required' => 'true',
            'default' => [
                'size' => 100,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'separator' => 'none',
        ];

        $controls['hue'] = [
            'label' => __('Hue'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
            'render_type' => 'ui',
            'required' => 'true',
            'default' => [
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 360,
                ],
            ],
            'separator' => 'none',
        ];

        return $controls;
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the CSS filter control. Used to return the
     * default options while initializing the CSS filter control.
     *
     *
     * @return array Default CSS filter control options.
     */
    protected function getDefaultOptions()
    {
        return [
            'popover' => [
                'starter_name' => 'css_filter',
                'starter_title' => __('CSS Filters'),
                'settings' => [
                    'render_type' => 'ui',
                ],
            ],
        ];
    }
}
