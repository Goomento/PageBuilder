<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Schemes;

use Goomento\PageBuilder\Helper\StaticConfig;

/**
 * Class Base
 * @package Goomento\PageBuilder\Builder\Schemes
 */
abstract class Base
{

    /**
     * System schemes.
     *
     * Holds the list of all the system schemes.
     *
     *
     * @var array System schemes.
     */
    private $_system_schemes;

    /**
     * Init system schemes.
     *
     * Initialize the system schemes.
     *
     * @abstract
     */
    abstract protected function _initSystemSchemes();

    /**
     * Get description.
     *
     * Retrieve the scheme description.
     *
     *
     * @return string Scheme description.
     */
    public static function getDescription()
    {
        return '';
    }

    /**
     * Get system schemes.
     *
     * Retrieve the system schemes.
     *
     *
     * @return string System schemes.
     */
    final public function getSystemSchemes()
    {
        if (null === $this->_system_schemes) {
            $this->_system_schemes = $this->_initSystemSchemes();
        }

        return $this->_system_schemes;
    }

    /**
     * Get scheme value.
     *
     * Retrieve the scheme value.
     *
     *
     * @return array Scheme value.
     */
    public function getSchemeValue()
    {
        $scheme_value = StaticConfig::getOption('scheme_' . static::getType());

        if (empty($scheme_value)) {
            $scheme_value = $this->getDefaultScheme();

            StaticConfig::setOption('scheme_' . static::getType(), $scheme_value);
        }

        return $scheme_value;
    }

    /**
     * Save scheme.
     *
     * Update SagoTheme scheme in the database, and update the last updated
     * scheme time.
     *
     *
     * @param array $posted
     */
    public function saveScheme(array $posted)
    {
        $scheme_value = $this->getSchemeValue();
        StaticConfig::setOption('scheme_' . static::getType(),
            array_replace($scheme_value, array_intersect_key($posted, $scheme_value)));
    }

    /**
     * Get scheme.
     *
     * Retrieve the scheme.
     *
     *
     * @return array The scheme.
     */
    public function getScheme()
    {
        $scheme = [];

        $titles = $this->getSchemeTitles();

        foreach ($this->getSchemeValue() as $scheme_key => $scheme_value) {
            $scheme[ $scheme_key ] = [
                'title' => $titles[$scheme_key] ?? '',
                'value' => $scheme_value,
            ];
        }

        return $scheme;
    }

    /**
     * Print scheme template.
     *
     * Used to generate the scheme template on the editor using Underscore JS
     * template.
     *
     */
    final public function printTemplate()
    {
        ?>
		<script type="text/template" id="tmpl-gmt-panel-schemes-<?= static::getType() ?>">
			<div class="gmt-panel-scheme-buttons">
				<div class="gmt-panel-scheme-button-wrapper gmt-panel-scheme-reset">
					<button class="gmt-button">
						<i class="fas fa-undo" aria-hidden="true"></i>
						<?= __('Reset'); ?>
					</button>
				</div>
				<div class="gmt-panel-scheme-button-wrapper gmt-panel-scheme-discard">
					<button class="gmt-button">
						<i class="fas fa-times" aria-hidden="true"></i>
						<?= __('Discard'); ?>
					</button>
				</div>
				<div class="gmt-panel-scheme-button-wrapper gmt-panel-scheme-save">
					<button class="gmt-button gmt-button-success" disabled><?= __('Apply'); ?></button>
				</div>
			</div>
			<?php $this->printTemplateContent(); ?>
		</script>
		<?php
    }
}
