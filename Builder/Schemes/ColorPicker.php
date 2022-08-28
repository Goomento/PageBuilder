<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Schemes;

class ColorPicker extends Color
{
    const NAME = 'color-picker';

    /**
     * 5th color scheme.
     */
    const COLOR_5 = '5';

    /**
     * 6th color scheme.
     */
    const COLOR_6 = '6';

    /**
     * 7th color scheme.
     */
    const COLOR_7 = '7';

    /**
     * 9th color scheme.
     */
    const COLOR_8 = '8';

    /**
     * Get color picker scheme description.
     *
     * Retrieve the color picker scheme description.
     *
     *
     * @return string Color picker scheme description.
     */

    public static function getDescription()
    {
        return __('Choose which colors appear in the editor\'s color picker. This makes accessing the colors you chose for the site much easier.');
    }

    /**
     * Get default color picker scheme.
     *
     * Retrieve the default color picker scheme.
     *
     *
     * @return array Default color picker scheme.
     */
    public function getDefaultScheme()
    {
        return array_replace(
            parent::getDefaultScheme(),
            [
                self::COLOR_5 => '#5b95c1',
                self::COLOR_6 => '#23a455',
                self::COLOR_7 => '#000',
                self::COLOR_8 => '#fff',
            ]
        );
    }

    /**
     * Get color picker scheme titles.
     *
     * Retrieve the color picker scheme titles.
     *
     *
     * @return array Color picker scheme titles.
     */
    public function getSchemeTitles()
    {
        return [];
    }

    /**
     * Init system color picker schemes.
     *
     * Initialize the system color picker schemes.
     *
     *
     * @return array System color picker schemes.
     */
    protected function _initSystemSchemes()
    {
        $schemes = parent::_initSystemSchemes();

        $additionalSchemes = [
            'joker' => [
                'items' => [
                    self::COLOR_5 => '#4b4646',
                    self::COLOR_6 => '#e2e2e2',
                ],
            ],
            'ocean' => [
                'items' => [
                    self::COLOR_5 => '#154d80',
                    self::COLOR_6 => '#8c8c8c',
                ],
            ],
            'royal' => [
                'items' => [
                    self::COLOR_5 => '#ac8e4d',
                    self::COLOR_6 => '#e2cea1',
                ],
            ],
            'violet' => [
                'items' => [
                    self::COLOR_5 => '#9c9ea6',
                    self::COLOR_6 => '#c184d0',
                ],
            ],
            'sweet' => [
                'items' => [
                    self::COLOR_5 => '#41aab9',
                    self::COLOR_6 => '#ffc72f',
                ],
            ],
            'urban' => [
                'items' => [
                    self::COLOR_5 => '#aa4039',
                    self::COLOR_6 => '#94dbaf',
                ],
            ],
            'earth' => [
                'items' => [
                    self::COLOR_5 => '#aa6666',
                    self::COLOR_6 => '#efe5d9',
                ],
            ],
            'river' => [
                'items' => [
                    self::COLOR_5 => '#7b8c93',
                    self::COLOR_6 => '#eb6d65',
                ],
            ],
            'pastel' => [
                'items' => [
                    self::COLOR_5 => '#f5a46c',
                    self::COLOR_6 => '#6e6f71',
                ],
            ],
        ];

        $schemes = array_replace_recursive($schemes, $additionalSchemes);

        foreach ($schemes as & $scheme) {
            $scheme['items'] += [
                self::COLOR_7 => '#000',
                self::COLOR_8 => '#fff',
            ];
        }

        return $schemes;
    }

    /**
     * Get system color picker schemes to print.
     *
     * Retrieve the system color picker schemes
     *
     *
     * @return array The system color picker schemes.
     */
    protected function _getSystemSchemesToPrint()
    {
        $schemes = $this->getSystemSchemes();

        $itemsToPrint = [
            self::COLOR_1,
            self::COLOR_5,
            self::COLOR_2,
            self::COLOR_3,
            self::COLOR_6,
            self::COLOR_4,
        ];

        $itemsToPrint = array_flip($itemsToPrint);

        foreach ($schemes as $schemeKey => $scheme) {
            $schemes[ $schemeKey ]['items'] = array_replace($itemsToPrint, array_intersect_key($scheme['items'], $itemsToPrint));
        }

        return $schemes;
    }

    /**
     * Get current color picker scheme title.
     *
     * Retrieve the current color picker scheme title.
     *
     *
     * @return string The current color picker scheme title.
     */
    protected function _getCurrentSchemeTitle()
    {
        return __('Color Picker');
    }
}
