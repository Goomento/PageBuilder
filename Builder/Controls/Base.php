<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Core\Base\BaseObject;

/**
 * Class Base
 * @package Goomento\PageBuilder\Builder\Controls
 */
abstract class Base extends BaseObject
{

    /**
     * Base settings.
     *
     * Holds all the base settings of the control.
     *
     *
     * @var array
     */
    private $_base_settings = [
        'label' => '',
        'description' => '',
        'show_label' => true,
        'label_block' => false,
        'separator' => 'default',
    ];

    /**
     * Get features.
     *
     * Retrieve the list of all the available features. Currently SagoTheme uses only
     * the `UI` feature.
     *
     *
     * @return array Features array.
     */
    public static function getFeatures()
    {
        return [];
    }

    /**
     * Get control type.
     *
     * Retrieve the control type.
     *
     * @abstract
     */
    abstract public function getType();

    /**
     * Control base constructor.
     *
     * Initializing the control base class.
     *
     */
    public function __construct()
    {
        $this->setSettings(array_merge($this->_base_settings, $this->getDefaultSettings()));

        $this->setSettings('features', static::getFeatures());
    }

    /**
     * Enqueue control scripts and styles.
     *
     * Used to register and enqueue custom scripts and styles used by the control.
     *
     */
    public function enqueue()
    {
    }

    /**
     * Control content template.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     * Note that the content template is wrapped by Base_Control::printTemplate().
     *
     * @abstract
     */
    abstract public function contentTemplate();

    /**
     * Print control template.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    final public function printTemplate()
    {
        ?>
		<script type="text/html" id="tmpl-gmt-control-<?= $this->getType() ?>-content">
			<div class="gmt-control-content">
				<?php $this->contentTemplate(); ?>
			</div>
		</script>
		<?php
    }

    /**
     * Get default control settings.
     *
     * Retrieve the default settings of the control. Used to return the default
     * settings while initializing the control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [];
    }
}
