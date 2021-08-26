<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Utils;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Core\Common\Modules\Ajax\Module as Ajax;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;
use Goomento\PageBuilder\Helper\Theme;

/**
 * Class Icons
 * @package Goomento\PageBuilder\Builder\Managers
 */
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

        $shared_styles = [];

        foreach ($config as $type => $icon_type) {
            if (! isset($icon_type['url'])) {
                continue;
            }
            $dependencies = [];
            if (! empty($icon_type['enqueue'])) {
                foreach ((array) $icon_type['enqueue'] as $font_css_url) {
                    if (! in_array($font_css_url, array_keys($shared_styles))) {
                        $style_handle = 'goomento-icons-shared-' . count($shared_styles);
                        Theme::registerStyle(
                            $style_handle,
                            $font_css_url,
                            [],
                            $icon_type['ver']
                        );
                        $shared_styles[ $font_css_url ] = $style_handle;
                    }
                    $dependencies[] = $shared_styles[ $font_css_url ];
                }
            }

            Theme::registerStyle(
                'goomento-icons-' . $icon_type['name'],
                $icon_type['url'],
                $dependencies,
                $icon_type['ver']
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
        self::$tabs = Hooks::applyFilters('pagebuilder/icons_manager/native', [
            'fa-regular' => [
                'name' => 'fa-regular',
                'label' => __('Font Awesome - Regular'),
                'prefix' => 'fa-',
                'displayPrefix' => 'far',
                'labelIcon' => 'fab fa-font-awesome-alt',
                'ver' => '5.9.0',
                'fetchJson' => self::getFaAssetUrl('regular', 'json', false),
                'native' => true,
            ],
            'fa-solid' => [
                'name' => 'fa-solid',
                'label' => __('Font Awesome - Solid'),
                'prefix' => 'fa-',
                'displayPrefix' => 'fas',
                'labelIcon' => 'fab fa-font-awesome',
                'ver' => '5.9.0',
                'fetchJson' => self::getFaAssetUrl('solid', 'json', false),
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
                'fetchJson' => self::getFaAssetUrl('brands', 'json', false),
                'native' => true,
            ],
        ]);
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
        $additional_tabs = Hooks::applyFilters('pagebuilder/icons_manager/additional_tabs', []);
        return array_merge(self::$tabs, $additional_tabs);
    }

    /**
     * @param $filename
     * @param string $ext_type
     * @param bool $add_suffix
     * @return string
     */
    private static function getFaAssetUrl($filename, $ext_type = 'css', $add_suffix = true)
    {
        static $is_test_mode = null;
        if (null === $is_test_mode) {
            $is_test_mode = !!Configuration::DEBUG;
        }
        $url = 'Goomento_PageBuilder/lib/font-awesome/' . $ext_type . '/' . $filename;
        if (! $is_test_mode && $add_suffix) {
            $url .= '.min';
        }
        $url .= '.' . $ext_type;
        return StaticUrlBuilder::urlStaticBuilder($url);
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
     * @return false|mixed|string'
     */
    private static function renderIconHtml($icon, $attributes = [], $tag = 'i')
    {
        $icon_types = self::getIconManagerTabs();
        if (isset($icon_types[ $icon['library'] ]['render_callback']) && is_callable($icon_types[ $icon['library'] ]['render_callback'])) {
            return call_user_func_array($icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ]);
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
        return '<' . $tag . ' ' . Utils::renderHtmlAttributes($attributes) . '></' . $tag . '>';
    }

    /**
     * Render Icon
     *
     * @param array $icon             Icon Type, Icon value
     * @param array $attributes       Icon HTML Attributes
     * @param string $tag             Icon HTML tag, defaults to <i>
     *
     * @return mixed|string
     */
    public static function renderIcon($icon, $attributes = [], $tag = 'i')
    {
        if (empty($icon['library'])) {
            return false;
        }

        $output = self::renderIconHtml($icon, $attributes, $tag);
        echo $output;
        return true;
    }

    public function enqueueFontawesomeCss()
    {
        Theme::enqueueStyle('fontawesome');
    }

    public function registerAjaxActions(Ajax $ajax)
    {
    }

    /**
     * Icons Manager constructor
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/editor/after_enqueue_styles', [ $this, 'enqueueFontawesomeCss' ]);
        Hooks::addAction('pagebuilder/frontend/after_enqueue_styles', [ $this, 'enqueueFontawesomeCss' ]);

        Hooks::addAction('pagebuilder/frontend/after_register_styles', [ $this, 'registerStyles' ]);

        // Ajax.
        Hooks::addAction('pagebuilder/ajax/register_actions', [ $this, 'registerAjaxActions' ]);
    }
}
