<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

use Goomento\PageBuilder\Builder\Base\AbstractDocumentType;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class Section extends AbstractDocumentType
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'section';
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        parent::registerControls();
        self::registerLayoutControl($this, 'pagebuilder_content_empty');
    }

    /**
     * @param AbstractDocumentType $section
     * @param string $defaultLayout
     * @throws BuilderException
     */
    public static function registerLayoutControl(
        AbstractDocumentType $section,
        string $defaultLayout = 'pagebuilder_content_fullwidth'
    ) {
        $section->startInjection([
            'of' => 'document_settings'
        ]);

        $section->addControl('layout', [
            'label' => __('Preview Layout'),
            'type' => Controls::SELECT,
            'default' => $section->getModel()->getSetting('layout') ?: $defaultLayout,
            'options' => [
                'pagebuilder_content_1column' => __('1-Column'),
                'pagebuilder_content_fullwidth' => __('Full width'),
                'pagebuilder_content_empty' => __('Empty'),
            ],
        ]);

        $section->endInjection();
    }

    /**
     * @inheritDoc
     */
    public static function getTitle()
    {
        return __('Section');
    }
}
