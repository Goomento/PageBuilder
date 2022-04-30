<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

use Goomento\PageBuilder\Builder\Base\AbstractDocumentType;
use Goomento\PageBuilder\Builder\Managers\Controls;

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
        self::registerLayoutControl($this);
    }

    /**
     * @param AbstractDocumentType $section
     */
    public static function registerLayoutControl(AbstractDocumentType $section)
    {
        $section->startInjection([
            'of' => 'is_active',
            'fallback' => [
                'of' => 'title',
            ],
        ]);

        $section->addControl('layout', [
            'label' => __('Preview Layout'),
            'type' => Controls::SELECT,
            'default' => $section->getModel()->getSetting('layout') ?: 'pagebuilder_content_1column',
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
