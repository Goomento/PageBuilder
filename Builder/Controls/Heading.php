<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Heading extends AbstractControlUi
{
    const NAME = 'heading';

    /**
     * Get heading control default settings.
     *
     * Retrieve the default settings of the heading control. Used to return the
     * default settings while initializing the heading control.
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
        ];
    }

    /**
     * Render heading control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <div class="gmt-control-field">
            <h3 class="gmt-control-title">{{ data.label }}</h3>
        </div>
        <?php
    }
}
