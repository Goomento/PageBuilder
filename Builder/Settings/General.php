<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Settings;

use Goomento\PageBuilder\Builder\Managers\Controls as ControlManager;
use Goomento\PageBuilder\Builder\Base\AbstractSettings;
use Goomento\PageBuilder\Builder\Managers\GeneralSettings;

class General extends AbstractSettings
{
    const NAME = 'global-settings';

    /**
     * Get CSS wrapper selector.
     *
     * Retrieve the wrapper selector for the global settings model.
     *
     *
     * @return string CSS wrapper selector.
     */

    public function getCssWrapperSelector()
    {
        return '';
    }

    /**
     * Get panel page settings.
     *
     * Retrieve the panel setting for the global settings model.
     *
     *
     * @return array {
     *    Panel settings.
     *
     * @type string $title The panel title.
     * @type array  $menu  The panel menu.
     * }
     */
    public function getPanelPageSettings()
    {
        return [];
    }

    /**
     * Get controls list.
     *
     * Retrieve the global settings model controls list.
     *
     *
     * @return array Controls list.
     */
    public static function getControlsList()
    {
        return [
            ControlManager::TAB_STYLE => [
                'style' => [
                    'label' => __('Style'),
                    'controls' => [
                        'default_generic_fonts' => [
                            'label' => __('Default Generic Fonts'),
                            'type' => ControlManager::TEXT,
                            'default' => 'Sans-serif',
                            'description' => __('The list of fonts used if the chosen font is not available.'),
                            'label_block' => true,
                        ],
                        'space_between_widgets' => [
                            'label' => __('Widgets Space') . ' (px)',
                            'type' => ControlManager::NUMBER,
                            'min' => 0,
                            'placeholder' => '20',
                            'description' => __('Sets the default space between widgets (Default: 20)'),
                            'selectors' => [
                                '.gmt-widget:not(:last-child)' => 'margin-bottom: {{VALUE}}px',
                            ],
                        ],
                        'stretched_section_container' => [
                            'label' => __('Stretched Section Fit To'),
                            'type' => ControlManager::TEXT,
                            'placeholder' => 'body',
                            'description' => __('Enter parent element selector to which stretched sections will fit to (e.g. #primary / .wrapper / main etc). Leave blank to fit to page width.'),
                            'label_block' => true,
                            'frontend_available' => true,
                        ],
                        'page_title_selector' => [
                            'label' => __('Page Title Selector'),
                            'type' => ControlManager::TEXT,
                            'placeholder' => 'h1.entry-title',
                            'description' => __('Goomento lets you hide the page title. This works for themes that have "h1.entry-title" selector. If your theme\'s selector is different, please enter it above.'),
                            'label_block' => true,
                        ],
                    ],
                ],
            ],
            GeneralSettings::PANEL_TAB_LIGHTBOX => [
                'lightbox' => [
                    'label' => __('Lightbox'),
                    'controls' => [
                        'global_image_lightbox' => [
                            'label' => __('Image Lightbox'),
                            'type' => ControlManager::SWITCHER,
                            'default' => 'yes',
                            'description' => __('Open all image links in a lightbox popup window. The lightbox will automatically work on any link that leads to an image file.'),
                            'frontend_available' => true,
                        ],
                        'enable_lightbox_in_editor' => [
                            'label' => __('Enable In Editor'),
                            'type' => ControlManager::SWITCHER,
                            'default' => 'yes',
                            'frontend_available' => true,
                        ],
                        'lightbox_color' => [
                            'label' => __('Background Color'),
                            'type' => ControlManager::COLOR,
                            'selectors' => [
                                '.gmt-lightbox' => 'background-color: {{VALUE}}',
                            ],
                        ],
                        'lightbox_ui_color' => [
                            'label' => __('UI Color'),
                            'type' => ControlManager::COLOR,
                            'selectors' => [
                                '.gmt-lightbox .dialog-lightbox-close-button, .gmt-lightbox .gmt-swiper-button' => 'color: {{VALUE}}',
                            ],
                        ],
                        'lightbox_ui_color_hover' => [
                            'label' => __('UI Hover Color'),
                            'type' => ControlManager::COLOR,
                            'selectors' => [
                                '.gmt-lightbox .dialog-lightbox-close-button:hover, .gmt-lightbox .gmt-swiper-button:hover' => 'color: {{VALUE}}',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Register model controls.
     *
     * Used to add new controls to the global settings model.
     *
     */
    protected function registerControls()
    {
        $controlsList = self::getControlsList();

        foreach ($controlsList as $tabName => $sections) {
            foreach ($sections as $sectionName => $sectionData) {
                $this->startControlsSection(
                    $sectionName,
                    [
                        'label' => $sectionData['label'],
                        'tab' => $tabName,
                    ]
                );

                foreach ($sectionData['controls'] as $controlName => $controlData) {
                    $this->addControl($controlName, $controlData);
                }

                $this->endControlsSection();
            }
        }
    }
}
