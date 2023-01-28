<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class DeprecatedNotice extends AbstractControlUi
{

    const NAME = 'deprecated_notice';

    /**
     * Render deprecated notice control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <# if ( data.label ) { #>
        <span class="gmt-control-title">{{{ data.label }}}</span>
        <#
        }
        let notice = jQuery.mage.__( 'The <strong>%1$s</strong> widget has been deprecated since %2$s %3$s.', [ data.widget, data.plugin, data.since ] );
        if ( data.replacement ) {
            notice += '<br>' + jQuery.mage.__( 'It has been replaced by <strong>%1$s</strong>', [ data.replacement ] );
        }
        if ( data.last ) {
            notice += '<br>' + jQuery.mage.__( 'Note that %1$s will be completely removed once %2$s %3$s is released', [ data.widget, data.plugin, data.last ] );
        }
        #>
        <div class="gmt-control-deprecated-notice gmt-panel-alert gmt-panel-alert-warning">{{{ notice }}}</div>
        <?php
    }

    /**
     * Get deprecated-notice control default settings.
     *
     * Retrieve the default settings of the deprecated notice control. Used to return the
     * default settings while initializing the deprecated notice control.
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'widget' => '', // Widgets name
            'since' => '', // Plugin version widget was deprecated
            'last' => '', // Plugin version in which the widget will be removed
            'plugin' => '', // Plugin's title
            'replacement' => '', // AbstractWidget replacement
        ];
    }
}
