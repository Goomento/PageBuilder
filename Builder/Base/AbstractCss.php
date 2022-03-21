<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Modules\Frontend;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Stylesheet;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

abstract class AbstractCss extends AbstractFile
{

    /**
     * SagoTheme CSS file generated status.
     *
     * The parsing result after generating CSS file.
     */
    const CSS_STATUS_FILE = 'file';

    /**
     * SagoTheme inline CSS status.
     *
     * The parsing result after generating inline CSS.
     */
    const CSS_STATUS_INLINE = 'inline';

    /**
     * SagoTheme CSS empty status.
     *
     * The parsing result when an empty CSS returned.
     */
    const CSS_STATUS_EMPTY = 'empty';

    /**
     * Fonts.
     *
     * Holds the list of fonts.
     *
     *
     * @var array
     */
    protected $fonts = [];

    protected $icons_fonts = [];

    /**
     * Stylesheet object.
     *
     * Holds the CSS file stylesheet instance.
     *
     *
     * @var Stylesheet
     */
    protected $stylesheetObj;

    /**
     * Printed.
     *
     * Holds the list of printed files.
     *
     *
     * @var array
     */
    private static $printed = [];

    /**
     * CSS file constructor.
     *
     * Initializing SagoTheme CSS file.
     *
     */
    public function __construct($file_name)
    {
        parent::__construct($file_name);

        $this->initStylesheet();
    }

    /**
     * Use external file.
     *
     * Whether to use external CSS file of not. When there are new schemes or settings
     * updates.
     *
     *
     * @return bool True if the CSS requires an update, False otherwise.
     */
    protected function useExternalFile()
    {
        return 'external'  === DataHelper::getCssPrintMethod();
    }

    /**
     * Update the CSS file.
     *
     * Delete old CSS, parse the CSS, save the new file and update the database.
     *
     * This method also sets the CSS status to be used later on in the render posses.
     *
     */
    public function update()
    {
        $this->updateFile();

        $meta = $this->getMeta();

        $meta['css_updated_time'] = time();

        $content = $this->getContent();
        $meta['fonts'] = $this->getFonts();

        if (empty($content)) {
            $meta['status'] = self::CSS_STATUS_EMPTY;
            $meta['css'] = '';
        } else {
            $use_external_file = $this->useExternalFile();

            if ($use_external_file) {
                $meta['status'] = self::CSS_STATUS_FILE;
            } else {
                $meta['status'] = self::CSS_STATUS_INLINE;
                $meta['css'] = $content;
            }
        }

        $this->updateMeta($meta);
    }


    public function write()
    {
        if ($this->useExternalFile()) {
            parent::write();
        }
    }

    /**
     * Enqueue CSS.
     *
     * Either enqueue the CSS file in SagoTheme or add inline style.
     *
     * This method is also responsible for loading the fonts.
     *
     */
    public function enqueue()
    {
        $handle_id = $this->getFileHandleId();

        if (isset(self::$printed[ $handle_id ])) {
            return;
        }

        self::$printed[ $handle_id ] = true;

        $meta = $this->getMeta();

        if (self::CSS_STATUS_EMPTY === $meta['status']) {
            return;
        }

        // First time after clear cache and etc.
        if ('' === $meta['status'] || $this->isUpdateRequired()) {
            $this->update();

            $meta = $this->getMeta();
        }

        if (!$this->useExternalFile()) {
            $dep = $this->getInlineDependency();
            if (ThemeHelper::styleIs($dep, 'done')) {
                echo sprintf('<style id="%s">%s</style>', $this->getFileHandleId(), $meta['css']);
            } else {
                ThemeHelper::inlineStyle($dep, $meta['css']);
            }
        } else { // Re-check if it's not empty after CSS update.
            ThemeHelper::enqueueStyle(
                $this->getFileHandleId(),
                $this->getUrl(),
                $this->getEnqueueDependencies(),
                $this->getMeta('css_updated_time')
            );
        }

        /** @var Frontend $frontend */
        $frontend = ObjectManagerHelper::get(Frontend::class);
        // Handle fonts.
        if (!empty($meta['fonts'])) {
            foreach ($meta['fonts'] as $font) {
                $frontend->enqueueFont($font);
            }
        }

        foreach ($this->getFonts() as $defaultFont) {
            $frontend->enqueueFont($defaultFont);
        }

        if (!empty($meta['icons'])) {
            $icons_types = Icons::getIconManagerTabs();
            foreach ($meta['icons'] as $icon_font) {
                if (!isset($icons_types[ $icon_font ])) {
                    continue;
                }
                /** @var Frontend $frontend */
                $frontend = ObjectManagerHelper::get(Frontend::class);
                $frontend->enqueueFont($icon_font);
            }
        }

        $name = $this->getName();

        /**
         * Enqueue CSS file.
         *
         * Fires when CSS file is enqueued on SagoTheme.
         *
         * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
         *
         *
         * @param AbstractCss $this The current CSS file.
         */
        HooksHelper::doAction("pagebuilder/css-file/{$name}/enqueue", $this);
    }

    /**
     * Print CSS.
     *
     * Output the final CSS inside the `<style>` tags and all the frontend fonts in
     * use.
     *
     */
    public function printCss()
    {
        /** @var Frontend $frontend */
        $frontend = ObjectManagerHelper::get(Frontend::class);
        echo '<style>' . $this->getContent() . '</style>'; // XSS ok.
        $frontend->printFontsLinks();
    }

    /**
     * Add control rules.
     *
     * Parse the CSS for all the elements inside any given control.
     *
     * This method recursively renders the CSS for all the selectors in the control.
     *
     *
     * @param array    $control        The controls.
     * @param array    $controls_stack The controls stack.
     * @param callable $value_callback Callback function for the value.
     * @param array    $placeholders   Placeholders.
     * @param array    $replacements   Replacements.
     */
    public function addControlRules(array $control, array $controls_stack, callable $value_callback, array $placeholders, array $replacements)
    {
        $value = call_user_func($value_callback, $control);

        if (null === $value || empty($control['selectors'])) {
            return;
        }

        foreach ($control['selectors'] as $selector => $css_property) {
            try {
                $output_css_property = preg_replace_callback('/\{\{(?:([^.}]+)\.)?([^}| ]*)(?: *\|\| *(?:([^.}]+)\.)?([^}| ]*) *)*}}/', function ($matches) use ($control, $value_callback, $controls_stack, $value, $css_property) {
                    $external_control_missing = $matches[1] && ! isset($controls_stack[ $matches[1] ]);

                    $parsed_value = '';

                    if (!$external_control_missing) {
                        $parsed_value = $this->parsePropertyPlaceholder($control, $value, $controls_stack, $value_callback, $matches[2], $matches[1]);
                    }

                    if ('' === $parsed_value) {
                        if (isset($matches[4])) {
                            $parsed_value = $matches[4];

                            $is_string_value = preg_match('/^([\'"])(.*)\1$/', $parsed_value, $string_matches);

                            if ($is_string_value) {
                                $parsed_value = $string_matches[2];
                            } elseif (! is_numeric($parsed_value)) {
                                if ($matches[3] && ! isset($controls_stack[ $matches[3] ])) {
                                    return '';
                                }

                                $parsed_value = $this->parsePropertyPlaceholder($control, $value, $controls_stack, $value_callback, $matches[4], $matches[3]);
                            }
                        }

                        if ('' === $parsed_value) {
                            if ($external_control_missing) {
                                return '';
                            }

                            throw new \Exception();
                        }
                    }

                    return $parsed_value;
                }, $css_property);
            } catch (\Exception $e) {
                return;
            }

            if (!$output_css_property) {
                continue;
            }

            $device_pattern = '/^(?:\([^\)]+\)){1,2}/';

            preg_match($device_pattern, $selector, $device_rules);

            $query = [];

            if ($device_rules) {
                $selector = preg_replace($device_pattern, '', $selector);

                preg_match_all('/\(([^\)]+)\)/', $device_rules[0], $pure_device_rules);

                $pure_device_rules = $pure_device_rules[1];

                foreach ($pure_device_rules as $device_rule) {
                    if (ControlsStack::RESPONSIVE_DESKTOP === $device_rule) {
                        continue;
                    }

                    $device = preg_replace('/\+$/', '', $device_rule);

                    $endpoint = $device === $device_rule ? 'max' : 'min';

                    $query[ $endpoint ] = $device;
                }
            }

            $parsed_selector = str_replace($placeholders, $replacements, $selector);

            if (!$query && ! empty($control['responsive'])) {
                $query = array_intersect_key($control['responsive'], array_flip([ 'min', 'max' ]));

                if (!empty($query['max']) && ControlsStack::RESPONSIVE_DESKTOP === $query['max']) {
                    unset($query['max']);
                }
            }

            $this->stylesheetObj->addRules($parsed_selector, $output_css_property, $query);
        }
    }

    /**
     * @param array    $control
     * @param mixed    $value
     * @param array    $controls_stack
     * @param callable $value_callback
     * @param string   $placeholder
     * @param string   $parser_control_name
     *
     * @return string
     */
    public function parsePropertyPlaceholder(array $control, $value, array $controls_stack, $value_callback, $placeholder, $parser_control_name = null)
    {
        if ($parser_control_name) {
            $control = $controls_stack[ $parser_control_name ];

            $value = call_user_func($value_callback, $control);
        }

        if (Controls::FONT === $control['type']) {
            $this->setFont($value);
        }

        /** @var Controls $controlManager */
        $controlManager = ObjectManagerHelper::get(Controls::class);
        $control_obj = $controlManager->getControl($control['type']);

        return (string) $control_obj->getStyleValue($placeholder, $value, $control);
    }

    /**
     * Get the fonts.
     *
     * Retrieve the list of fonts.
     *
     *
     * @return array Fonts.
     */
    public function getFonts()
    {
        return $this->fonts;
    }

    /**
     * @param $font_name
     * @return $this
     */
    public function setFont($font_name)
    {
        if (!in_array($font_name, $this->fonts)) {
            $this->fonts[] = $font_name;
        }

        return $this;
    }

    /**
     * Get stylesheet.
     *
     * Retrieve the CSS file stylesheet instance.
     *
     *
     * @return Stylesheet The stylesheet object.
     */
    public function getStylesheet()
    {
        return $this->stylesheetObj;
    }

    /**
     * Add controls stack style rules.
     *
     * Parse the CSS for all the elements inside any given controls stack.
     *
     * This method recursively renders the CSS for all the child elements in the stack.
     *
     *
     * @param ControlsStack $controls_stack The controls stack.
     * @param array $controls Controls array.
     * @param array $values Values array.
     * @param array $placeholders Placeholders.
     * @param array $replacements Replacements.
     * @param array|null $all_controls All controls.
     */
    public function addControlsStackStyleRules(ControlsStack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, array $all_controls = null)
    {
        if (!$all_controls) {
            $all_controls = $controls_stack->getControls();
        }

        $parsed_dynamic_settings = $controls_stack->parseDynamicSettings($values, $controls);

        foreach ($controls as $control) {
            if (!empty($control['style_fields'])) {
                $this->addRepeaterControlStyleRules($controls_stack, $control, $values[ $control['name'] ], $placeholders, $replacements);
            }

            if (!empty($control[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ])) {
                $this->addDynamicControlStyleRules($control, $control[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ]);
            }

            if (!empty($parsed_dynamic_settings[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ])) {
                // Dynamic CSS should not be added to the CSS files.
                // Instead it's handled by \Goomento\PageBuilder\Core\DynamicTags\Dynamic_CSS
                // and printed in a style tag.
                unset($parsed_dynamic_settings[ $control['name'] ]);
                continue;
            }

            if (empty($control['selectors'])) {
                continue;
            }

            $this->addControlStyleRules($control, $parsed_dynamic_settings, $all_controls, $placeholders, $replacements);
        }
    }

    /**
     * Get file handle ID.
     *
     * Retrieve the file handle ID.
     *
     * @abstract
     *
     * @return string CSS file handle ID.
     */
    abstract protected function getFileHandleId();

    /**
     * Render CSS.
     *
     * Parse the CSS.
     *
     * @abstract
     */
    abstract protected function renderCss();

    /**
     * @return array|string[]
     */
    protected function getDefaultMeta()
    {
        return array_merge(parent::getDefaultMeta(), [
            'fonts' => array_unique($this->fonts),
            'status' => '',
        ]);
    }

    /**
     * Get enqueue dependencies.
     *
     * Retrieve the name of the stylesheet used by `\Goomento\PageBuilder\Helper\Theme::enqueueStyle()`.
     *
     *
     * @return array Name of the stylesheet.
     */
    protected function getEnqueueDependencies()
    {
        return [];
    }

    /**
     * Get inline dependency.
     *
     *
     * @return string Name of the stylesheet.
     */
    protected function getInlineDependency()
    {
        return '';
    }

    /**
     * @return bool
     */
    protected function isUpdateRequired()
    {
        $isAdmin = StateHelper::isAdminhtml();
        if ($isAdmin) {
            return true;
        }
        $time = $this->getMeta('css_updated_time');
        if (!$time) {
            return true;
        }
        $css_updated_time = ConfigHelper::getValue('css_updated_time');
        if ($css_updated_time && $css_updated_time > $time) {
            return true;
        }

        return false;
    }

    /**
     * Parse CSS.
     *
     * Parsing the CSS file.
     *
     */
    protected function parseContent()
    {
        $this->renderCss();

        $name = $this->getName();

        /**
         * Parse CSS file.
         *
         * Fires when CSS file is parsed on SagoTheme.
         *
         * The dynamic portion of the hook name, `$name`, refers to the CSS file name.
         *
         *
         * @param AbstractCss $this The current CSS file.
         */
        HooksHelper::doAction("pagebuilder/css-file/{$name}/parse", $this);

        return $this->stylesheetObj->__toString();
    }

    /**
     * Add control style rules.
     *
     * Register new style rules for the control.
     *
     *
     * @param array $control      The control.
     * @param array $values       Values array.
     * @param array $controls     The controls stack.
     * @param array $placeholders Placeholders.
     * @param array $replacements Replacements.
     */
    protected function addControlStyleRules(array $control, array $values, array $controls, array $placeholders, array $replacements)
    {
        $this->addControlRules(
            $control,
            $controls,
            function ($control) use ($values) {
                return $this->getStyleControlValue($control, $values);
            },
            $placeholders,
            $replacements
        );
    }

    /**
     * Get style control value.
     *
     * Retrieve the value of the style control for any give control and values.
     *
     * It will retrieve the control name and return the style value.
     *
     *
     * @param array $control The control.
     * @param array $values  Values array.
     *
     * @return mixed Style control value.
     */
    private function getStyleControlValue(array $control, array $values)
    {
        $value = $values[ $control['name'] ];

        if (isset($control['selectors_dictionary'][ $value ])) {
            $value = $control['selectors_dictionary'][ $value ];
        }

        if (! is_numeric($value) && ! is_float($value) && empty($value)) {
            return null;
        }

        return $value;
    }

    /**
     * Init stylesheet.
     *
     * Initialize CSS file stylesheet by creating a new `Stylesheet` object and register new
     * breakpoints for the stylesheet.
     *
     */
    private function initStylesheet()
    {
        /** @var Stylesheet stylesheet_obj */
        $this->stylesheetObj = ObjectManagerHelper::get(Stylesheet::class);

        $breakpoints = Configuration::DEFAULT_BREAKPOINTS;

        $this->stylesheetObj
            ->addDevice('mobile', 0)
            ->addDevice('tablet', $breakpoints['md'])
            ->addDevice('desktop', $breakpoints['lg']);
    }

    /**
     * Add repeater control style rules.
     *
     * Register new style rules for the repeater control.
     *
     *
     * @param ControlsStack $controls_stack The control stack.
     * @param array $repeater_control The repeater control.
     * @param array $repeater_values Repeater values array.
     * @param array $placeholders Placeholders.
     * @param array $replacements Replacements.
     */
    protected function addRepeaterControlStyleRules(ControlsStack $controls_stack, array $repeater_control, array $repeater_values, array $placeholders, array $replacements)
    {
        $placeholders = array_merge($placeholders, [ '{{CURRENT_ITEM}}' ]);

        foreach ($repeater_control['style_fields'] as $index => $item) {
            $this->addControlsStackStyleRules(
                $controls_stack,
                $item,
                $repeater_values[ $index ],
                $placeholders,
                array_merge($replacements, [ '.gmt-repeater-item-' . $repeater_values[ $index ]['_id'] ]),
                $repeater_control['fields']
            );
        }
    }

    /**
     * Add dynamic control style rules.
     *
     * Register new style rules for the dynamic control.
     *
     *
     * @param array  $control The control.
     * @param string $value   The value.
     */
    protected function addDynamicControlStyleRules(array $control, $value)
    {
        /** @var Tags $tagsManager */
        $tagsManager = ObjectManagerHelper::get(Tags::class);

        $tagsManager->parseTagsText($value, $control, function ($id, $name, $settings) use ($tagsManager) {
            $tag = $tagsManager->createTag($id, $name, $settings);

            if (!$tag instanceof AbstractTag) {
                return;
            }

            $this->addControlsStackStyleRules($tag, $tag->getStyleControls(), $tag->getActiveSettings(), [ '{{WRAPPER}}' ], [ '#gmt-tag-' . $id ]);
        });
    }
}
