<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Schemes;

use Goomento\PageBuilder\Builder\Base\AbstractSchema;
use Goomento\PageBuilder\Helper\EscaperHelper;

class Color extends AbstractSchema
{
    const NAME = 'color';

    /**
     * 1st color scheme.
     */
    const COLOR_1 = '1';

    /**
     * 2nd color scheme.
     */
    const COLOR_2 = '2';

    /**
     * 3rd color scheme.
     */
    const COLOR_3 = '3';

    /**
     * 4th color scheme.
     */
    const COLOR_4 = '4';

    /**
     * Get color scheme title.
     *
     * Retrieve the color scheme title.
     *
     *
     * @return string Color scheme title.
     */
    public function getTitle()
    {
        return __('Colors');
    }

    /**
     * Get color scheme disabled title.
     *
     * Retrieve the color scheme disabled title.
     *
     *
     * @return string Color scheme disabled title.
     */
    public function getDisabledTitle()
    {
        return __('Color Palettes');
    }

    /**
     * Get color scheme titles.
     *
     * Retrieve the color scheme titles.
     *
     *
     * @return array Color scheme titles.
     */
    public function getSchemeTitles()
    {
        return [
            self::COLOR_1 => __('Primary'),
            self::COLOR_2 => __('Secondary'),
            self::COLOR_3 => __('Text'),
            self::COLOR_4 => __('Accent'),
        ];
    }

    /**
     * Get default color scheme.
     *
     * Retrieve the default color scheme.
     *
     *
     * @return array Default color scheme.
     */
    public function getDefaultScheme()
    {
        return [
            self::COLOR_1 => '#1979c3',
            self::COLOR_2 => '#ff5501',
            self::COLOR_3 => '#333333',
            self::COLOR_4 => '#61ce70',
        ];
    }

    /**
     * Print color scheme content template.
     *
     * Used to generate the HTML in the editor using Underscore JS template. The
     * variables for the class are available using `data` JS object.
     *
     */
    public function printTemplateContent()
    {
        ?>
        <div class="gmt-panel-scheme-content gmt-panel-box">
            <div class="gmt-panel-heading">
                <div class="gmt-panel-heading-title"><?= $this->_getCurrentSchemeTitle(); ?></div>
            </div>
            <?php
            $description = static::getDescription();

            if ($description):
                ?>
                <div class="gmt-panel-scheme-description gmt-descriptor"><?= $description; ?></div>
            <?php endif; ?>
            <div class="gmt-panel-scheme-items gmt-panel-box-content"></div>
        </div>
        <div class="gmt-panel-scheme-colors-more-palettes gmt-panel-box">
            <div class="gmt-panel-heading">
                <div class="gmt-panel-heading-title"><?= __('More Palettes'); ?></div>
            </div>
            <div class="gmt-panel-box-content">
                <?php foreach ($this->_getSystemSchemesToPrint() as $schemeName => $scheme): ?>
                    <div class="gmt-panel-scheme-color-system-scheme" data-scheme-name="<?= EscaperHelper::escapeHtml($schemeName); ?>">
                        <div class="gmt-panel-scheme-color-system-items">
                            <?php foreach ($scheme['items'] as $colorValue): ?>
                                <div class="gmt-panel-scheme-color-system-item" style="background-color: <?= EscaperHelper::escapeHtml($colorValue); ?>;"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="gmt-title"><?= $scheme['title']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Init system color schemes.
     *
     * Initialize the system color schemes.
     *
     *
     * @return array System color schemes.
     */
    protected function _initSystemSchemes()
    {
        return [
            'joker' => [
                'title' => 'Joker',
                'items' => [
                    self::COLOR_1 => '#202020',
                    self::COLOR_2 => '#b7b4b4',
                    self::COLOR_3 => '#707070',
                    self::COLOR_4 => '#f6121c',
                ],
            ],
            'ocean' => [
                'title' => 'Ocean',
                'items' => [
                    self::COLOR_1 => '#1569ae',
                    self::COLOR_2 => '#b6c9db',
                    self::COLOR_3 => '#545454',
                    self::COLOR_4 => '#fdd247',
                ],
            ],
            'royal' => [
                'title' => 'Royal',
                'items' => [
                    self::COLOR_1 => '#d5ba7f',
                    self::COLOR_2 => '#902729',
                    self::COLOR_3 => '#ae4848',
                    self::COLOR_4 => '#302a8c',
                ],
            ],
            'violet' => [
                'title' => 'Violet',
                'items' => [
                    self::COLOR_1 => '#747476',
                    self::COLOR_2 => '#ebca41',
                    self::COLOR_3 => '#6f1683',
                    self::COLOR_4 => '#a43cbd',
                ],
            ],
            'sweet' => [
                'title' => 'Sweet',
                'items' => [
                    self::COLOR_1 => '#6ccdd9',
                    self::COLOR_2 => '#763572',
                    self::COLOR_3 => '#919ca7',
                    self::COLOR_4 => '#f12184',
                ],
            ],
            'urban' => [
                'title' => 'Urban',
                'items' => [
                    self::COLOR_1 => '#db6159',
                    self::COLOR_2 => '#3b3b3b',
                    self::COLOR_3 => '#7a7979',
                    self::COLOR_4 => '#2abf64',
                ],
            ],
            'earth' => [
                'title' => 'Earth',
                'items' => [
                    self::COLOR_1 => '#882021',
                    self::COLOR_2 => '#c48e4c',
                    self::COLOR_3 => '#825e24',
                    self::COLOR_4 => '#e8c12f',
                ],
            ],
            'river' => [
                'title' => 'River',
                'items' => [
                    self::COLOR_1 => '#8dcfc8',
                    self::COLOR_2 => '#565656',
                    self::COLOR_3 => '#50656e',
                    self::COLOR_4 => '#dc5049',
                ],
            ],
            'pastel' => [
                'title' => 'Pastel',
                'items' => [
                    self::COLOR_1 => '#f27f6f',
                    self::COLOR_2 => '#f4cd78',
                    self::COLOR_3 => '#a5b3c1',
                    self::COLOR_4 => '#aac9c3',
                ],
            ],
        ];
    }

    /**
     * Get system color schemes to print.
     *
     * Retrieve the system color schemes
     *
     *
     * @return array The system color schemes.
     */
    protected function _getSystemSchemesToPrint()
    {
        return $this->getSystemSchemes();
    }

    /**
     * Get current color scheme title.
     *
     * Retrieve the current color scheme title.
     *
     *
     * @return string The current color scheme title.
     */
    protected function _getCurrentSchemeTitle()
    {
        return __('Color Palette');
    }
}
