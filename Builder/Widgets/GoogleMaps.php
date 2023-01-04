<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilterGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;

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
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerGoogleMapInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'address',
            [
                'label' => __('Location'),
                'type' => Controls::TEXT,
                'placeholder' => 'Your address',
                'default' => 'Son Doong cave, Quang Binh Province, Vietnam',
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'zoom',
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

        $widget->addResponsiveControl(
            $prefix . 'height',
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
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerGoogleMapStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = 'iframe'
    ) {
        $widget->startControlsTabs($prefix . 'map_filter');

        $widget->startControlsTab(
            $prefix . 'normal',
            [
                'label' => __('Normal'),
            ]
        );

        $widget->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => $prefix . 'css_filters',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->endControlsTab();

        $widget->startControlsTab(
            $prefix . 'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $widget->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => $prefix . 'css_filters_hover',
                'selector' => '{{WRAPPER}}:hover ' . $cssTarget,
            ]
        );

        $widget->addControl(
            $prefix . 'hover_transition',
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
                    '{{WRAPPER}} ' . $cssTarget => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $widget->endControlsTab();

        $widget->endControlsTabs();
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

        self::registerGoogleMapInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_map_style',
            [
                'label' => __('Map'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        self::registerGoogleMapStyle($this);

        $this->endControlsSection();
    }
}
