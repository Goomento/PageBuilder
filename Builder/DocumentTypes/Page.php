<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

use Goomento\PageBuilder\Builder\Base\AbstractDocumentType;
use Goomento\PageBuilder\Builder\Managers\Controls;

class Page extends AbstractDocumentType
{
    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        parent::registerControls();
        self::registerTemplateControl($this);
    }

    /**
     * @param AbstractDocumentType $page
     * @throws \Exception
     */
    public static function registerTemplateControl(AbstractDocumentType $page)
    {
        $page->startInjection([
            'of' => 'status',
            'fallback' => [
                'of' => 'title',
            ],
        ]);

        $page->addControl('layout', [
            'label' => __('Layout'),
            'type' => Controls::SELECT,
            'default' => '1column',
            'options' => [
                '1column' => __('1-Column'),
                'fullwidth' => __('Full width'),
                'empty' => __('Empty'),
            ],
        ]);

        $page->addControl(
            'layout_warning',
            [
                'type' => Controls::RAW_HTML,
                'raw' => __('Note: The following layout only use for Preview purpose. For CMS content, use Magento layout instead.'),
                'content_classes' => 'gmt-panel-alert gmt-panel-alert-warning',
            ]
        );

        $page->endInjection();
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'page';
    }

    /**
     * @inheritDoc
     */
    public static function getTitle()
    {
        return __('Page');
    }
}
