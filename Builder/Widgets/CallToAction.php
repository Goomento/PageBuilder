<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;

class CallToAction extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'calltoaction';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/call_to_action.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Call to Action');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-fire-alt';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'action', 'click', 'call to action'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return ['general'];
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerAction(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'trigger',
            [
                'label' => __('Trigger when'),
                'type' => Controls::SELECT,
                'default' => 'load',
                'options' => [
                    'load' => __('Page loaded'),
                    'timeout' => __('After timeout'),
                    'click' => __('Click on element'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'trigger_id',
            [
                'label' => __('Trigger CSS ID'),
                'description' => __('CSS ID of the Element, which might be found under Advanced > Identify > CSS ID.'),
                'type' => Controls::TEXT,
                'condition' => [
                    $prefix . 'trigger' => 'click'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'action',
            [
                'label' => __('Action'),
                'description' => __('Active this section when matching the trigger.'),
                'type' => Controls::SELECT,
                'default' => 'show_popup',
                'options' => [
                    'code' => __('Insert HTML/JS/CSS'),
                    'show_popup' => __('Open Popup'),
                    'hide_popup' => __('Close Popup'),
                    'show_element' => __('Show Element'),
                    'hide_element' => __('Hide Element'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'code',
            [
                'label' => __('Code'),
                'description' => __('JS/CSS must be wrapped into `script` or `style` tag.'),
                'type' => Controls::CODE,
                'language' => 'html',
                'condition' => [
                    $prefix . 'action' => 'code'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'timout',
            [
                'label' => __('Second(s)'),
                'description' => __('Trigger after amount of seconds.'),
                'type' => Controls::NUMBER,
                'default' => 10, // 10 seconds
                'condition' => [
                    $prefix . 'trigger' => 'timeout'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'target_id',
            [
                'label' => __('Target CSS ID'),
                'description' => __('CSS ID of the Element, which might be found under Advanced > Identify > CSS ID.'),
                'type' => Controls::TEXT,
                'condition' => [
                    $prefix . 'action!' => 'code'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'remember_in_seconds',
            [
                'label' => __('Remember trigger in (seconds)'),
                'description' => __('Remember this trigger, then ignore it in amount of seconds. For example: can use for displaying popup once per year to customer.'),
                'type' => Controls::NUMBER,
                'default' => '0',
                'condition' => [
                    $prefix . 'trigger!' => 'click'
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'calltoaction_action_section',
            [
                'label' => __('Define Action'),
            ]
        );

        self::registerAction($this);

        $this->endControlsSection();
    }

    /**
     * @return bool
     */
    protected function renderPreview(): bool
    {
        return false;
    }
}
