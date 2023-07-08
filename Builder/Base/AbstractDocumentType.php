<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Exception;
use Goomento\PageBuilder\Builder\Controls\Groups\BackgroundGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\DataHelper;

abstract class AbstractDocumentType extends AbstractDocument
{
    /**
     * @return array|mixed
     */
    protected static function getEditorPanelCategories()
    {
        return DataHelper::arrayInject(
            parent::getEditorPanelCategories(),
            'theme-elements',
            [
                'theme-elements-single' => [
                    'title' => __('Single'),
                    'active' => false,
                ],
            ]
        );
    }


    public function getCssWrapperSelector()
    {
        return 'div[data-gmt-id="' . $this->getModel()->getOriginContent()->getId() . '"]';
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        parent::registerControls();
        self::registerStyleControls($this);
    }

    /**
     * @param AbstractDocument $document
     * @throws Exception
     */
    public static function registerStyleControls($document)
    {
        $document->startControlsSection(
            'section_page_style',
            [
                'label' => __('Body Style'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $document->addGroupControl(
            BackgroundGroup::NAME,
            [
                'name'  => 'background'
            ]
        );

        $document->addResponsiveControl(
            'padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $document->endControlsSection();

        Controls::addExtendControls($document);
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = '';
        $config['type'] = 'page';

        return $config;
    }
}
