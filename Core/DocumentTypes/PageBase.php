<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\DocumentTypes;

use Exception;
use Goomento\PageBuilder\Builder\Controls\Groups\Background;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Utils;
use Goomento\PageBuilder\Core\Base\Document;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use ReflectionException;

/**
 * Class PageBase
 * @package Goomento\PageBuilder\Core\DocumentTypes
 */
abstract class PageBase extends Document
{


    public static function getProperties()
    {
        return parent::getProperties();
    }


    protected static function getEditorPanelCategories()
    {
        return Utils::arrayInject(
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
        return 'div[data-gmt-id="' . $this->getMainId() . '"]';
    }


    protected function registerControls()
    {
        parent::registerControls();

        self::registerHideTitleControl($this);

        self::registerPostFieldsControl($this);

        self::registerStyleControls($this);
    }

    /**
     * @param Document $document
     */
    public static function registerHideTitleControl($document)
    {
        $page_title_selector = SettingsManager::getSettingsManagers('general')->getSettingModel($document->getContentModel()->getId())->getSettings('goomento_page_title_selector');

        if (! $page_title_selector) {
            $page_title_selector = 'h1.entry-title';
        }

        $page_title_selector .= ', .gmt-page-title';

        $document->startInjection([
            'of' => 'status',
            'fallback' => [
                'of' => 'title',
            ],
        ]);

        $document->endInjection();
    }

    /**
     * @param Document $document
     * @throws ReflectionException|Exception
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
            Background::getType(),
            [
                'name'  => 'background',
                'fields_options' => [
                    'image' => [
                        // Currently isn't supported.
                        'dynamic' => [
                            'active' => false,
                        ],
                    ],
                ],
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

        StaticObjectManager::get(Controls::class)->addCustomCssControls($document);
    }

    /**
     * @param Document $document
     */
    public static function registerPostFieldsControl($document)
    {
        $document->startInjection([
            'of' => 'status',
            'fallback' => [
                'of' => 'title',
            ],
        ]);

        $document->endInjection();
    }

    /**
     *
     * @param array $data
     *
     * @throws Exception
     */
    public function __construct(array $data = [])
    {
        if ($data) {
            $data['settings']['template'] = 'default';
        }

        parent::__construct($data);
    }

    protected function getRemoteLibraryConfig()
    {
        $config = parent::getRemoteLibraryConfig();

        $config['category'] = '';
        $config['type'] = 'page';

        return $config;
    }
}
