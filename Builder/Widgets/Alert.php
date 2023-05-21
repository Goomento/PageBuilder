<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;

class Alert extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'alert';

    /**
     * @inheirtDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/alert.phtml';

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Alert');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'far fa-bell';
    }

    /**
     * @inheritDoc
     */
    public function getScriptDepends()
    {
        return ['goomento-widget-alert'];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'alert', 'notice', 'message' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerAlertInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => 'info',
                'options' => [
                    'info' => __('Info'),
                    'success' => __('Success'),
                    'warning' => __('Warning'),
                    'danger' => __('Danger'),
                ],
                'style_transfer' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'title',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'placeholder' => __('Enter your title'),
                'default' => __('This is an Alert'),
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'description',
            [
                'label' => __('Content'),
                'type' => Controls::TEXTAREA,
                'placeholder' => __('Enter your description'),
                'default' => __('I am a description. Click the edit button to change this text.'),
                'separator' => 'none',
                'show_label' => false,
            ]
        );

        $widget->addControl(
            $prefix . 'show_dismiss',
            [
                'label' => __('Dismiss Button'),
                'type' => Controls::SELECT,
                'default' => 'show',
                'options' => [
                    'show' => __('Show'),
                    'hide' => __('Hide'),
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
    public static function registerAlertStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-alert'
    ) {
        $widget->addControl(
            $prefix . 'background',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_left-width',
            [
                'label' => __('Left Border Width'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-left-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_alert',
            [
                'label' => __('Alert'),
            ]
        );

        self::registerAlertInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_type',
            [
                'label' => __('Alert'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerAlertStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Text::registerTextStyle($this, self::NAME . '_title_', '.gmt-alert-title');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_description',
            [
                'label' => __('Description'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Text::registerTextStyle($this, self::NAME . '_description_', '.gmt-alert-description');

        $this->endControlsSection();
    }
}
