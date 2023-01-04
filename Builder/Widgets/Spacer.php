<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class Spacer extends AbstractWidget
{

    const NAME = 'spacer';

    protected $template = 'Goomento_PageBuilder::widgets/spacer.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Spacer');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-arrows-alt-v';
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
        return [ 'space' ];
    }

    /**
     * @inheritDoc
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
            'spacer_space',
            [
                'label' => __('Space'),
                'type' => Controls::SLIDER,
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

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
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
