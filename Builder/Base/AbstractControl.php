<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

abstract class AbstractControl extends AbstractBase
{
    const TYPE = 'control';

    const NAME = 'base';

    /**
     * Base settings.
     *
     * Holds all the base settings of the control.
     *
     *
     * @var array
     */
    private $baseSettings = [
        'label' => '',
        'description' => '',
        'show_label' => true,
        'label_block' => false,
        'separator' => 'default',
    ];

    /**
     * Get features.
     *
     * Retrieve the list of all the available features. Currently Goomento uses only
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
     * Control base constructor.
     *
     * Initializing the control base class.
     *
     */
    public function __construct()
    {
        $this->setSettings(array_merge($this->baseSettings, $this->getDefaultSettings()));

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
     * @return void
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
    public function printTemplate()
    {
        ?>
        <script type="text/html" id="tmpl-gmt-control-<?= $this->getName() ?>-content">
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
