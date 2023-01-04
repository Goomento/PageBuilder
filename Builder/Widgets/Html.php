<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class Html extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'html';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/html.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('HTML');
    }

    /**
     * @inheirtDoc
     */
    public function getCategories()
    {
        return ['basic'];
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-code';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'html', 'code' ];
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
        {{{ settings.html }}}
        <?php
    }
}
