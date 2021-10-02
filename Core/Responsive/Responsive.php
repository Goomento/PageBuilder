<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Responsive;

use Goomento\PageBuilder\Core\Responsive\Files\Frontend;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticConfig;

/**
 * Class Responsive
 * @package Goomento\PageBuilder\Core\Responsive
 */
class Responsive
{

    /**
     * The SagoTheme breakpoint prefix.
     */
    const BREAKPOINT_OPTION_PREFIX = 'goomento_viewport_';

    /**
     * Default breakpoints.
     *
     * Holds the default responsive breakpoints.
     *
     *
     * @var array Default breakpoints.
     */
    private static $default_breakpoints = [
        'xs' => 0,
        'sm' => 480,
        'md' => 768,
        'lg' => 1025,
        'xl' => 1440,
        'xxl' => 1600,
    ];

    /**
     * Editable breakpoint keys.
     *
     * Holds the editable breakpoint keys.
     *
     *
     * @var array Editable breakpoint keys.
     */
    private static $editable_breakpoints_keys = [
        'md',
        'lg',
    ];

    /**
     * Get default breakpoints.
     *
     * Retrieve the default responsive breakpoints.
     *
     *
     * @return array Default breakpoints.
     */
    public static function getDefaultBreakpoints()
    {
        return self::$default_breakpoints;
    }

    /**
     * Get editable breakpoints.
     *
     * Retrieve the editable breakpoints.
     *
     *
     * @return array Editable breakpoints.
     */
    public static function getEditableBreakpoints()
    {
        return array_intersect_key(self::getBreakpoints(), array_flip(self::$editable_breakpoints_keys));
    }

    /**
     * Get breakpoints.
     *
     * Retrieve the responsive breakpoints.
     *
     *
     * @return array Responsive breakpoints.
     */
    public static function getBreakpoints()
    {
        return array_reduce(
            array_keys(self::$default_breakpoints),
            function ($new_array, $breakpoint_key) {
                if (! in_array($breakpoint_key, self::$editable_breakpoints_keys)) {
                    $new_array[ $breakpoint_key ] = self::$default_breakpoints[ $breakpoint_key ];
                } else {
                    $saved_option = StaticConfig::getOption(self::BREAKPOINT_OPTION_PREFIX . $breakpoint_key);

                    $new_array[ $breakpoint_key ] = $saved_option ? (int) $saved_option : self::$default_breakpoints[ $breakpoint_key ];
                }

                return $new_array;
            },
            []
        );
    }


    public static function hasCustomBreakpoints()
    {
        return ! ! array_diff(self::$default_breakpoints, self::getBreakpoints());
    }

    /**
     * @TODO make this later
     */
    public static function getStylesheetTemplatesPath()
    {
        return 'css/templates/';
    }


    public static function compileStylesheetTemplates()
    {
        foreach (self::getStylesheetTemplates() as $file_name => $template_path) {
            $file = new Frontend($file_name, $template_path);

            $file->update();
        }
    }


    private static function getStylesheetTemplates()
    {
        $templates_paths = glob(self::getStylesheetTemplatesPath() . '*.css');

        $templates = [];

        foreach ($templates_paths as $template_path) {
            $file_name = 'custom-' . basename($template_path);

            $templates[ $file_name ] = $template_path;
        }

        return Hooks::applyFilters('pagebuilder/core/responsive/get_stylesheet_templates', $templates);
    }
}
