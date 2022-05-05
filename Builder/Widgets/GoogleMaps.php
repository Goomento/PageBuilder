<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilterGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;

class GoogleMaps extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    public const NAME = 'google_maps';
    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/google_maps.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Google Maps');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-map-marker-alt';
    }

    /**
     * @inheritDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'google', 'map', 'embed', 'location' ];
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_map',
            [
                'label' => __('Map'),
            ]
        );

        $default_address = 'Hang Sơn Đoòng';

        $this->addControl(
            'address',
            [
                'label' => __('Location'),
                'type' => Controls::TEXT,
                'placeholder' => $default_address,
                'default' => $default_address,
                'label_block' => true,
            ]
        );

        $this->addControl(
            'zoom',
            [
                'label' => __('Zoom'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'height',
            [
                'label' => __('Height'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 40,
                        'max' => 1440,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_map_style',
            [
                'label' => __('Map'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('map_filter');

        $this->startControlsTab(
            'normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} iframe',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}}:hover iframe',
            ]
        );

        $this->addControl(
            'hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} iframe' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();
    }
}
