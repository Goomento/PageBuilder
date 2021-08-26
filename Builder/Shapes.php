<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticAssets;

/**
 * Class Shapes
 * @package Goomento\PageBuilder\Builder
 */
class Shapes
{

    /**
     * The exclude filter.
     */
    const FILTER_EXCLUDE = 'exclude';

    /**
     * The include filter.
     */
    const FILTER_INCLUDE = 'include';

    /**
     * Shapes.
     *
     * Holds the list of supported shapes.
     * @var array A list of supported shapes.
     */
    private static $shapes;

    /**
     * Get shapes.
     *
     * Retrieve a shape from the lists of supported shapes. If no shape specified
     * it will return all the supported shapes.
     *
     *
     * @param array $shape Optional. Specific shape. Default is `null`.
     *
     * @return array The specified shape or a list of all the supported shapes.
     */
    public static function getShapes($shape = null)
    {
        if (null === self::$shapes) {
            self::initShapes();
        }

        if ($shape) {
            return self::$shapes[$shape] ?? null;
        }

        return self::$shapes;
    }

    /**
     * Filter shapes.
     *
     * Retrieve shapes filtered by a specific condition, from the list of
     * supported shapes.
     *
     *
     * @param string $by     Specific condition to filter by.
     * @param string $filter Optional. Comparison condition to filter by.
     *                       Default is `include`.
     *
     * @return array A list of filtered shapes.
     */
    public static function filterShapes($by, $filter = self::FILTER_INCLUDE)
    {
        return array_filter(
            self::getShapes(),
            function ($shape) use ($by, $filter) {
                return self::FILTER_INCLUDE === $filter xor empty($shape[ $by ]);
            }
        );
    }

    /**
     * Get shape path.
     *
     * For a given shape, retrieve the file path.
     *
     *
     * @param string $shape       The shape.
     * @param bool   $is_negative Optional. Whether the file name is negative or
     *                            not. Default is `false`.
     *
     * @return string Shape file path.
     */
    public static function getShapePath($shape, $is_negative = false)
    {
        if (isset(self::$shapes[ $shape ]) && isset(self::$shapes[ $shape ]['path'])) {
            return self::$shapes[ $shape ]['path'];
        }

        $file_name = $shape;

        if ($is_negative) {
            $file_name .= '-negative';
        }

        return StaticAssets::getModulePath('Goomento_PageBuilder', 'view') . '/base/web/shapes/' . $file_name . '.svg';
    }

    /**
     * Init shapes.
     *
     * Set the supported shapes.
     *
     */
    private static function initShapes()
    {
        $native_shapes = [
            'mountains' => [
                'title' => __('Mountains'),
                'has_flip' => true,
            ],
            'drops' => [
                'title' => __('Drops'),
                'has_negative' => true,
                'has_flip' => true,
                'height_only' => true,
            ],
            'clouds' => [
                'title' => __('Clouds'),
                'has_negative' => true,
                'has_flip' => true,
                'height_only' => true,
            ],
            'zigzag' => [
                'title' => __('Zigzag'),
            ],
            'pyramids' => [
                'title' => __('Pyramids'),
                'has_negative' => true,
                'has_flip' => true,
            ],
            'triangle' => [
                'title' => __('Triangle'),
                'has_negative' => true,
            ],
            'triangle-asymmetrical' => [
                'title' => __('Triangle Asymmetrical'),
                'has_negative' => true,
                'has_flip' => true,
            ],
            'tilt' => [
                'title' => __('Tilt'),
                'has_flip' => true,
                'height_only' => true,
            ],
            'opacity-tilt' => [
                'title' => __('Tilt Opacity'),
                'has_flip' => true,
            ],
            'opacity-fan' => [
                'title' => __('Fan Opacity'),
            ],
            'curve' => [
                'title' => __('Curve'),
                'has_negative' => true,
            ],
            'curve-asymmetrical' => [
                'title' => __('Curve Asymmetrical'),
                'has_negative' => true,
                'has_flip' => true,
            ],
            'waves' => [
                'title' => __('Waves'),
                'has_negative' => true,
                'has_flip' => true,
            ],
            'wave-brush' => [
                'title' => __('Waves Brush'),
                'has_flip' => true,
            ],
            'waves-pattern' => [
                'title' => __('Waves Pattern'),
                'has_flip' => true,
            ],
            'arrow' => [
                'title' => __('Arrow'),
                'has_negative' => true,
            ],
            'split' => [
                'title' => __('Split'),
                'has_negative' => true,
            ],
            'book' => [
                'title' => __('Book'),
                'has_negative' => true,
            ],
        ];

        self::$shapes = array_merge($native_shapes, self::getAdditionalShapes());
    }

    /**
     * Get Additional Shapes
     *
     * Used to add custom shapes to goomento.
     *
     *
     * @return array
     */
    private static function getAdditionalShapes()
    {
        static $additional_shapes = null;

        if (null !== $additional_shapes) {
            return $additional_shapes;
        }
        $additional_shapes = [];
        /**
         * Additional shapes.
         *
         * Filters the shapes used by SagoTheme to add additional shapes.
         *
         *
         * @param array $additional_shapes Additional SagoTheme shapes.
         */
        return Hooks::applyFilters('pagebuilder/shapes/additional_shapes', $additional_shapes);
    }

    /**
     * Get Additional Shapes For Config
     *
     * Used to set additional shape paths for editor
     *
     *
     * @return array|bool
     */
    public static function getAdditionalShapesForConfig()
    {
        $additional_shapes = self::getAdditionalShapes();
        if (empty($additional_shapes)) {
            return false;
        }

        $additional_shapes_config = [];
        foreach ($additional_shapes as $shape_name => $shape_settings) {
            if (! isset($shape_settings['url'])) {
                continue;
            }
            $additional_shapes_config[ $shape_name ] = $shape_settings['url'];
        }

        if (empty($additional_shapes_config)) {
            return false;
        }

        return $additional_shapes_config;
    }
}
