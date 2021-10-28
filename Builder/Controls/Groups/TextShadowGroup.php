<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;

class TextShadowGroup extends AbstractControlGroup
{
    const NAME = 'text-shadow';

    /**
     * Fields.
     *
     * Holds all the text shadow control fields.
     *
     *
     * @var array Text shadow control fields.
     */
    protected static $fields;

    /**
     * Init fields.
     *
     * Initialize text shadow control fields.
     *
     *
     * @return array Control fields.
     */
    protected function initFields()
    {
        $controls = [];

        $controls['text_shadow'] = [
            'label' => __('Text Shadow'),
            'type' => \Goomento\PageBuilder\Builder\Managers\Controls::TEXT_SHADOW,
            'selectors' => [
                '{{SELECTOR}}' => 'text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
            ],
        ];

        return $controls;
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the text shadow control. Used to return the
     * default options while initializing the text shadow control.
     *
     *
     * @return array Default text shadow control options.
     */
    protected function getDefaultOptions()
    {
        return [
            'popover' => [
                'starter_title' => __('Text Shadow'),
                'starter_name' => 'text_shadow_type',
                'starter_value' => 'yes',
                'settings' => [
                    'render_type' => 'ui',
                ],
            ],
        ];
    }
}
