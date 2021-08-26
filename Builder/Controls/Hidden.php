<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class Hidden
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Hidden extends BaseData
{

    /**
     * Get hidden control type.
     *
     * Retrieve the control type, in this case `hidden`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'hidden';
    }

    /**
     * Render hidden control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
		<input type="hidden" data-setting="{{{ data.name }}}" />
		<?php
    }
}
