<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;

/**
 * Class Spacer
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Spacer extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve spacer widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'spacer';
    }

    /**
     * Get widget title.
     *
     * Retrieve spacer widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Spacer');
    }

    /**
     * Get widget icon.
     *
     * Retrieve spacer widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-arrows-v fas fa-arrows-alt-v';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the spacer widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     *
     * @return array Widget categories.
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     *
     * @return array Widget keywords.
     */
    public function getKeywords()
    {
        return [ 'space' ];
    }

    /**
     * Register spacer widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_spacer',
            [
                'label' => __('Spacer'),
            ]
        );

        $this->addResponsiveControl(
            'space',
            [
                'label' => __('Space'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 600,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-spacer-inner' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'view',
            [
                'label' => __('View'),
                'type' => \Goomento\PageBuilder\Builder\Managers\Controls::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render spacer widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        ?>
		<div class="gmt-spacer">
			<div class="gmt-spacer-inner"></div>
		</div>
		<?php
    }

    /**
     * Render spacer widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<div class="gmt-spacer">
			<div class="gmt-spacer-inner"></div>
		</div>
		<?php
    }
}
