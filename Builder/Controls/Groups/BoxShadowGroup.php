<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;

class BoxShadowGroup extends AbstractControlGroup
{

    const NAME = 'box-shadow';

    /**
     * Fields.
     *
     * Holds all the box shadow control fields.
     *
     *
     * @var array Box shadow control fields.
     */
    protected static $fields;

    /**
     * Init fields.
     *
     * Initialize box shadow control fields.
     *
     *
     * @return array Control fields.
     */
    protected function initFields()
    {
        $controls = [];

        $controls['box_shadow'] = [
            'label' => __('Box Shadow'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::BOX_SHADOW,
            'selectors' => [
                '{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
            ],
        ];

        $controls['box_shadow_position'] = [
            'label' => __('Position'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
            'options' => [
                ' ' => __('Outline'),
                'inset' => __('Inset'),
            ],
            'default' => ' ',
            'render_type' => 'ui',
        ];

        return $controls;
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the box shadow control. Used to return the
     * default options while initializing the box shadow control.
     *
     *
     * @return array Default box shadow control options.
     */
    protected function getDefaultOptions()
    {
        return [
            'popover' => [
                'starter_title' => __('Box Shadow'),
                'starter_name' => 'box_shadow_type',
                'starter_value' => 'yes',
                'settings' => [
                    'render_type' => 'ui',
                ],
            ],
        ];
    }
}
