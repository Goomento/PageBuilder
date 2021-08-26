<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Managers\Controls;

/**
 * Class Html
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Html extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve HTML widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'html';
    }

    /**
     * Get widget title.
     *
     * Retrieve HTML widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('HTML');
    }

    /**
     * Get widget icon.
     *
     * Retrieve HTML widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-code';
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
        return [ 'html', 'code' ];
    }

    /**
     * Register HTML widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_title',
            [
                'label' => __('HTML Code'),
            ]
        );

        $this->addControl(
            'html',
            [
                'label' => '',
                'type' => Controls::CODE,
                'default' => '',
                'placeholder' => __('Enter your code'),
                'show_label' => false,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render HTML widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        echo $this->getSettingsForDisplay('html');
    }

    /**
     * Render HTML widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		{{{ settings.html }}}
		<?php
    }
}
