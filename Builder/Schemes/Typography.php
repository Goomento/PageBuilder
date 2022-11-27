<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Schemes;

use Goomento\PageBuilder\Builder\Base\AbstractSchema;

class Typography extends AbstractSchema
{
    const NAME = 'typography';

    /**
     * 1st typography scheme.
     */
    const TYPOGRAPHY_1 = '1';

    /**
     * 2nd typography scheme.
     */
    const TYPOGRAPHY_2 = '2';

    /**
     * 3rd typography scheme.
     */
    const TYPOGRAPHY_3 = '3';

    /**
     * 4th typography scheme.
     */
    const TYPOGRAPHY_4 = '4';

    /**
     * Get typography scheme title.
     *
     * Retrieve the typography scheme title.
     *
     *
     * @return string Typography scheme title.
     */
    public function getTitle()
    {
        return __('Typography');
    }

    /**
     * Get typography scheme disabled title.
     *
     * Retrieve the typography scheme disabled title.
     *
     *
     * @return string Typography scheme disabled title.
     */
    public function getDisabledTitle()
    {
        return __('Default Fonts');
    }

    /**
     * Get typography scheme titles.
     *
     * Retrieve the typography scheme titles.
     *
     *
     * @return array Typography scheme titles.
     */
    public function getSchemeTitles()
    {
        return [
            self::TYPOGRAPHY_1 => __('Primary Headline'),
            self::TYPOGRAPHY_2 => __('Secondary Headline'),
            self::TYPOGRAPHY_3 => __('Body Text'),
            self::TYPOGRAPHY_4 => __('Accent Text'),
        ];
    }

    /**
     * Get default typography scheme.
     *
     * Retrieve the default typography scheme.
     *
     *
     * @return array Default typography scheme.
     */
    public function getDefaultScheme()
    {
        return [
            self::TYPOGRAPHY_1 => [
                'font_family' => 'Open Sans',
                'font_weight' => '600',
            ],
            self::TYPOGRAPHY_2 => [
                'font_family' => 'Open Sans',
                'font_weight' => '400',
            ],
            self::TYPOGRAPHY_3 => [
                'font_family' => 'Open Sans',
                'font_weight' => '400',
            ],
            self::TYPOGRAPHY_4 => [
                'font_family' => 'Open Sans',
                'font_weight' => '500',
            ],
        ];
    }

    /**
     * Init system typography schemes.
     *
     * Initialize the system typography schemes.
     *
     *
     * @return array System typography schemes.
     */
    protected function _initSystemSchemes()
    {
        return [];
    }

    /**
     * Print typography scheme content template.
     *
     * Used to generate the HTML in the editor using Underscore JS template. The
     * variables for the class are available using `data` JS object.
     *
     */
    public function printTemplateContent()
    {
        ?>
        <div class="gmt-panel-scheme-items"></div>
        <?php
    }
}
