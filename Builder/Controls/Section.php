<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Section extends AbstractControlUi
{
    const NAME = 'section';

    /**
     * Render section control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <div class="gmt-panel-heading">
            <div class="gmt-panel-heading-toggle gmt-section-toggle" data-collapse_id="{{ data.name }}">
                <i class="gmt-icon" aria-hidden="true"></i>
            </div>
            <div class="gmt-panel-heading-title gmt-section-title">{{{ data.label }}}</div>
        </div>
        <?php
    }

    /**
     * Get repeater control default settings.
     *
     * Retrieve the default settings of the repeater control. Used to return the
     * default settings while initializing the repeater control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'separator' => 'none',
        ];
    }
}
