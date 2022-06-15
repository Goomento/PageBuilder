<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

use Goomento\PageBuilder\Builder\Base\AbstractDocumentType;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Model\Content;

class Page extends AbstractDocumentType
{
    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        parent::registerControls();
        self::registerStatusControl($this);
    }

    /**
     * @param AbstractDocumentType $page
     * @throws \Exception
     */
    public static function registerStatusControl(AbstractDocumentType $page)
    {
        $page->startInjection([
            'of' => 'title'
        ]);

        $page->addControl('layout', [
            'label' => __('Layout'),
            'type' => Controls::SELECT,
            'default' => $page->getModel()->getSetting('layout') ?: 'pagebuilder_content_fullwidth',
            'options' => [
                'pagebuilder_content_1column' => __('1-Column'),
                'pagebuilder_content_fullwidth' => __('Full width'),
                'pagebuilder_content_empty' => __('Empty'),
            ],
        ]);

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
