<?php
/**
 * @package Goomento_BuilderWidgets
 * @link https://github.com/Goomento/BuilderWidgets
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class Block extends AbstractWidget
{
    const NAME = 'block';

    /**
     * @var string
     */
    protected $template = 'Goomento_PageBuilder::widgets/magento/block.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Magento Block');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fab fa-magento';
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
        return [ 'block', 'code' ];
    }

    /**
     * @inheritDoc
     */
    public function isReloadPreviewRequired()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_block',
            [
                'label' => __('Block'),
            ]
        );

        $this->addControl(
            'class',
            [
                'label' => __('Block class'),
                'type' => Controls::TEXT,
                'placeholder' => '\Magento\Framework\View\Element\Template',
                'default' => '',
            ]
        );

        $this->addControl(
            'template',
            [
                'label' => __('Template'),
                'type' => Controls::TEXT,
                'placeholder' => 'Your_Module::your_template.phtml',
                'default' => '',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    public function renderPlainContent()
    {
        // In plain mode, render without shortcode
        // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
        echo $this->getSettings('block');
    }
}
