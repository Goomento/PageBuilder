<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class Icons
{
    /**
     * Tabs.
     *
     * Holds the list of all the tabs.
     *
     * @var array
     */
    private static $tabs;

    /**
     * register styles
     *
     * Used to register all icon types stylesheets so they could be enqueued later by widgets
     */
    public function registerStyles()
    {
        $config = self::getIconManagerTabsConfig();

        $sharedStyles = [];

        foreach ($config as $type => $iconType) {
            if (!isset($iconType['url'])) {
                continue;
            }
            $dependencies = [];
            if (!empty($iconType['enqueue'])) {
                foreach ((array) $iconType['enqueue'] as $fontCssUrl) {
                    if (! in_array($fontCssUrl, array_keys($sharedStyles))) {
                        $styleHandle = 'goomento-icons-shared-' . count($sharedStyles);
                        ThemeHelper::registerStyle(
                            $styleHandle,
                            $fontCssUrl,
                            [],
                            $iconType['ver']
                        );
                        $sharedStyles[ $fontCssUrl ] = $styleHandle;
                    }
                    $dependencies[] = $sharedStyles[ $fontCssUrl ];
                }
            }

            ThemeHelper::registerStyle(
                'goomento-icons-' . $iconType['name'],
                $iconType['url'],
                $dependencies,
                $iconType['ver']
            );
        }
    }

    /**
     * Init Tabs
     *
     * Initiate Icon Manager Tabs.
     *
     */
    private static function initTabs()
    {
        self::$tabs = HooksHelper::applyFilters('pagebuilder/icons_manager/native', [
            'fa-regular' => [
                'name' => 'fa-regular',
                'label' => __('Font Awesome - Regular'),
                'prefix' => 'fa-',
                'displayPrefix' => 'far',
                'labelIcon' => 'fab fa-font-awesome-alt',
                'ver' => '5.9.0',
                'fetchJson' => self::getFaAssetUrl('regular'),
                'native' => true,
            ],
            'fa-solid' => [
                'name' => 'fa-solid',
                'label' => __('Font Awesome - Solid'),
                'prefix' => 'fa-',
                'displayPrefix' => 'fas',
                'labelIcon' => 'fab fa-font-awesome',
                'ver' => '5.9.0',
                'fetchJson' => self::getFaAssetUrl('solid'),
                'native' => true,
            ],
            'fa-brands' => [
                'name' => 'fa-brands',
                'label' => __('Font Awesome - Brands'),
                'enqueue' => [],
                'prefix' => 'fa-',
                'displayPrefix' => 'fab',
                'labelIcon' => 'fab fa-font-awesome-flag',
                'ver' => '5.9.0',
                'fetchJson' => self::getFaAssetUrl('brands'),
                'native' => true,
            ],
        ])->getResult();
    }

    /**
     * Get Icon Manager Tabs
     * @return array
     */
    public static function getIconManagerTabs()
    {
        if (! self::$tabs) {
            self::initTabs();
        }
        $additionalTabs = HooksHelper::applyFilters('pagebuilder/icons_manager/additional_tabs', [])->getResult();
        return array_merge(self::$tabs, $additionalTabs);
    }

    /**
     * @param $filename
     * @return string
     */
    private static function getFaAssetUrl($filename)
    {
        return UrlBuilderHelper::getAssetUrlWithParams(sprintf('Goomento_PageBuilder/lib/font-awesome/json/%s.json', $filename));
    }

    /**
     * @return array
     */
    public static function getIconManagerTabsConfig()
    {
        $tabs = [
            'all' => [
                'name' => 'all',
                'label' => __('All Icons'),
                'labelIcon' => 'fas fa-filter',
                'native' => true,
            ],
        ];

        return array_values(array_merge($tabs, self::getIconManagerTabs()));
    }

    /**
     * @param $icon
     * @param array $attributes
     * @param string $tag
     * @return false|mixed|string
     */
    private static function renderIconHtml($icon, $attributes = [], $tag = 'i')
    {
        $iconTypes = self::getIconManagerTabs();
        if (isset($iconTypes[ $icon['library'] ]['render_callback']) && is_callable($iconTypes[ $icon['library'] ]['render_callback'])) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            return call_user_func($iconTypes[ $icon['library'] ]['render_callback'], $icon, $attributes, $tag);
        }

        if (empty($attributes['class'])) {
            $attributes['class'] = $icon['value'];
        } else {
            if (is_array($attributes['class'])) {
                $attributes['class'][] = $icon['value'];
            } else {
                $attributes['class'] .= ' ' . $icon['value'];
            }
        }
        return '<' . $tag . ' ' . DataHelper::renderHtmlAttributes($attributes) . '></' . $tag . '>';
    }

    /**
     * Render Icon
     *
     * @param array $icon             Icon Type, Icon value
     * @param array $attributes       Icon HTML Attributes
     * @param string $tag             Icon HTML tag, defaults to <i>
     *
     * @return bool
     */
    public static function renderIcon($icon, $attributes = [], $tag = 'i')
    {
        if (empty($icon['library'])) {
            return false;
        }

        $output = self::renderIconHtml($icon, $attributes, $tag);

        // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
        echo $output;
        return true;
    }

    /**
     * Icons Manager constructor
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/frontend/after_register_styles', [ $this, 'registerStyles' ]);
    }
}
