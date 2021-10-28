<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

abstract class AbstractBaseTag extends ControlsStack
{

    /**
     * @abstract
     */
    abstract public function getCategories();

    /**
     * @abstract
     */
    abstract public function getGroup();

    /**
     * @abstract
     */
    abstract public function getTitle();

    /**
     * @abstract
     *
     * @param array $options
     */
    abstract public function getContent(array $options = []);

    /**
     * @abstract
     */
    abstract public function getContentType();


    public function getPanelTemplateSettingKey()
    {
        return '';
    }


    public function isSettingsRequired()
    {
        return false;
    }


    public function getEditorConfig()
    {
        ob_start();

        $this->printPanelTemplate();

        $panel_template = ob_get_clean();

        return [
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'panel_template' => $panel_template,
            'categories' => $this->getCategories(),
            'group' => $this->getGroup(),
            'controls' => $this->getControls(),
            'content_type' => $this->getContentType(),
            'settings_required' => $this->isSettingsRequired(),
        ];
    }


    public function printPanelTemplate()
    {
        $panel_template_setting_key = $this->getPanelTemplateSettingKey();

        if (!$panel_template_setting_key) {
            return;
        } ?><#
		var key = <?= EscaperHelper::escapeHtml($panel_template_setting_key); ?>;

		if ( key ) {
			var settingsKey = "<?= EscaperHelper::escapeHtml($panel_template_setting_key); ?>";

			/*
			 * If the tag has controls,
			 * and key is an existing control (and not an old one),
			 * and the control has options (select/select2),
			 * and the key is an existing option (and not in a group or an old one).
			 */
			if ( controls && controls[settingsKey] ) {
				var controlSettings = controls[settingsKey];

				if ( controlSettings.options && controlSettings.options[ key ] ) {
					key = controlSettings.options[ key ];
				} else if ( controlSettings.groups ) {
					var label = _.filter( _.pluck( _.pluck( controls.key.groups, 'options' ), key ) );

					if ( label[0] ) {
						key = label[0];
					}
				}
			}

			print( '(' + key + ')' );
		}
		#>
		<?php
    }


    final public function getUniqueName()
    {
        return 'tag-' . $this->getName();
    }


    protected function registerAdvancedSection()
    {
    }


    final protected function initControls()
    {
        ObjectManagerHelper::get(Controls::class)->openStack($this);

        $this->startControlsSection('settings', [
            'label' => __('Settings'),
        ]);

        $this->registerControls();

        $this->endControlsSection();

        // If in fact no controls were registered, empty the stack
        if (1 === count(ObjectManagerHelper::get(Controls::class)->getStacks($this->getUniqueName())['controls'])) {
            ObjectManagerHelper::get(Controls::class)->openStack($this);
        }

        $this->registerAdvancedSection();
    }
}
