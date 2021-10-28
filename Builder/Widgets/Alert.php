<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Typography;

class Alert extends AbstractWidget
{

    const NAME = 'alert';

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
    public function getKeywords()
    {
        return [ 'alert', 'notice', 'message' ];
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerAlertInterface(AbstractWidget $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'type',
            $args + [
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
            $args + [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'placeholder' => __('Enter your title'),
                'default' => __('This is an Alert'),
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'description',
            $args + [
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
            $args + [
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
     * @param AbstractElement $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerAlertStyle(AbstractElement $widget, string $prefix = self::NAME . '_',
                                              string          $cssTarget = '.gmt-alert', array $args = [])
    {
        $widget->addControl(
            $prefix . 'background',
            $args + [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_color',
            $args + [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_left-width',
            $args + [
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


    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
        <# if ( settings.alert_title ) {
        view.addRenderAttribute( {
            alert_title: { class: 'gmt-alert-title' },
            alert_description: { class: 'gmt-alert-description' }
        } );

        view.addInlineEditingAttributes( 'alert_title', 'none' );
        view.addInlineEditingAttributes( 'alert_description' );
        #>

        <div class="gmt-alert gmt-alert-{{ settings.alert_type }}" role="alert">
            <span {{{ view.getRenderAttributeString( 'alert_title' ) }}}>{{{ settings.alert_title }}}</span>
            <span {{{ view.getRenderAttributeString( 'alert_description' ) }}}>{{{ settings.alert_description }}}</span>
            <# if ( 'show' === settings.alert_show_dismiss ) { #>
            <button type="button" class="gmt-alert-dismiss">
                <span aria-hidden="true">&times;</span>
                <span class="gmt-screen-only"><?= __('Dismiss alert'); ?></span>
            </button>
            <# } #>
        </div>
        <# } #>
        <?php
    }
}
