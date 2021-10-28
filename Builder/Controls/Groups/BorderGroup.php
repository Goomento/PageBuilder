<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;

class BorderGroup extends AbstractControlGroup
{

    const NAME = 'border';

    /**
     * Fields.
     *
     * Holds all the border control fields.
     *
     *
     * @var array Border control fields.
     */
    protected static $fields;

    /**
     * Init fields.
     *
     * Initialize border control fields.
     *
     *
     * @return array Control fields.
     */
    protected function initFields()
    {
        $fields = [];

        $fields['border'] = [
            'label' => __('Border Type'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SELECT,
            'options' => [
                '' => __('None'),
                'solid' => __('Solid'),
                'double' => __('Double'),
                'dotted' => __('Dotted'),
                'dashed' => __('Dashed'),
                'groove' => __('Groove'),
            ],
            'selectors' => [
                '{{SELECTOR}}' => 'border-style: {{VALUE}};',
            ],
        ];

        $fields['width'] = [
            'label' => __('Width'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::DIMENSIONS,
            'selectors' => [
                '{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'border!' => '',
            ],
            'responsive' => true,
        ];

        $fields['color'] = [
            'label' => __('Color'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::COLOR,
            'default' => '',
            'selectors' => [
                '{{SELECTOR}}' => 'border-color: {{VALUE}};',
            ],
            'condition' => [
                'border!' => '',
            ],
        ];

        return $fields;
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the border control. Used to return the
     * default options while initializing the border control.
     *
     *
     * @return array Default border control options.
     */
    protected function getDefaultOptions()
    {
        return [
            'popover' => false,
        ];
    }
}
